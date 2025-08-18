<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollRun;
use App\Models\PayrollRunDetail;
use App\Models\EmployeeLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeePortalController extends Controller
{
    public function login(Request $request, $token)
    {
        $employee = Employee::where('portal_token', $token)
            ->where('portal_token_expires_at', '>', now())
            ->first();

        if (!$employee) {
            return view('payroll.portal.invalid-token');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'employee_id' => 'required',
                'date_of_birth' => 'required|date',
            ]);

            if ($employee->employee_id === $request->employee_id &&
                $employee->date_of_birth &&
                $employee->date_of_birth->format('Y-m-d') === $request->date_of_birth) {

                session(['employee_portal_id' => $employee->id]);
                return redirect()->route('payroll.portal.dashboard', $token);
            }

            return back()->withErrors(['Invalid credentials']);
        }

        return view('payroll.portal.login', compact('employee', 'token'));
    }

    public function dashboard($token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', $token);
        }

        // Get recent payslips
        $recentPayslips = PayrollRunDetail::whereHas('payrollRun', function($query) {
                $query->where('status', 'approved');
            })
            ->where('employee_id', $employee->id)
            ->with('payrollRun.payrollPeriod')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get current loans
        $activeLoans = EmployeeLoan::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->get();

        // Calculate year-to-date stats
        $ytdStats = $this->calculateYearToDateStats($employee);

        return view('payroll.portal.dashboard', compact(
            'employee',
            'token',
            'recentPayslips',
            'activeLoans',
            'ytdStats'
        ));
    }

    public function payslips($token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', $token);
        }

        $payslips = PayrollRunDetail::whereHas('payrollRun', function($query) {
                $query->where('status', 'approved');
            })
            ->where('employee_id', $employee->id)
            ->with(['payrollRun.payrollPeriod', 'employeeSalaryComponents.salaryComponent'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('payroll.portal.payslips', compact('employee', 'token', 'payslips'));
    }

    public function payslip($token, PayrollRunDetail $payslip)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee || $payslip->employee_id !== $employee->id) {
            return redirect()->route('payroll.portal.login', $token);
        }

        $payslip->load([
            'payrollRun.payrollPeriod',
            'employeeSalaryComponents.salaryComponent'
        ]);

        return view('payroll.portal.payslip', compact('employee', 'token', 'payslip'));
    }

    public function downloadPayslip($token, PayrollRunDetail $payslip)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee || $payslip->employee_id !== $employee->id) {
            abort(403);
        }

        $payslip->load([
            'payrollRun.payrollPeriod',
            'employeeSalaryComponents.salaryComponent'
        ]);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('payroll.portal.payslip-pdf', compact('employee', 'payslip'));

        $filename = "payslip_{$employee->employee_id}_{$payslip->payrollRun->payrollPeriod->name}.pdf";

        return $pdf->download($filename);
    }

    public function profile($token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', $token);
        }

        return view('payroll.portal.profile', compact('employee', 'token'));
    }

    public function updateProfile(Request $request, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', $token);
        }

        $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:20',
            'account_holder_name' => 'nullable|string|max:100',
        ]);

        $employee->update($request->only([
            'phone',
            'address',
            'emergency_contact_name',
            'emergency_contact_phone',
            'bank_name',
            'account_number',
            'account_holder_name',
        ]));

        return back()->with('success', 'Profile updated successfully');
    }

    public function loans($token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', $token);
        }

        $loans = EmployeeLoan::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payroll.portal.loans', compact('employee', 'token', 'loans'));
    }

    public function taxCertificate($token, $year = null)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', $token);
        }

        $year = $year ?: date('Y');

        $taxData = PayrollRunDetail::whereHas('payrollRun', function($query) {
                $query->where('status', 'approved');
            })
            ->where('employee_id', $employee->id)
            ->whereYear('created_at', $year)
            ->selectRaw('
                SUM(gross_pay) as total_gross,
                SUM(tax_amount) as total_tax,
                SUM(pension_amount) as total_pension,
                SUM(net_pay) as total_net
            ')
            ->first();

        return view('payroll.portal.tax-certificate', compact(
            'employee',
            'token',
            'year',
            'taxData'
        ));
    }

    public function downloadTaxCertificate($token, $year = null)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            abort(403);
        }

        $year = $year ?: date('Y');

        $taxData = PayrollRunDetail::whereHas('payrollRun', function($query) {
                $query->where('status', 'approved');
            })
            ->where('employee_id', $employee->id)
            ->whereYear('created_at', $year)
            ->selectRaw('
                SUM(gross_pay) as total_gross,
                SUM(tax_amount) as total_tax,
                SUM(pension_amount) as total_pension,
                SUM(net_pay) as total_net
            ')
            ->first();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('payroll.portal.tax-certificate-pdf', compact(
            'employee',
            'year',
            'taxData'
        ));

        $filename = "tax_certificate_{$employee->employee_id}_{$year}.pdf";

        return $pdf->download($filename);
    }

    public function logout($token)
    {
        session()->forget('employee_portal_id');
        return redirect()->route('payroll.portal.login', $token)
            ->with('message', 'You have been logged out successfully');
    }

    protected function getAuthenticatedEmployee($token)
    {
        $employeeId = session('employee_portal_id');
        if (!$employeeId) {
            return null;
        }

        return Employee::where('id', $employeeId)
            ->where('portal_token', $token)
            ->where('portal_token_expires_at', '>', now())
            ->with('department')
            ->first();
    }

    protected function calculateYearToDateStats($employee)
    {
        $year = date('Y');

        $stats = PayrollRunDetail::whereHas('payrollRun', function($query) {
                $query->where('status', 'approved');
            })
            ->where('employee_id', $employee->id)
            ->whereYear('created_at', $year)
            ->selectRaw('
                SUM(gross_pay) as ytd_gross,
                SUM(tax_amount) as ytd_tax,
                SUM(pension_amount) as ytd_pension,
                SUM(net_pay) as ytd_net,
                COUNT(*) as payroll_count
            ')
            ->first();

        return [
            'ytd_gross' => $stats->ytd_gross ?? 0,
            'ytd_tax' => $stats->ytd_tax ?? 0,
            'ytd_pension' => $stats->ytd_pension ?? 0,
            'ytd_net' => $stats->ytd_net ?? 0,
            'payroll_count' => $stats->payroll_count ?? 0,
        ];
    }
}

<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollRun;
use App\Models\PayrollRunDetail;
use App\Models\EmployeeLoan;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmployeePortalController extends Controller
{
    public function login(Request $request, $tenant, $token)
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

            if ($employee->employee_number === $request->employee_id &&
                $employee->date_of_birth &&
                $employee->date_of_birth->format('Y-m-d') === $request->date_of_birth) {

                session(['employee_portal_id' => $employee->id]);
                return redirect()->route('payroll.portal.dashboard', ['tenant' => $tenant, 'token' => $token]);
            }

            return back()->withErrors(['Invalid credentials']);
        }

        return view('payroll.portal.login', compact('employee', 'tenant', 'token'));
    }

    public function dashboard($tenant, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
        }

        // Get recent payslips
        $recentPayslips = PayrollRun::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', function($query) {
                $query->where('status', 'approved');
            })
            ->with('payrollPeriod')
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
            'tenant',
            'token',
            'recentPayslips',
            'activeLoans',
            'ytdStats'
        ));
    }

    public function payslips($tenant, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
        }

        $payslips = PayrollRun::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', function($query) {
                $query->where('status', 'approved');
            })
            ->with(['payrollPeriod', 'details.salaryComponent'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('payroll.portal.payslips', compact('employee', 'tenant', 'token', 'payslips'));
    }

    public function payslip($tenant, $token, PayrollRun $payslip)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee || $payslip->employee_id !== $employee->id) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
        }

        $payslip->load([
            'payrollPeriod',
            'details.salaryComponent'
        ]);

        return view('payroll.portal.payslip', compact('employee', 'tenant', 'token', 'payslip'));
    }

    public function downloadPayslip($tenant, $token, PayrollRun $payslip)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee || $payslip->employee_id !== $employee->id) {
            abort(403);
        }

        $payslip->load([
            'payrollPeriod',
            'details.salaryComponent'
        ]);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('payroll.portal.payslip-pdf', compact('employee', 'payslip'));

        $filename = "payslip_{$employee->employee_number}_{$payslip->payrollPeriod->name}.pdf";

        return $pdf->download($filename);
    }

    public function profile($tenant, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
        }

        return view('payroll.portal.profile', compact('employee', 'tenant', 'token'));
    }

    public function updateProfile(Request $request, $tenant, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
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

    public function loans($tenant, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
        }

        $loans = EmployeeLoan::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payroll.portal.loans', compact('employee', 'tenant', 'token', 'loans'));
    }

    public function taxCertificate($tenant, $token, $year = null)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
        }

        $year = $year ?: date('Y');

        $taxData = PayrollRun::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', function($query) {
                $query->where('status', 'approved');
            })
            ->whereYear('created_at', $year)
            ->selectRaw('
                SUM(gross_salary) as total_gross,
                SUM(monthly_tax) as total_tax,
                SUM(0) as total_pension,
                SUM(net_salary) as total_net
            ')
            ->first();

        return view('payroll.portal.tax-certificate', compact(
            'employee',
            'tenant',
            'token',
            'year',
            'taxData'
        ));
    }

    public function downloadTaxCertificate($tenant, $token, $year = null)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            abort(403);
        }

        $year = $year ?: date('Y');

        $taxData = PayrollRun::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', function($query) {
                $query->where('status', 'approved');
            })
            ->whereYear('created_at', $year)
            ->selectRaw('
                SUM(gross_salary) as total_gross,
                SUM(monthly_tax) as total_tax,
                SUM(0) as total_pension,
                SUM(net_salary) as total_net
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

    public function logout($tenant, $token)
    {
        session()->forget('employee_portal_id');
        return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token])
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

        $stats = PayrollRun::where('employee_id', $employee->id)
            ->whereHas('payrollPeriod', function($query) {
                $query->where('status', 'approved');
            })
            ->whereYear('created_at', $year)
            ->selectRaw('
                SUM(gross_salary) as ytd_gross,
                SUM(monthly_tax) as ytd_tax,
                SUM(0) as ytd_pension,
                SUM(net_salary) as ytd_net,
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

    /**
     * Show attendance page with QR scanner
     */
    public function attendance($tenant, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return redirect()->route('payroll.portal.login', ['tenant' => $tenant, 'token' => $token]);
        }

        // Get today's attendance
        $todayAttendance = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', now())
            ->first();

        // Get recent attendance records (last 7 days)
        $recentAttendance = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', '>=', now()->subDays(7))
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('payroll.portal.attendance', compact(
            'employee',
            'tenant',
            'token',
            'todayAttendance',
            'recentAttendance'
        ));
    }

    /**
     * Process scanned QR code for attendance
     */
    public function scanAttendanceQR(Request $request, $tenant, $token)
    {
        $employee = $this->getAuthenticatedEmployee($token);
        if (!$employee) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            // Decrypt QR payload
            $payload = decrypt($request->qr_data);

            // Validate payload structure
            if (!isset($payload['tenant_id'], $payload['date'], $payload['type'], $payload['expires_at'])) {
                return response()->json(['error' => 'Invalid QR code format'], 400);
            }

            // Verify tenant matches
            if ($payload['tenant_id'] !== $employee->tenant_id) {
                return response()->json(['error' => 'QR code is not valid for your organization'], 403);
            }

            // Check if QR code is expired
            if (Carbon::parse($payload['expires_at'])->isPast()) {
                return response()->json(['error' => 'QR code has expired'], 400);
            }

            // Verify the date matches today
            if ($payload['date'] !== now()->format('Y-m-d')) {
                return response()->json(['error' => 'QR code is for a different date'], 400);
            }

            // Get or create today's attendance record
            $attendance = AttendanceRecord::firstOrCreate(
                [
                    'tenant_id' => $employee->tenant_id,
                    'employee_id' => $employee->id,
                    'attendance_date' => now()->toDateString(),
                ],
                [
                    'status' => 'absent',
                ]
            );

            $type = $payload['type'];

            // Process based on type (clock_in or clock_out)
            if ($type === 'clock_in') {
                if ($attendance->clock_in) {
                    return response()->json([
                        'error' => 'Already clocked in',
                        'clock_in_time' => $attendance->clock_in->format('h:i A'),
                    ], 400);
                }

                $attendance->clockIn(
                    $request->ip(),
                    $request->header('User-Agent'),
                    'Scanned via QR Code'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Clocked in successfully',
                    'clock_in_time' => $attendance->clock_in->format('h:i A'),
                    'status' => $attendance->status,
                    'late_minutes' => $attendance->late_minutes,
                ]);

            } elseif ($type === 'clock_out') {
                if (!$attendance->clock_in) {
                    return response()->json([
                        'error' => 'Must clock in before clocking out',
                    ], 400);
                }

                if ($attendance->clock_out) {
                    return response()->json([
                        'error' => 'Already clocked out',
                        'clock_out_time' => $attendance->clock_out->format('h:i A'),
                    ], 400);
                }

                $attendance->clockOut(
                    $request->ip(),
                    $request->header('User-Agent'),
                    'Scanned via QR Code'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Clocked out successfully',
                    'clock_out_time' => $attendance->clock_out->format('h:i A'),
                    'work_hours' => $attendance->calculateWorkHours(),
                    'overtime_hours' => $attendance->calculateOvertimeHours(),
                ]);
            }

            return response()->json(['error' => 'Invalid QR code type'], 400);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['error' => 'Invalid or corrupted QR code'], 400);
        } catch (\Exception $e) {
            Log::error('QR Scan Error: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An error occurred processing your scan: ' . $e->getMessage()], 500);
        }
    }
}


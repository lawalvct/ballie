<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Employee;
use App\Models\Department;
use App\Models\EmployeeAnnouncement;
use App\Models\AnnouncementRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Display announcements list
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = EmployeeAnnouncement::where('tenant_id', $tenant->id)
            ->with(['creator', 'recipients']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->count(),
            'sent' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->where('status', 'sent')->count(),
            'scheduled' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->where('status', 'scheduled')->count(),
            'draft' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
        ];

        return view('tenant.payroll.announcements.index', compact('tenant', 'announcements', 'stats'));
    }

    /**
     * Show create announcement form
     */
    public function create(Tenant $tenant)
    {
        $departments = Department::where('tenant_id', $tenant->id)
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('department')
            ->orderBy('first_name')
            ->get();

        return view('tenant.payroll.announcements.create', compact('tenant', 'departments', 'employees'));
    }

    /**
     * Store new announcement
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'delivery_method' => 'required|in:email,sms,both',
            'recipient_type' => 'required|in:all,department,selected',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'requires_acknowledgment' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:now',
            'send_now' => 'nullable|boolean',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        try {
            DB::beginTransaction();

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('announcements', 'public');
            }

            // Create announcement
            $announcement = EmployeeAnnouncement::create([
                'tenant_id' => $tenant->id,
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'message' => $validated['message'],
                'priority' => $validated['priority'],
                'delivery_method' => $validated['delivery_method'],
                'recipient_type' => $validated['recipient_type'],
                'department_ids' => $validated['department_ids'] ?? null,
                'employee_ids' => $validated['employee_ids'] ?? null,
                'requires_acknowledgment' => $validated['requires_acknowledgment'] ?? false,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'expires_at' => $validated['expires_at'] ?? null,
                'attachment_path' => $attachmentPath,
                'status' => $request->boolean('send_now') ? 'sending' :
                           ($validated['scheduled_at'] ? 'scheduled' : 'draft'),
            ]);

            // Get targeted employees
            $employees = $announcement->getTargetedEmployees();
            $announcement->update(['total_recipients' => $employees->count()]);

            // Create recipient records
            foreach ($employees as $employee) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->id,
                    'employee_id' => $employee->id,
                ]);
            }

            DB::commit();

            // Send immediately if requested
            if ($request->boolean('send_now')) {
                $this->sendAnnouncement($announcement);
            }

            return redirect()
                ->route('tenant.payroll.announcements.show', [$tenant, $announcement])
                ->with('success', 'Announcement created successfully.' .
                    ($request->boolean('send_now') ? ' Sending in progress...' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating announcement: ' . $e->getMessage());
        }
    }

    /**
     * Show announcement details
     */
    public function show(Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        $announcement->load(['creator', 'recipients.employee.department']);

        return view('tenant.payroll.announcements.show', compact('tenant', 'announcement'));
    }

    /**
     * Show edit form
     */
    public function edit(Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if (!$announcement->canBeEdited()) {
            return redirect()
                ->route('tenant.payroll.announcements.show', [$tenant, $announcement])
                ->with('error', 'This announcement cannot be edited.');
        }

        $departments = Department::where('tenant_id', $tenant->id)
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('department')
            ->orderBy('first_name')
            ->get();

        return view('tenant.payroll.announcements.edit', compact('tenant', 'announcement', 'departments', 'employees'));
    }

    /**
     * Update announcement
     */
    public function update(Request $request, Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if (!$announcement->canBeEdited()) {
            return redirect()
                ->route('tenant.payroll.announcements.show', [$tenant, $announcement])
                ->with('error', 'This announcement cannot be edited.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'delivery_method' => 'required|in:email,sms,both',
            'recipient_type' => 'required|in:all,department,selected',
            'department_ids' => 'nullable|array',
            'employee_ids' => 'nullable|array',
            'requires_acknowledgment' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:now',
        ]);

        try {
            DB::beginTransaction();

            $announcement->update($validated);

            // Recalculate recipients
            $announcement->recipients()->delete();
            $employees = $announcement->getTargetedEmployees();
            $announcement->update(['total_recipients' => $employees->count()]);

            foreach ($employees as $employee) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->id,
                    'employee_id' => $employee->id,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('tenant.payroll.announcements.show', [$tenant, $announcement])
                ->with('success', 'Announcement updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating announcement: ' . $e->getMessage());
        }
    }

    /**
     * Delete announcement
     */
    public function destroy(Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if (!$announcement->canBeDeleted()) {
            return redirect()
                ->back()
                ->with('error', 'This announcement cannot be deleted.');
        }

        try {
            // Delete attachment if exists
            if ($announcement->attachment_path) {
                Storage::disk('public')->delete($announcement->attachment_path);
            }

            $announcement->delete();

            return redirect()
                ->route('tenant.payroll.announcements.index', $tenant)
                ->with('success', 'Announcement deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting announcement: ' . $e->getMessage());
        }
    }

    /**
     * Send announcement now
     */
    public function send(Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if (!$announcement->canBeSent()) {
            return redirect()
                ->back()
                ->with('error', 'This announcement cannot be sent.');
        }

        try {
            $this->sendAnnouncement($announcement);

            return redirect()
                ->route('tenant.payroll.announcements.show', [$tenant, $announcement])
                ->with('success', 'Announcement is being sent...');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error sending announcement: ' . $e->getMessage());
        }
    }

    /**
     * Send announcement to recipients
     */
    protected function sendAnnouncement(EmployeeAnnouncement $announcement)
    {
        try {
            $announcement->markAsSending();

            $recipients = $announcement->recipients()->with('employee')->get();
            $emailCount = 0;
            $smsCount = 0;
            $failedCount = 0;

            foreach ($recipients as $recipient) {
                $employee = $recipient->employee;

                // Send email
                if (in_array($announcement->delivery_method, ['email', 'both']) && $employee->email) {
                    try {
                        // TODO: Implement email sending using Mail facade
                        // Mail::to($employee->email)->send(new AnnouncementMail($announcement, $employee));
                        $recipient->markEmailSent();
                        $emailCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                    }
                }

                // Send SMS
                if (in_array($announcement->delivery_method, ['sms', 'both']) && $employee->phone) {
                    try {
                        // TODO: Implement SMS sending (Twilio, Nexmo, etc.)
                        $recipient->markSmsSent();
                        $smsCount++;
                    } catch (\Exception $e) {
                        $failedCount++;
                    }
                }
            }

            // Update announcement
            $announcement->update([
                'status' => 'sent',
                'sent_at' => now(),
                'email_sent_count' => $emailCount,
                'sms_sent_count' => $smsCount,
                'failed_count' => $failedCount,
            ]);

        } catch (\Exception $e) {
            $announcement->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Get recipients for preview
     */
    public function previewRecipients(Request $request, Tenant $tenant)
    {
        $recipientType = $request->recipient_type;
        $departmentIds = $request->department_ids;
        $employeeIds = $request->employee_ids;

        $query = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('department');

        if ($recipientType === 'department' && $departmentIds) {
            $query->whereIn('department_id', $departmentIds);
        } elseif ($recipientType === 'selected' && $employeeIds) {
            $query->whereIn('id', $employeeIds);
        }

        $employees = $query->get()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'department' => $employee->department->name ?? 'N/A',
            ];
        });

        return response()->json([
            'count' => $employees->count(),
            'employees' => $employees
        ]);
    }
}

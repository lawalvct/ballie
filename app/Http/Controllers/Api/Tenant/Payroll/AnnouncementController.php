<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementRecipient;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeAnnouncement;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * List announcements with stats.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = EmployeeAnnouncement::where('tenant_id', $tenant->id)
            ->with(['creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $announcements = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $announcements->getCollection()->transform(function (EmployeeAnnouncement $announcement) {
            return $this->formatAnnouncement($announcement);
        });

        $stats = [
            'total' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->count(),
            'sent' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->where('status', 'sent')->count(),
            'scheduled' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->where('status', 'scheduled')->count(),
            'draft' => EmployeeAnnouncement::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Announcements retrieved successfully',
            'data' => [
                'stats' => $stats,
                'records' => $announcements,
            ],
        ]);
    }

    /**
     * Create announcement.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('announcements', 'public');
            }

            $announcement = EmployeeAnnouncement::create([
                'tenant_id' => $tenant->id,
                'created_by' => Auth::id(),
                'title' => $request->get('title'),
                'message' => $request->get('message'),
                'priority' => $request->get('priority'),
                'delivery_method' => $request->get('delivery_method'),
                'recipient_type' => $request->get('recipient_type'),
                'department_ids' => $request->get('department_ids'),
                'employee_ids' => $request->get('employee_ids'),
                'requires_acknowledgment' => (bool) $request->get('requires_acknowledgment', false),
                'scheduled_at' => $request->get('scheduled_at'),
                'expires_at' => $request->get('expires_at'),
                'attachment_path' => $attachmentPath,
                'status' => $request->boolean('send_now') ? 'sending' : ($request->filled('scheduled_at') ? 'scheduled' : 'draft'),
            ]);

            $employees = $announcement->getTargetedEmployees();
            $announcement->update(['total_recipients' => $employees->count()]);

            foreach ($employees as $employee) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->id,
                    'employee_id' => $employee->id,
                ]);
            }

            DB::commit();

            if ($request->boolean('send_now')) {
                $this->sendAnnouncement($announcement);
            }

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully',
                'data' => [
                    'announcement' => $this->formatAnnouncement($announcement->fresh(['creator'])),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create announcement',
            ], 500);
        }
    }

    /**
     * Show announcement details (with recipients).
     */
    public function show(Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if ($announcement->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found',
            ], 404);
        }

        $announcement->load(['creator', 'recipients.employee.department']);

        return response()->json([
            'success' => true,
            'message' => 'Announcement retrieved successfully',
            'data' => [
                'announcement' => $this->formatAnnouncement($announcement),
                'recipients' => $announcement->recipients->map(fn (AnnouncementRecipient $recipient) => $this->formatRecipient($recipient)),
            ],
        ]);
    }

    /**
     * Update announcement.
     */
    public function update(Request $request, Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if ($announcement->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found',
            ], 404);
        }

        if (!$announcement->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement cannot be edited',
            ], 409);
        }

        $validator = Validator::make($request->all(), $this->rules(false));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $announcement->update([
                'title' => $request->get('title'),
                'message' => $request->get('message'),
                'priority' => $request->get('priority'),
                'delivery_method' => $request->get('delivery_method'),
                'recipient_type' => $request->get('recipient_type'),
                'department_ids' => $request->get('department_ids'),
                'employee_ids' => $request->get('employee_ids'),
                'requires_acknowledgment' => (bool) $request->get('requires_acknowledgment', false),
                'scheduled_at' => $request->get('scheduled_at'),
                'expires_at' => $request->get('expires_at'),
            ]);

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

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully',
                'data' => [
                    'announcement' => $this->formatAnnouncement($announcement->fresh(['creator'])),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update announcement',
            ], 500);
        }
    }

    /**
     * Delete announcement.
     */
    public function destroy(Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if ($announcement->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found',
            ], 404);
        }

        if (!$announcement->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement cannot be deleted',
            ], 409);
        }

        if ($announcement->attachment_path) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }

        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully',
        ]);
    }

    /**
     * Send announcement now.
     */
    public function send(Tenant $tenant, EmployeeAnnouncement $announcement)
    {
        if ($announcement->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found',
            ], 404);
        }

        if (!$announcement->canBeSent()) {
            return response()->json([
                'success' => false,
                'message' => 'Announcement cannot be sent',
            ], 409);
        }

        try {
            $this->sendAnnouncement($announcement);

            return response()->json([
                'success' => true,
                'message' => 'Announcement sending started',
                'data' => [
                    'announcement' => $this->formatAnnouncement($announcement->fresh(['creator'])),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send announcement',
            ], 500);
        }
    }

    /**
     * Preview recipients count and list.
     */
    public function previewRecipients(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'recipient_type' => 'required|in:all,department,selected',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('department');

        $recipientType = $request->get('recipient_type');
        if ($recipientType === 'department' && $request->filled('department_ids')) {
            $query->whereIn('department_id', $request->get('department_ids'));
        } elseif ($recipientType === 'selected' && $request->filled('employee_ids')) {
            $query->whereIn('id', $request->get('employee_ids'));
        }

        $employees = $query->get()->map(function (Employee $employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'department' => $employee->department?->name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Recipients preview retrieved successfully',
            'data' => [
                'count' => $employees->count(),
                'employees' => $employees,
            ],
        ]);
    }

    private function sendAnnouncement(EmployeeAnnouncement $announcement): void
    {
        $announcement->markAsSending();

        $recipients = $announcement->recipients()->with('employee')->get();
        $emailCount = 0;
        $smsCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            $employee = $recipient->employee;

            if (in_array($announcement->delivery_method, ['email', 'both']) && $employee?->email) {
                try {
                    $recipient->markEmailSent();
                    $emailCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }

            if (in_array($announcement->delivery_method, ['sms', 'both']) && $employee?->phone) {
                try {
                    $recipient->markSmsSent();
                    $smsCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }
        }

        $announcement->update([
            'status' => 'sent',
            'sent_at' => now(),
            'email_sent_count' => $emailCount,
            'sms_sent_count' => $smsCount,
            'failed_count' => $failedCount,
        ]);
    }

    private function rules(bool $requireRecipients = true): array
    {
        return [
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
        ];
    }

    private function formatAnnouncement(EmployeeAnnouncement $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'message' => $announcement->message,
            'priority' => $announcement->priority,
            'delivery_method' => $announcement->delivery_method,
            'recipient_type' => $announcement->recipient_type,
            'department_ids' => $announcement->department_ids,
            'employee_ids' => $announcement->employee_ids,
            'status' => $announcement->status,
            'scheduled_at' => $announcement->scheduled_at?->toDateTimeString(),
            'sent_at' => $announcement->sent_at?->toDateTimeString(),
            'total_recipients' => $announcement->total_recipients,
            'email_sent_count' => $announcement->email_sent_count,
            'sms_sent_count' => $announcement->sms_sent_count,
            'failed_count' => $announcement->failed_count,
            'error_message' => $announcement->error_message,
            'requires_acknowledgment' => (bool) $announcement->requires_acknowledgment,
            'expires_at' => $announcement->expires_at?->toDateTimeString(),
            'attachment_url' => $announcement->attachment_path ? Storage::url($announcement->attachment_path) : null,
            'created_by' => $announcement->creator?->name,
            'created_at' => $announcement->created_at?->toDateTimeString(),
            'updated_at' => $announcement->updated_at?->toDateTimeString(),
        ];
    }

    private function formatRecipient(AnnouncementRecipient $recipient): array
    {
        return [
            'employee_id' => $recipient->employee_id,
            'employee_name' => $recipient->employee?->full_name,
            'employee_email' => $recipient->employee?->email,
            'department_name' => $recipient->employee?->department?->name,
            'email_sent' => (bool) $recipient->email_sent,
            'email_sent_at' => $recipient->email_sent_at?->toDateTimeString(),
            'sms_sent' => (bool) $recipient->sms_sent,
            'sms_sent_at' => $recipient->sms_sent_at?->toDateTimeString(),
            'acknowledged' => (bool) $recipient->acknowledged,
            'acknowledged_at' => $recipient->acknowledged_at?->toDateTimeString(),
            'acknowledgment_note' => $recipient->acknowledgment_note,
            'read' => (bool) $recipient->read,
            'read_at' => $recipient->read_at?->toDateTimeString(),
        ];
    }
}

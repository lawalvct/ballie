<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportCategory;
use App\Models\SupportTicketReply;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketStatusHistory;
use App\Models\Tenant;
use App\Notifications\TicketRepliedNotification;
use App\Notifications\TicketStatusChangedNotification;
use App\Notifications\TicketClosedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SupportController extends Controller
{
    /**
     * Display support dashboard with tickets and statistics.
     */
    public function index(Request $request)
    {
        // Statistics
        $stats = [
            'new_tickets' => SupportTicket::where('status', 'new')->count(),
            'open_tickets' => SupportTicket::whereIn('status', ['new', 'open', 'in_progress', 'waiting_customer'])->count(),
            'resolved_today' => SupportTicket::where('status', 'resolved')->whereDate('resolved_at', today())->count(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
            'satisfaction_rating' => SupportTicket::whereNotNull('satisfaction_rating')->avg('satisfaction_rating'),
        ];

        // Query tickets
        $query = SupportTicket::with(['tenant', 'user', 'category', 'assignedAdmin', 'replies'])
            ->latest();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('tenant')) {
            $query->where('tenant_id', $request->tenant);
        }

        if ($request->filled('assigned')) {
            if ($request->assigned === 'me') {
                $query->where('assigned_to', Auth::id());
            } elseif ($request->assigned === 'unassigned') {
                $query->whereNull('assigned_to');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(25);
        $categories = SupportCategory::active()->ordered()->get();
        $tenants = Tenant::orderBy('name')->get();

        return view('super-admin.support.index', compact('tickets', 'stats', 'categories', 'tenants'));
    }

    /**
     * Display the specified ticket (admin view).
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load([
            'tenant',
            'user',
            'category',
            'assignedAdmin',
            'replies.user',
            'replies.admin',
            'replies.attachments',
            'attachments',
            'statusHistory.changedBy'
        ]);

        // Get all replies including internal notes
        $replies = $ticket->replies()->with('attachments')->get();

        // Get tenant's other tickets
        $otherTickets = SupportTicket::where('tenant_id', $ticket->tenant_id)
            ->where('id', '!=', $ticket->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('super-admin.support.show', compact('ticket', 'replies', 'otherTickets'));
    }

    /**
     * Post admin reply to ticket.
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:10',
            'status' => 'nullable|in:new,open,in_progress,waiting_customer,resolved,closed',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,txt,log,zip',
        ]);

        // Create the reply
        $reply = SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'admin_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal_note' => false,
            'is_automated' => false,
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('support-tickets/' . $ticket->id, $filename, 'private');

                SupportTicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'reply_id' => $reply->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by_type' => 'App\Models\SuperAdmin',
                    'uploaded_by_id' => Auth::id(),
                ]);
            }
        }

        // Update first response time if this is the first admin reply
        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        // Update status if provided
        if ($request->filled('status') && $request->status !== $ticket->status) {
            $oldStatus = $ticket->status;
            $ticket->update(['status' => $request->status]);

            // Update timestamps based on status
            if ($request->status === 'resolved') {
                $ticket->update(['resolved_at' => now()]);
            } elseif ($request->status === 'closed') {
                $ticket->update(['closed_at' => now()]);
            }

            SupportTicketStatusHistory::recordChange(
                $ticket,
                $oldStatus,
                $request->status,
                Auth::guard('super_admin')->user(),
                'Status changed with reply'
            );
        } else {
            // If status wasn't explicitly changed, update to in_progress if it was new
            if ($ticket->status === 'new') {
                $oldStatus = $ticket->status;
                $ticket->update(['status' => 'in_progress']);

                SupportTicketStatusHistory::recordChange(
                    $ticket,
                    $oldStatus,
                    'in_progress',
                    Auth::guard('super_admin')->user(),
                    'Admin replied'
                );
            }
        }

        // Notify the ticket owner about the admin reply
        $ticket->user->notify(new TicketRepliedNotification($ticket, $reply, true));

        return back()->with('success', 'Reply sent successfully!');
    }

    /**
     * Add internal note to ticket (not visible to tenant).
     */
    public function internalNote(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:10',
        ]);

        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'admin_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal_note' => true,
            'is_automated' => false,
        ]);

        return back()->with('success', 'Internal note added successfully!');
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,open,in_progress,waiting_customer,resolved,closed',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($ticket->status === $validated['status']) {
            return back()->with('info', 'Status is already ' . $validated['status']);
        }

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $validated['status']]);

        // Update timestamps based on status
        if ($validated['status'] === 'resolved') {
            $ticket->update(['resolved_at' => now()]);
        } elseif ($validated['status'] === 'closed') {
            $ticket->update(['closed_at' => now()]);
        }

        SupportTicketStatusHistory::recordChange(
            $ticket,
            $oldStatus,
            $validated['status'],
            Auth::guard('super_admin')->user(),
            $validated['notes'] ?? null
        );

        // Notify the ticket owner about the status change
        $ticket->user->notify(new TicketStatusChangedNotification($ticket, $oldStatus, $validated['status']));

        return back()->with('success', 'Status updated successfully!');
    }

    /**
     * Update ticket priority.
     */
    public function updatePriority(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket->update(['priority' => $validated['priority']]);

        return back()->with('success', 'Priority updated successfully!');
    }

    /**
     * Assign ticket to admin.
     */
    public function assign(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:super_admins,id',
        ]);

        $ticket->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('success', 'Ticket assigned successfully!');
    }

    /**
     * Delete ticket (soft delete).
     */
    public function destroy(SupportTicket $ticket)
    {
        // Notify user before deleting
        $ticket->user->notify(new TicketClosedNotification($ticket, Auth::guard('super_admin')->user()));

        $ticket->delete();

        return redirect()
            ->route('super-admin.support.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
            'status' => 'required|in:new,open,in_progress,waiting_customer,resolved,closed',
        ]);

        $updated = 0;
        foreach ($validated['ticket_ids'] as $ticketId) {
            $ticket = SupportTicket::find($ticketId);
            if ($ticket && $ticket->status !== $validated['status']) {
                $oldStatus = $ticket->status;
                $ticket->update(['status' => $validated['status']]);

                SupportTicketStatusHistory::recordChange(
                    $ticket,
                    $oldStatus,
                    $validated['status'],
                    Auth::guard('super_admin')->user(),
                    'Bulk status update'
                );
                $updated++;
            }
        }

        return back()->with('success', "Updated {$updated} tickets successfully!");
    }

    /**
     * Bulk assign tickets.
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
            'assigned_to' => 'nullable|exists:super_admins,id',
        ]);

        SupportTicket::whereIn('id', $validated['ticket_ids'])
            ->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('success', count($validated['ticket_ids']) . ' tickets assigned successfully!');
    }

    /**
     * Bulk delete tickets.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
        ]);

        SupportTicket::whereIn('id', $validated['ticket_ids'])->delete();

        return back()->with('success', count($validated['ticket_ids']) . ' tickets deleted successfully!');
    }

    /**
     * Display analytics and reports.
     */
    public function reports(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Tickets over time
        $ticketsOverTime = SupportTicket::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Tickets by category
        $ticketsByCategory = SupportTicket::whereBetween('created_at', [$dateFrom, $dateTo])
            ->join('support_categories', 'support_tickets.category_id', '=', 'support_categories.id')
            ->selectRaw('support_categories.name, COUNT(*) as count')
            ->groupBy('support_categories.name')
            ->orderByDesc('count')
            ->get();

        // Tickets by priority
        $ticketsByPriority = SupportTicket::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        // Tickets by status
        $ticketsByStatus = SupportTicket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Top tenants by ticket count
        $topTenants = SupportTicket::whereBetween('created_at', [$dateFrom, $dateTo])
            ->join('tenants', 'support_tickets.tenant_id', '=', 'tenants.id')
            ->selectRaw('tenants.name, COUNT(*) as count')
            ->groupBy('tenants.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Average metrics
        $avgFirstResponse = SupportTicket::whereNotNull('first_response_at')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_hours')
            ->value('avg_hours');

        $avgResolution = SupportTicket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        $avgSatisfaction = SupportTicket::whereNotNull('satisfaction_rating')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->avg('satisfaction_rating');

        return view('super-admin.support.reports', compact(
            'ticketsOverTime',
            'ticketsByCategory',
            'ticketsByPriority',
            'ticketsByStatus',
            'topTenants',
            'avgFirstResponse',
            'avgResolution',
            'avgSatisfaction',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export report.
     */
    public function exportReport(Request $request)
    {
        // TODO: Implement CSV/Excel export
        return back()->with('info', 'Export functionality coming soon!');
    }

    /**
     * Display settings.
     */
    public function settings()
    {
        // TODO: Load settings from database or config
        return view('super-admin.support.settings');
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        // TODO: Implement settings update
        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Calculate average response time in hours.
     */
    private function calculateAverageResponseTime()
    {
        $avgHours = SupportTicket::whereNotNull('first_response_at')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_hours')
            ->value('avg_hours');

        return $avgHours ? round($avgHours, 1) : 0;
    }
}

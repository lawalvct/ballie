<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportCategory;
use App\Models\SupportTicketReply;
use App\Models\SupportTicketAttachment;
use App\Models\SupportTicketStatusHistory;
use App\Models\KnowledgeBaseArticle;
use App\Models\SuperAdmin;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketRepliedNotification;
use App\Notifications\TicketClosedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    /**
     * Display a listing of tickets for the authenticated user.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['category', 'user', 'replies'])
            ->where('tenant_id', tenant()->id)
            ->where('user_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest()->paginate(25);
        $categories = SupportCategory::active()->ordered()->get();

        return view('tenant.support.index', compact('tickets', 'categories'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $categories = SupportCategory::active()->ordered()->get();

        return view('tenant.support.create', compact('categories'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:support_categories,id',
            'subject' => 'required|string|min:10|max:255',
            'description' => 'required|string|min:20',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,txt,log,zip',
        ]);

        // Collect system metadata
        $metadata = [
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'url' => $request->header('referer'),
        ];

        // Create the ticket
        $ticket = SupportTicket::create([
            'tenant_id' => tenant()->id,
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'new',
            'metadata' => $metadata,
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('support-tickets/' . $ticket->id, $filename, 'private');

                SupportTicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by_type' => 'App\Models\User',
                    'uploaded_by_id' => Auth::id(),
                ]);
            }
        }

        // Record initial status in history
        SupportTicketStatusHistory::recordChange(
            $ticket,
            '',
            'new',
            Auth::user(),
            'Ticket created'
        );

        // Notify all super admins about the new ticket
        $admins = SuperAdmin::all();
        Notification::send($admins, new TicketCreatedNotification($ticket));

        return redirect()
            ->route('tenant.support.tickets.show', ['tenant' => tenant()->slug, 'ticket' => $ticket->id])
            ->with('success', 'Support ticket created successfully! Ticket #' . $ticket->ticket_number);
    }

    /**
     * Display the specified ticket.
     */
    public function show($tenant, $supportTicket)
    {
        // Manually fetch the ticket to avoid route binding conflicts
        $supportTicket = SupportTicket::where('id', $supportTicket)
            ->where('tenant_id', tenant()->id)
            ->firstOrFail();

        // Ensure user can only view their own tickets
        if ($supportTicket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $supportTicket->load([
            'category',
            'user',
            'assignedAdmin',
            'replies.user',
            'replies.admin',
            'replies.attachments',
            'attachments',
            'statusHistory.changedBy'
        ]);

        // Get only public replies (exclude internal notes)
        $replies = $supportTicket->replies()->public()->with('attachments')->get();

        return view('tenant.support.show', compact('supportTicket', 'replies'))->with('ticket', $supportTicket);
    }

    /**
     * Add a reply to a ticket.
     */
    public function reply(Request $request, $tenant, $supportTicket)
    {
        // Manually fetch the ticket to avoid route binding conflicts
        $supportTicket = SupportTicket::where('id', $supportTicket)
            ->where('tenant_id', tenant()->id)
            ->firstOrFail();

        // Ensure user can only reply to their own tickets
        if ($supportTicket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Ensure ticket is not closed
        if ($supportTicket->isClosed()) {
            return back()->with('error', 'Cannot reply to a closed ticket. Please reopen it first.');
        }

        $validated = $request->validate([
            'message' => 'required|string|min:10',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,txt,log,zip',
        ]);

        // Create the reply
        $reply = SupportTicketReply::create([
            'ticket_id' => $supportTicket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal_note' => false,
            'is_automated' => false,
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('support-tickets/' . $supportTicket->id, $filename, 'private');

                SupportTicketAttachment::create([
                    'ticket_id' => $supportTicket->id,
                    'reply_id' => $reply->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by_type' => 'App\Models\User',
                    'uploaded_by_id' => Auth::id(),
                ]);
            }
        }

        // Update ticket status if it was waiting for customer
        if ($supportTicket->status === 'waiting_customer') {
            $oldStatus = $supportTicket->status;
            $supportTicket->update(['status' => 'open']);

            SupportTicketStatusHistory::recordChange(
                $supportTicket,
                $oldStatus,
                'open',
                Auth::user(),
                'Customer replied'
            );
        }

        // Notify admins about the customer reply
        $admins = SuperAdmin::all();
        Notification::send($admins, new TicketRepliedNotification($supportTicket, $reply, false));

        return back()->with('success', 'Reply added successfully!');
    }

    /**
     * Close a ticket.
     */
    public function close(Request $request, $tenant, $supportTicket)
    {
        // Manually fetch the ticket to avoid route binding conflicts
        $supportTicket = SupportTicket::where('id', $supportTicket)
            ->where('tenant_id', tenant()->id)
            ->firstOrFail();

        // Ensure user can only close their own tickets
        if ($supportTicket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Ensure ticket is not already closed
        if ($supportTicket->isClosed()) {
            return back()->with('error', 'Ticket is already closed.');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus = $supportTicket->status;
        $supportTicket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        SupportTicketStatusHistory::recordChange(
            $supportTicket,
            $oldStatus,
            'closed',
            Auth::user(),
            $validated['reason'] ?? 'Closed by customer'
        );
        // Notify admins that customer closed the ticket
        $admins = SuperAdmin::all();
        Notification::send($admins, new TicketClosedNotification($ticket, Auth::user()));
        return back()->with('success', 'Ticket closed successfully!');
    }

    /**
     * Reopen a closed ticket.
     */
    public function reopen(Request $request, $tenant, $supportTicket)
    {
        // Manually fetch the ticket to avoid route binding conflicts
        $supportTicket = SupportTicket::where('id', $supportTicket)
            ->where('tenant_id', tenant()->id)
            ->firstOrFail();

        // Ensure user can only reopen their own tickets
        if ($supportTicket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Ensure ticket is closed and can be reopened
        if (!$supportTicket->isClosed()) {
            return back()->with('error', 'Ticket is not closed.');
        }

        if (!$supportTicket->canReopen()) {
            return back()->with('error', 'Ticket cannot be reopened. It has been closed for more than 30 days.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        $oldStatus = $supportTicket->status;
        $supportTicket->update([
            'status' => 'open',
            'closed_at' => null,
        ]);

        SupportTicketStatusHistory::recordChange(
            $supportTicket,
            $oldStatus,
            'open',
            Auth::user(),
            'Reopened: ' . $validated['reason']
        );

        return back()->with('success', 'Ticket reopened successfully!');
    }

    /**
     * Submit a satisfaction rating for a ticket.
     */
    public function rate(Request $request, $tenant, $supportTicket)
    {
        // Manually fetch the ticket to avoid route binding conflicts
        $supportTicket = SupportTicket::where('id', $supportTicket)
            ->where('tenant_id', tenant()->id)
            ->firstOrFail();

        // Ensure user can only rate their own tickets
        if ($supportTicket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Ensure ticket is resolved or closed
        if (!in_array($supportTicket->status, ['resolved', 'closed'])) {
            return back()->with('error', 'You can only rate resolved or closed tickets.');
        }

        // Ensure ticket hasn't been rated yet
        if ($supportTicket->hasRating()) {
            return back()->with('error', 'You have already rated this ticket.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $supportTicket->update([
            'satisfaction_rating' => $validated['rating'],
            'satisfaction_comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Upload an attachment via AJAX.
     */
    public function uploadAttachment(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:support_tickets,id',
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,txt,log,zip',
        ]);

        $ticket = SupportTicket::findOrFail($validated['ticket_id']);

        // Ensure user owns the ticket
        if ($ticket->tenant_id !== tenant('id') || $ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('support-tickets/' . $ticket->id, $filename, 'private');

        $attachment = SupportTicketAttachment::create([
            'ticket_id' => $ticket->id,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by_type' => 'App\Models\User',
            'uploaded_by_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'attachment' => [
                'id' => $attachment->id,
                'name' => $attachment->original_name,
                'size' => $attachment->formatted_size,
                'url' => $attachment->download_url,
            ],
        ]);
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(SupportTicketAttachment $attachment)
    {
        // Ensure user owns the ticket
        if ($attachment->ticket->tenant_id !== tenant('id') || $attachment->ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this attachment.');
        }

        if (!Storage::disk('private')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('private')->download($attachment->file_path, $attachment->original_name);
    }

    /**
     * Display the knowledge base home.
     */
    public function knowledgeBase()
    {
        $featuredArticles = KnowledgeBaseArticle::published()
            ->featured()
            ->with('category')
            ->ordered()
            ->limit(6)
            ->get();

        $popularArticles = KnowledgeBaseArticle::published()
            ->with('category')
            ->popular(6)
            ->get();

        $categories = SupportCategory::active()
            ->ordered()
            ->withCount(['articles' => function($query) {
                $query->where('is_published', true);
            }])
            ->having('articles_count', '>', 0)
            ->get();

        return view('tenant.support.knowledge-base.index', compact('featuredArticles', 'popularArticles', 'categories'));
    }

    /**
     * Display articles for a specific category.
     */
    public function knowledgeBaseCategory(SupportCategory $category)
    {
        $articles = KnowledgeBaseArticle::published()
            ->where('category_id', $category->id)
            ->ordered()
            ->paginate(20);

        return view('tenant.support.knowledge-base.category', compact('category', 'articles'));
    }

    /**
     * Display a specific article.
     */
    public function knowledgeBaseArticle(SupportCategory $category, KnowledgeBaseArticle $article)
    {
        // Ensure article belongs to the category
        if ($article->category_id !== $category->id) {
            abort(404);
        }

        // Ensure article is published
        if (!$article->is_published) {
            abort(404);
        }

        // Increment view count
        $article->incrementViews();

        // Get related articles from the same category
        $relatedArticles = KnowledgeBaseArticle::published()
            ->where('category_id', $category->id)
            ->where('id', '!=', $article->id)
            ->ordered()
            ->limit(5)
            ->get();

        return view('tenant.support.knowledge-base.article', compact('article', 'category', 'relatedArticles'));
    }

    /**
     * Mark an article as helpful or not helpful.
     */
    public function markHelpful(Request $request, KnowledgeBaseArticle $article)
    {
        $validated = $request->validate([
            'helpful' => 'required|boolean',
        ]);

        if ($validated['helpful']) {
            $article->markHelpful();
            $message = 'Thank you for your feedback!';
        } else {
            $article->markNotHelpful();
            $message = 'Thank you for your feedback. We will work to improve this article.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'helpfulness' => $article->helpfulness_percentage,
        ]);
    }

    /**
     * Search tickets and knowledge base articles.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:3',
        ]);

        $query = $validated['q'];

        // Search user's tickets
        $tickets = SupportTicket::where('tenant_id', tenant('id'))
            ->where('user_id', Auth::id())
            ->where(function($q) use ($query) {
                $q->where('ticket_number', 'like', "%{$query}%")
                  ->orWhere('subject', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->latest()
            ->limit(10)
            ->get();

        // Search knowledge base articles
        $articles = KnowledgeBaseArticle::published()
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->with('category')
            ->ordered()
            ->limit(10)
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'tickets' => $tickets,
                'articles' => $articles,
            ]);
        }

        return view('tenant.support.search', compact('tickets', 'articles', 'query'));
    }
}

@extends('layouts.super-admin')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <a href="{{ route('super-admin.support.index') }}" class="text-gray-400 hover:text-gray-500">
                    <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $ticket->ticket_number }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Header -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
                            <div class="mt-2 flex items-center space-x-3">
                                @php
                                    $statusColors = [
                                        'new' => 'bg-red-100 text-red-800',
                                        'open' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'waiting_customer' => 'bg-purple-100 text-purple-800',
                                        'resolved' => 'bg-green-100 text-green-800',
                                        'closed' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $priorityColors = [
                                        'low' => 'bg-gray-100 text-gray-800',
                                        'medium' => 'bg-blue-100 text-blue-800',
                                        'high' => 'bg-orange-100 text-orange-800',
                                        'urgent' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$ticket->status] }}">
                                    {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$ticket->priority] }}">
                                    {{ ucfirst($ticket->priority) }} Priority
                                </span>
                                <span class="text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Original Message -->
                <div class="px-6 py-4">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">{{ substr($ticket->user->name, 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $ticket->tenant->name }}</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">
                                {!! nl2br(e($ticket->description)) !!}
                            </div>

                            @if($ticket->attachments->count() > 0)
                                <div class="mt-4">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Attachments:</p>
                                    <div class="space-y-2">
                                        @foreach($ticket->attachments as $attachment)
                                            <a href="{{ route('super-admin.support.tickets.show', $ticket) }}?download={{ $attachment->id }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                {{ $attachment->original_name }} ({{ number_format($attachment->file_size / 1024, 2) }} KB)
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Replies -->
            @foreach($replies as $reply)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($reply->admin_id)
                                    <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">{{ substr($reply->admin->name, 0, 2) }}</span>
                                    </div>
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">{{ substr($reply->user->name, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $reply->admin_id ? $reply->admin->name : $reply->user->name }}
                                            @if($reply->admin_id)
                                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded bg-indigo-100 text-indigo-800">Admin</span>
                                            @endif
                                            @if($reply->is_internal_note)
                                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Internal Note</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-2 text-sm text-gray-700">
                                    {!! nl2br(e($reply->message)) !!}
                                </div>

                                @if($reply->attachments->count() > 0)
                                    <div class="mt-4">
                                        <p class="text-xs font-medium text-gray-700 mb-2">Attachments:</p>
                                        <div class="space-y-2">
                                            @foreach($reply->attachments as $attachment)
                                                <a href="{{ route('super-admin.support.tickets.show', $ticket) }}?download={{ $attachment->id }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    {{ $attachment->original_name }} ({{ number_format($attachment->file_size / 1024, 2) }} KB)
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Reply Forms -->
            @if(!in_array($ticket->status, ['closed']))
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Reply to Customer</h3>
                    </div>
                    <form action="{{ route('super-admin.support.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
                        @csrf
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea id="message" name="message" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Type your reply..."></textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Update Status (Optional)</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Keep Current ({{ ucwords(str_replace('_', ' ', $ticket->status)) }})</option>
                                <option value="in_progress">In Progress</option>
                                <option value="waiting_customer">Waiting Customer</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Attachments (Optional)</label>
                            <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.txt,.log,.zip" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Max 5 files, 10MB each. Allowed: jpg, jpeg, png, pdf, txt, log, zip</p>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Add Internal Note</h3>
                        <p class="text-sm text-gray-500">Only visible to admins</p>
                    </div>
                    <form action="{{ route('super-admin.support.tickets.internal-note', $ticket) }}" method="POST" class="px-6 py-4 space-y-4">
                        @csrf
                        <div>
                            <textarea name="message" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Type internal note..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Add Note
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ticket Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Ticket Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->ticket_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Tenant</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->tenant->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @if($ticket->first_response_at)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">First Response</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->first_response_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    @endif
                    @if($ticket->resolved_at)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Resolved</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->resolved_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Replies</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->replies->count() }}</dd>
                    </div>
                    @if($ticket->assignedAdmin)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Assigned To</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->assignedAdmin->name }}</dd>
                        </div>
                    @endif
                    @if($ticket->satisfaction_rating)
                        <div class="border-t pt-3 mt-3">
                            <dt class="text-xs font-medium text-gray-500">Customer Satisfaction</dt>
                            <dd class="mt-1">
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $ticket->satisfaction_rating)
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                    <span class="text-sm font-semibold text-gray-900 ml-2">{{ $ticket->satisfaction_rating }}/5</span>
                                </div>
                                @if($ticket->satisfaction_comment)
                                    <p class="mt-2 text-sm text-gray-600 italic">"{{ $ticket->satisfaction_comment }}"</p>
                                @endif
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <!-- Update Status -->
                    <form action="{{ route('super-admin.support.tickets.update-status', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="block text-sm font-medium text-gray-700 mb-1">Change Status</label>
                        <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="new" {{ $ticket->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Waiting Customer</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </form>

                    <!-- Update Priority -->
                    <form action="{{ route('super-admin.support.tickets.update-priority', $ticket) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="block text-sm font-medium text-gray-700 mb-1">Change Priority</label>
                        <select name="priority" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </form>

                    <!-- Delete Ticket -->
                    <form action="{{ route('super-admin.support.tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 border border-red-600 rounded-md text-sm font-medium text-red-600 hover:bg-red-50">
                            Delete Ticket
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status History</h3>
                <div class="space-y-3">
                    @foreach($ticket->statusHistory as $history)
                        <div class="text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900">
                                    {{ ucwords(str_replace('_', ' ', $history->old_status)) }} → {{ ucwords(str_replace('_', ' ', $history->new_status)) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $history->changedBy->name ?? 'System' }} - {{ $history->created_at->diffForHumans() }}
                            </p>
                            @if($history->notes)
                                <p class="text-xs text-gray-600 mt-1">{{ $history->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Other Tickets from Same Tenant -->
            @if($otherTickets->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Other Tickets</h3>
                    <div class="space-y-2">
                        @foreach($otherTickets as $otherTicket)
                            <a href="{{ route('super-admin.support.tickets.show', $otherTicket) }}" class="block p-2 hover:bg-gray-50 rounded">
                                <div class="text-sm font-medium text-gray-900">{{ $otherTicket->ticket_number }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $otherTicket->subject }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

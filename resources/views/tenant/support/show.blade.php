@extends('layouts.tenant')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <a href="{{ route('tenant.support.index', ['tenant' => tenant()->slug]) }}"
       class="text-pink-600 hover:text-pink-700 font-medium inline-flex items-center mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Tickets
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Header -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="text-sm font-mono text-gray-500">#{{ $ticket->ticket_number }}</span>

                            <!-- Status Badge -->
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($ticket->status === 'new') bg-purple-100 text-purple-700
                                @elseif($ticket->status === 'open') bg-blue-100 text-blue-700
                                @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-700
                                @elseif($ticket->status === 'waiting_customer') bg-orange-100 text-orange-700
                                @elseif($ticket->status === 'resolved') bg-green-100 text-green-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ $ticket->status_label['text'] }}
                            </span>

                            <!-- Priority Badge -->
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($ticket->priority === 'urgent') bg-red-100 text-red-700
                                @elseif($ticket->priority === 'high') bg-orange-100 text-orange-700
                                @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ $ticket->priority_label['text'] }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Created {{ $ticket->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <!-- Original Message -->
                <div class="prose max-w-none">
                    {!! nl2br(e($ticket->description)) !!}
                </div>

                <!-- Attachments -->
                @if($ticket->attachments->where('reply_id', null)->count() > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Attachments</h4>
                        <div class="space-y-2">
                            @foreach($ticket->attachments->where('reply_id', null) as $attachment)
                                <a href="{{ route('tenant.support.attachments.download', ['tenant' => tenant()->slug, 'attachment' => $attachment->id]) }}"
                                   class="flex items-center space-x-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $attachment->formatted_size }}</p>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Replies -->
            @if($replies->count() > 0)
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-gray-900">Conversation</h2>
                    @foreach($replies as $reply)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full {{ $reply->isFromAdmin() ? 'bg-pink-500' : 'bg-gray-400' }} flex items-center justify-center text-white font-semibold">
                                        {{ substr($reply->author_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="font-semibold text-gray-900">{{ $reply->author_name }}</span>
                                        @if($reply->isFromAdmin())
                                            <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs font-semibold rounded">Support Team</span>
                                        @endif
                                        <span class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="prose max-w-none">
                                        {!! nl2br(e($reply->message)) !!}
                                    </div>

                                    <!-- Reply Attachments -->
                                    @if($reply->attachments->count() > 0)
                                        <div class="mt-4 space-y-2">
                                            @foreach($reply->attachments as $attachment)
                                                <a href="{{ route('tenant.support.attachments.download', ['tenant' => tenant()->slug, 'attachment' => $attachment->id]) }}"
                                                   class="inline-flex items-center space-x-2 p-2 bg-gray-50 hover:bg-gray-100 rounded text-sm transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span class="text-gray-700">{{ $attachment->original_name }}</span>
                                                    <span class="text-gray-500">({{ $attachment->formatted_size }})</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Reply Form -->
            @if($ticket->isOpen())
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Reply</h3>
                    <form method="POST" action="{{ route('tenant.support.tickets.reply', ['tenant' => tenant()->slug, 'supportTicket' => $ticket->id]) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <textarea name="message" rows="6" required minlength="10"
                                      placeholder="Type your message here..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Attachments (Optional)</label>
                            <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.txt,.log,.zip"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-gray-600">This ticket is {{ $ticket->status_label['text'] }}.
                    @if($ticket->canReopen())
                        <button onclick="document.getElementById('reopen-modal').classList.remove('hidden')"
                                class="text-pink-600 hover:text-pink-700 font-semibold">
                            Click here to reopen
                        </button>
                    @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ticket Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $ticket->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $ticket->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    @if($ticket->assignedAdmin)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $ticket->assignedAdmin->name }}</dd>
                        </div>
                    @endif
                    @if($ticket->resolved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Resolved</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Replies</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $ticket->reply_count }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($ticket->isOpen())
                        <button onclick="document.getElementById('close-modal').classList.remove('hidden')"
                                class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                            Close Ticket
                        </button>
                    @elseif($ticket->canReopen())
                        <button onclick="document.getElementById('reopen-modal').classList.remove('hidden')"
                                class="w-full px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg font-medium transition-colors">
                            Reopen Ticket
                        </button>
                    @endif

                    @if(in_array($ticket->status, ['resolved', 'closed']) && !$ticket->hasRating())
                        <button onclick="document.getElementById('rating-modal').classList.remove('hidden')"
                                class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition-colors">
                            Rate Support
                        </button>
                    @endif
                </div>
            </div>

            <!-- Status History -->
            @if($ticket->statusHistory->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status History</h3>
                    <div class="space-y-3">
                        @foreach($ticket->statusHistory->sortByDesc('created_at') as $history)
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">{{ $history->change_description }}</p>
                                <p class="text-gray-500">{{ $history->changed_by_name }} • {{ $history->created_at->diffForHumans() }}</p>
                                @if($history->notes)
                                    <p class="text-gray-600 mt-1 italic">{{ $history->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Close Ticket Modal -->
<div id="close-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Close Ticket</h3>
        <form method="POST" action="{{ route('tenant.support.tickets.close', ['tenant' => tenant()->slug, 'supportTicket' => $ticket->id]) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                <textarea name="reason" rows="3" maxlength="500"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('close-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg">
                    Close Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reopen Ticket Modal -->
<div id="reopen-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Reopen Ticket</h3>
        <form method="POST" action="{{ route('tenant.support.tickets.reopen', ['tenant' => tenant()->slug, 'supportTicket' => $ticket->id]) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="3" required minlength="10" maxlength="500"
                          placeholder="Why are you reopening this ticket?"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('reopen-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg">
                    Reopen Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rating Modal -->
<div id="rating-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Rate Our Support</h3>
        <form method="POST" action="{{ route('tenant.support.tickets.rate', ['tenant' => tenant()->slug, 'supportTicket' => $ticket->id]) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">How satisfied are you?</label>
                <div class="flex justify-center space-x-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" required class="sr-only peer">
                            <svg class="w-10 h-10 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </label>
                    @endfor
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Comments (Optional)</label>
                <textarea name="comment" rows="3" maxlength="1000"
                          placeholder="Tell us more about your experience..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('rating-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg">
                    Submit Rating
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.tenant')

@section('title', 'Notifications')

@section('page-title', 'Notifications')

@section('page-description', 'View and manage your notifications')

@section('content')
<div class="space-y-6">
    <!-- Actions Bar -->
    <div class="flex justify-between items-center">
        <div class="flex space-x-2">
            <button onclick="markAllAsRead()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Mark All as Read
            </button>
        </div>
        <div class="text-sm text-gray-600">
            {{ $notifications->total() }} total notifications
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-sm border">
        @forelse($notifications as $notification)
        <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-blue-50' }}">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4 flex-1">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                            @if(!$notification->read_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                New
                            </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                            @if(isset($notification->data['action_url']))
                            <a href="{{ $notification->data['action_url'] }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                {{ $notification->data['action_text'] ?? 'View' }} →
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    @if(!$notification->read_at)
                    <button onclick="markAsRead('{{ $notification->id }}')" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Mark as read">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                    @endif
                    <button onclick="deleteNotification('{{ $notification->id }}')" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No notifications</h3>
            <p class="text-sm text-gray-500">You're all caught up!</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="flex justify-center">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`{{ url(tenant()->slug . '/notifications') }}/${id}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('{{ route('tenant.notifications.mark-all-read', tenant()->slug) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

function deleteNotification(id) {
    if(!confirm('Are you sure you want to delete this notification?')) return;
    
    fetch(`{{ url(tenant()->slug . '/notifications') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection

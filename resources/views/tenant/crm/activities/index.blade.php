@extends('layouts.tenant')
@section('page-title', 'Customer Activities')
@section('page-description', 'Log and manage customer activities')

@section('content')
<style>
.sticky-note {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transform: rotate(-1deg);
    transition: all 0.3s ease;
}
.sticky-note:nth-child(even) { transform: rotate(1deg); }
.sticky-note:hover { transform: rotate(0deg) scale(1.02); box-shadow: 0 10px 20px rgba(0,0,0,0.15); z-index: 10; }
</style>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Customer Activities</h1>
        <a href="{{ route('tenant.crm.activities.create', $tenant->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg">
            + New Activity
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="border rounded px-3 py-2 text-sm">
            <select name="customer_id" class="border rounded px-3 py-2 text-sm">
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->full_name }}</option>
                @endforeach
            </select>
            <select name="activity_type" class="border rounded px-3 py-2 text-sm">
                <option value="">All Types</option>
                <option value="call" {{ request('activity_type') == 'call' ? 'selected' : '' }}>Call</option>
                <option value="email" {{ request('activity_type') == 'email' ? 'selected' : '' }}>Email</option>
                <option value="meeting" {{ request('activity_type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                <option value="note" {{ request('activity_type') == 'note' ? 'selected' : '' }}>Note</option>
                <option value="task" {{ request('activity_type') == 'task' ? 'selected' : '' }}>Task</option>
                <option value="follow_up" {{ request('activity_type') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
            </select>
            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-3 py-2 text-sm">
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Filter</button>
                <a href="{{ route('tenant.crm.activities.index', $tenant->slug) }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm">Clear</a>
            </div>
        </form>
    </div>

    <!-- Sticky Notes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($activities as $activity)
            <div class="sticky-note p-5 rounded-lg relative
                @if($activity->activity_type == 'call') bg-yellow-100
                @elseif($activity->activity_type == 'email') bg-blue-100
                @elseif($activity->activity_type == 'meeting') bg-purple-100
                @elseif($activity->activity_type == 'note') bg-green-100
                @elseif($activity->activity_type == 'task') bg-pink-100
                @else bg-orange-100 @endif">
                
                <!-- Status Badge -->
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($activity->status === 'completed') bg-green-500 text-white
                        @elseif($activity->status === 'pending') bg-yellow-500 text-white
                        @else bg-red-500 text-white @endif">
                        {{ ucfirst($activity->status) }}
                    </span>
                </div>

                <!-- Activity Type Icon -->
                <div class="mb-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full
                        @if($activity->activity_type == 'call') bg-yellow-200
                        @elseif($activity->activity_type == 'email') bg-blue-200
                        @elseif($activity->activity_type == 'meeting') bg-purple-200
                        @elseif($activity->activity_type == 'note') bg-green-200
                        @elseif($activity->activity_type == 'task') bg-pink-200
                        @else bg-orange-200 @endif">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($activity->activity_type == 'call')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            @elseif($activity->activity_type == 'email')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            @endif
                        </svg>
                    </span>
                    <span class="ml-2 text-xs font-semibold text-gray-600 uppercase">{{ str_replace('_', ' ', $activity->activity_type) }}</span>
                </div>

                <!-- Subject -->
                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $activity->subject }}</h3>

                <!-- Customer -->
                <p class="text-sm text-gray-700 mb-2">
                    <span class="font-semibold">Customer:</span> {{ $activity->customer->full_name }}
                </p>

                <!-- Description -->
                @if($activity->description)
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($activity->description, 80) }}</p>
                @endif

                <!-- Footer -->
                <div class="border-t pt-3 mt-3 flex justify-between items-center text-xs text-gray-600">
                    <div>
                        <p>{{ $activity->activity_date->format('M d, Y') }}</p>
                        <p class="text-gray-500">{{ $activity->user->name }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('tenant.crm.activities.edit', [$tenant->slug, $activity->id]) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                        <form action="{{ route('tenant.crm.activities.destroy', [$tenant->slug, $activity->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No activities found</p>
                <a href="{{ route('tenant.crm.activities.create', $tenant->slug) }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Create your first activity</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $activities->links() }}
    </div>
</div>
@endsection

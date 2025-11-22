@extends('layouts.tenant')
@section('page-title', 'Customer Activities' )


@section('page-description', 'Log and manage customer activities such as calls, emails, meetings, notes, tasks, and follow-ups.')
@section('content')
<div class="container mx-auto px-4 py-8">


    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 px-4 py-3">
                <span class="text-2xl">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <input type="text" name="search" placeholder="Search subject..." value="{{ request('search') }}" class="border rounded px-3 py-2">
            <select name="customer_id" class="border rounded px-3 py-2">
                <option value="">All Customers</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->full_name }}</option>
                @endforeach
            </select>
            <select name="activity_type" class="border rounded px-3 py-2">
                <option value="">All Types</option>
                <option value="call" {{ request('activity_type') == 'call' ? 'selected' : '' }}>Call</option>
                <option value="email" {{ request('activity_type') == 'email' ? 'selected' : '' }}>Email</option>
                <option value="meeting" {{ request('activity_type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                <option value="note" {{ request('activity_type') == 'note' ? 'selected' : '' }}>Note</option>
                <option value="task" {{ request('activity_type') == 'task' ? 'selected' : '' }}>Task</option>
                <option value="follow_up" {{ request('activity_type') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
            </select>
            <select name="status" class="border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-3 py-2" placeholder="From">
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
                <a href="{{ route('tenant.crm.activities.index', $tenant->slug) }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Clear</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Logged By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $activity->activity_date->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $activity->customer->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $activity->subject }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($activity->status === 'completed') bg-green-100 text-green-800
                                @elseif($activity->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($activity->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $activity->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('tenant.crm.activities.edit', [$tenant->slug, $activity->id]) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                <form action="{{ route('tenant.crm.activities.destroy', [$tenant->slug, $activity->id]) }}" method="POST" onsubmit="return confirm('Delete this activity?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No activities logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>
@endsection

@extends('layouts.tenant')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Log Customer Activity</h1>

        <form action="{{ route('tenant.crm.activities.store', $tenant->slug) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Customer *</label>
                <select name="customer_id" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Activity Type *</label>
                <select name="activity_type" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                    <option value="">Select Type</option>
                    <option value="call">Call</option>
                    <option value="email">Email</option>
                    <option value="meeting">Meeting</option>
                    <option value="note">Note</option>
                    <option value="task">Task</option>
                    <option value="follow_up">Follow Up</option>
                </select>
                @error('activity_type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Subject *</label>
                <input type="text" name="subject" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                @error('subject')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                @error('description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Activity Date *</label>
                <input type="datetime-local" name="activity_date" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                @error('activity_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Status *</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                @error('status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Save Activity
                </button>
                <a href="{{ route('tenant.crm.activities.index', $tenant->slug) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

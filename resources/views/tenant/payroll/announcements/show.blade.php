@extends('layouts.tenant')

@section('title', 'Announcement Details - ' . $tenant->name)

@section('page-title', 'Announcement Details')
@section('page-description', '')

@section('action-buttons')
<div class="flex items-center space-x-3">
    <a href="{{ route('tenant.payroll.announcements.index', ['tenant' => $tenant->slug]) }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to List
    </a>

    <a href="{{ route('tenant.payroll.announcements.create', ['tenant' => $tenant->slug]) }}"
       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        New Announcement
    </a>

    @if($announcement->canBeEdited())
    <a href="{{ route('tenant.payroll.announcements.edit', ['tenant' => $tenant->slug, 'announcement' => $announcement->id]) }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        Edit
    </a>
    @endif

    @if($announcement->canBeSent())
    <form action="{{ route('tenant.payroll.announcements.send', ['tenant' => $tenant->slug, 'announcement' => $announcement->id]) }}"
          method="POST"
          onsubmit="return confirm('Are you sure you want to send this announcement now?');">
        @csrf
        <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
            Send Now
        </button>
    </form>
    @endif

    @if($announcement->canBeDeleted())
    <form action="{{ route('tenant.payroll.announcements.destroy', ['tenant' => $tenant->slug, 'announcement' => $announcement->id]) }}"
          method="POST"
          onsubmit="return confirm('Are you sure you want to delete this announcement? This action cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Delete
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Announcement Header Card -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $announcement->title }}</h2>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            By {{ $announcement->creator->name }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $announcement->created_at->format('M d, Y h:i A') }}
                        </span>
                        @if($announcement->scheduled_at)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Scheduled: {{ $announcement->scheduled_at->format('M d, Y h:i A') }}
                        </span>
                        @endif
                        @if($announcement->sent_at)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sent: {{ $announcement->sent_at->format('M d, Y h:i A') }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2 ml-4">
                    <span class="{{ $announcement->getStatusBadgeClass() }} px-3 py-1 rounded-full text-xs font-semibold">
                        {{ ucfirst($announcement->status) }}
                    </span>
                    <span class="{{ $announcement->getPriorityBadgeClass() }} px-3 py-1 rounded-full text-xs font-semibold">
                        {{ ucfirst($announcement->priority) }} Priority
                    </span>
                </div>
            </div>

            @if($announcement->error_message)
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error occurred while sending</h3>
                        <div class="mt-2 text-sm text-red-700">
                            {{ $announcement->error_message }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="prose max-w-none">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $announcement->message }}</p>
            </div>

            @if($announcement->attachment_path)
            <div class="mt-4 flex items-center p-3 bg-gray-50 border border-gray-200 rounded-md">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <a href="{{ Storage::url($announcement->attachment_path) }}"
                   target="_blank"
                   class="text-primary-600 hover:text-primary-800 font-medium">
                    View Attachment
                </a>
            </div>
            @endif

            @if($announcement->expires_at)
            <div class="mt-4 flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Expires: {{ $announcement->expires_at->format('M d, Y h:i A') }}
            </div>
            @endif
        </div>
    </div>

    <!-- Delivery Statistics Card -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Delivery Statistics
            </h3>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Recipients -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-100">Total Recipients</p>
                            <p class="text-3xl font-bold mt-1">{{ $announcement->total_recipients }}</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-blue-100 mt-2">
                        Via {{ ucfirst($announcement->delivery_method) }}
                    </p>
                </div>

                <!-- Email Sent -->
                @if(in_array($announcement->delivery_method, ['email', 'both']))
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-100">Email Sent</p>
                            <p class="text-3xl font-bold mt-1">{{ $announcement->email_sent_count }}</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-green-100 mt-2">
                        {{ $announcement->total_recipients > 0 ? round(($announcement->email_sent_count / $announcement->total_recipients) * 100, 1) : 0 }}% delivered
                    </p>
                </div>
                @endif

                <!-- SMS Sent -->
                @if(in_array($announcement->delivery_method, ['sms', 'both']))
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-100">SMS Sent</p>
                            <p class="text-3xl font-bold mt-1">{{ $announcement->sms_sent_count }}</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-purple-100 mt-2">
                        {{ $announcement->total_recipients > 0 ? round(($announcement->sms_sent_count / $announcement->total_recipients) * 100, 1) : 0 }}% delivered
                    </p>
                </div>
                @endif

                <!-- Failed Count -->
                @if($announcement->failed_count > 0)
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-100">Failed</p>
                            <p class="text-3xl font-bold mt-1">{{ $announcement->failed_count }}</p>
                        </div>
                        <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-red-100 mt-2">Delivery errors</p>
                </div>
                @endif
            </div>

            @if($announcement->requires_acknowledgment)
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Acknowledgment Rate -->
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-100">Acknowledged</p>
                            <p class="text-3xl font-bold mt-1">{{ $announcement->getAcknowledgmentRate() }}%</p>
                        </div>
                        <div class="bg-indigo-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-indigo-100 mt-2">Employees acknowledged</p>
                </div>

                <!-- Read Rate -->
                <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-teal-100">Read</p>
                            <p class="text-3xl font-bold mt-1">{{ $announcement->getReadRate() }}%</p>
                        </div>
                        <div class="bg-teal-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-teal-100 mt-2">Employees viewed</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Recipients List -->
    @if($announcement->recipients->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Recipients ({{ $announcement->recipients->count() }})
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department
                            </th>
                            @if(in_array($announcement->delivery_method, ['email', 'both']))
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            @endif
                            @if(in_array($announcement->delivery_method, ['sms', 'both']))
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                SMS
                            </th>
                            @endif
                            @if($announcement->requires_acknowledgment)
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acknowledged
                            </th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Read
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($announcement->recipients as $recipient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $recipient->employee->first_name }} {{ $recipient->employee->last_name }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $recipient->employee->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $recipient->employee->department->name ?? 'N/A' }}
                            </td>
                            @if(in_array($announcement->delivery_method, ['email', 'both']))
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($recipient->email_sent)
                                    <span class="inline-flex items-center text-green-600" title="{{ $recipient->email_sent_at?->format('M d, Y h:i A') }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @else
                                    <span class="text-gray-400">
                                        <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </td>
                            @endif
                            @if(in_array($announcement->delivery_method, ['sms', 'both']))
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($recipient->sms_sent)
                                    <span class="inline-flex items-center text-green-600" title="{{ $recipient->sms_sent_at?->format('M d, Y h:i A') }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @else
                                    <span class="text-gray-400">
                                        <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @endif
                            </td>
                            @endif
                            @if($announcement->requires_acknowledgment)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($recipient->acknowledged)
                                    <span class="inline-flex items-center text-green-600" title="{{ $recipient->acknowledged_at?->format('M d, Y h:i A') }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @else
                                    <span class="text-gray-400">Pending</span>
                                @endif
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($recipient->read)
                                    <span class="inline-flex items-center text-blue-600" title="{{ $recipient->read_at?->format('M d, Y h:i A') }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                @else
                                    <span class="text-gray-400">Unread</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

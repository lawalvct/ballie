@extends('layouts.super-admin')

@section('title', 'Response Templates')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Response Templates</h1>
            <p class="mt-1 text-sm text-gray-600">Manage canned responses for common support scenarios</p>
        </div>
        <a href="{{ route('super-admin.support.templates.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Template
        </a>
    </div>

    <!-- Templates List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($templates->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($templates as $template)
                    <li class="hover:bg-gray-50">
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $template->name }}</h3>
                                        @if($template->is_active)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </div>
                                    @if($template->subject)
                                        <p class="mt-1 text-sm text-gray-600">Subject: {{ $template->subject }}</p>
                                    @endif
                                    <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ Str::limit($template->content, 200) }}</p>
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    <a href="{{ route('super-admin.support.templates.edit', $template) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('super-admin.support.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $templates->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No templates</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new response template.</p>
                <div class="mt-6">
                    <a href="{{ route('super-admin.support.templates.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Template
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

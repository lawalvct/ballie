@extends('layouts.tenant')

@section('title', 'Create Support Ticket')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('tenant.support.index', ['tenant' => tenant()->slug]) }}"
           class="text-pink-600 hover:text-pink-700 font-medium inline-flex items-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Tickets
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Create Support Ticket</h1>
        <p class="text-gray-600 mt-1">Tell us how we can help you</p>
    </div>

    <!-- Help Notice -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Before creating a ticket, check our
                    <a href="{{ route('tenant.support.knowledge-base.index', ['tenant' => tenant()->slug]) }}" class="font-medium underline">Knowledge Base</a>
                    for quick answers to common questions.
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.support.tickets.store', ['tenant' => tenant()->slug]) }}"
          enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8">
        @csrf

        <!-- Category -->
        <div class="mb-6">
            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Category <span class="text-red-500">*</span>
            </label>
            <select name="category_id" id="category_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                <option value="">Select a category...</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-sm mt-1">Choose the category that best describes your issue</p>
        </div>

        <!-- Priority -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Priority <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <label class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors @error('priority') border-red-500 @enderror">
                    <input type="radio" name="priority" value="low" {{ old('priority', 'medium') == 'low' ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-200 absolute inset-0 rounded-lg border-2"></div>
                    <span class="text-sm font-semibold text-gray-700 mb-1">Low</span>
                    <span class="text-xs text-gray-500">General inquiry</span>
                </label>

                <label class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="radio" name="priority" value="medium" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-200 absolute inset-0 rounded-lg border-2"></div>
                    <span class="text-sm font-semibold text-gray-700 mb-1">Medium</span>
                    <span class="text-xs text-gray-500">Normal issue</span>
                </label>

                <label class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="radio" name="priority" value="high" {{ old('priority') == 'high' ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-200 absolute inset-0 rounded-lg border-2"></div>
                    <span class="text-sm font-semibold text-gray-700 mb-1">High</span>
                    <span class="text-xs text-gray-500">Urgent issue</span>
                </label>

                <label class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="radio" name="priority" value="urgent" {{ old('priority') == 'urgent' ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-200 absolute inset-0 rounded-lg border-2"></div>
                    <span class="text-sm font-semibold text-gray-700 mb-1">Urgent</span>
                    <span class="text-xs text-gray-500">Critical issue</span>
                </label>
            </div>
            @error('priority')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subject -->
        <div class="mb-6">
            <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                Subject <span class="text-red-500">*</span>
            </label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                   minlength="10" maxlength="255"
                   placeholder="Brief description of your issue..."
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('subject') border-red-500 @enderror">
            @error('subject')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-sm mt-1">Minimum 10 characters</p>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                Description <span class="text-red-500">*</span>
            </label>
            <textarea name="description" id="description" rows="8" required minlength="20"
                      placeholder="Please provide detailed information about your issue..."
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-sm mt-1">Minimum 20 characters. Include any relevant details, error messages, or steps to reproduce.</p>
        </div>

        <!-- Attachments -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Attachments <span class="text-gray-500 font-normal">(Optional)</span>
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-pink-400 transition-colors">
                <input type="file" name="attachments[]" id="attachments" multiple accept=".jpg,.jpeg,.png,.pdf,.txt,.log,.zip"
                       class="hidden" onchange="displayFiles(this)">
                <label for="attachments" class="cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500">JPG, PNG, PDF, TXT, LOG, ZIP (max 10MB each, up to 5 files)</p>
                </label>
            </div>
            <div id="file-list" class="mt-3 space-y-2"></div>
            @error('attachments')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('tenant.support.index', ['tenant' => tenant()->slug]) }}"
               class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                Create Ticket
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function displayFiles(input) {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';

    if (input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
            div.innerHTML = `
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900">${file.name}</p>
                        <p class="text-xs text-gray-500">${fileSize} MB</p>
                    </div>
                </div>
            `;
            fileList.appendChild(div);
        });
    }
}
</script>
@endpush
@endsection

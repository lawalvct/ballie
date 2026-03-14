{{-- Files Tab --}}
<div x-show="activeTab === 'files'" x-transition>
    <!-- Upload Form -->
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Upload File</h4>
        <form @submit.prevent="uploadFile()" enctype="multipart/form-data">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="file" x-ref="fileInput" @change="selectedFile = $event.target.files[0]"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                    <p class="mt-1 text-xs text-gray-500">Max file size: 10MB</p>
                </div>
                <button type="submit" :disabled="fileLoading || !selectedFile"
                        class="inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                    <svg x-show="fileLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Upload
                </button>
            </div>
        </form>
    </div>

    <!-- Files List -->
    <div class="space-y-2">
        @forelse($project->attachments as $attachment)
            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200" id="attachment-{{ $attachment->id }}">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <!-- File Icon -->
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center {{ $attachment->is_image ? 'bg-green-100' : 'bg-gray-100' }}">
                        @if($attachment->is_image)
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->file_name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $attachment->file_size_formatted }} &middot; {{ $attachment->user->name ?? 'Unknown' }} &middot; {{ $attachment->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 ml-3">
                    <a href="{{ route('tenant.projects.attachments.download', [$tenant->slug, $project->id, $attachment->id]) }}"
                       class="p-1 text-gray-400 hover:text-violet-600 transition-colors duration-200" title="Download">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </a>
                    <button onclick="deleteAttachment({{ $attachment->id }})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <p>No files uploaded yet. Add project documents, images, or contracts above.</p>
            </div>
        @endforelse
    </div>
</div>

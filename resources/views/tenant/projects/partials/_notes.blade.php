{{-- Notes Tab --}}
<div x-show="activeTab === 'notes'" x-transition>
    <!-- Add Note Form -->
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Add Note</h4>
        <form @submit.prevent="addNote()">
            <textarea x-model="newNote.content" rows="3" placeholder="Write a note about this project..." required
                      class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500 mb-3"></textarea>
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" x-model="newNote.is_internal" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    <span class="ml-2 text-sm text-gray-600">Internal note (not visible to client)</span>
                </label>
                <button type="submit" :disabled="noteLoading"
                        class="inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                    Add Note
                </button>
            </div>
        </form>
    </div>

    <!-- Notes Feed -->
    <div id="notes-list" class="space-y-4">
        @forelse($project->notes as $note)
            <div class="flex space-x-3" id="note-{{ $note->id }}">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-violet-100 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-violet-600">{{ strtoupper(substr($note->user->name ?? '?', 0, 2)) }}</span>
                    </div>
                </div>
                <div class="flex-1 bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900">{{ $note->user->name ?? 'Unknown' }}</span>
                            <span class="text-xs text-gray-400">{{ $note->created_at->diffForHumans() }}</span>
                            @if($note->is_internal)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Internal</span>
                            @endif
                        </div>
                        <button onclick="deleteNote({{ $note->id }})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete note">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $note->content }}</p>
                </div>
            </div>
        @empty
            <div id="notes-empty-state" class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                <p>No notes yet. Add one to keep track of important information.</p>
            </div>
        @endforelse
    </div>
</div>

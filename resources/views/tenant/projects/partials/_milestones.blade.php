{{-- Milestones Tab --}}
<div x-show="activeTab === 'milestones'" x-transition>
    <!-- Add Milestone Form -->
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Add New Milestone</h4>
        <form @submit.prevent="addMilestone()" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-4">
                <input type="text" x-model="newMilestone.title" placeholder="Milestone title..." required
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div class="md:col-span-2">
                <input type="text" x-ref="milestoneAmountInput" :value="milestoneAmountDisplay" @input="setMilestoneAmount($event.target.value)" inputmode="decimal" placeholder="Amount (₦)"
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div class="md:col-span-2">
                <input type="date" x-model="newMilestone.due_date"
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center h-full px-2">
                    <input type="checkbox" x-model="newMilestone.is_billable" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    <span class="ml-2 text-sm text-gray-700">Billable</span>
                </label>
            </div>
            <div class="md:col-span-2">
                <button type="submit" :disabled="milestoneLoading"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                    Add Milestone
                </button>
            </div>
        </form>
    </div>

    <!-- Milestones List -->
    <div id="milestones-list" class="space-y-3">
        @forelse($project->milestones as $milestone)
            <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg" id="milestone-{{ $milestone->id }}">
                <div class="flex items-center space-x-4 flex-1 min-w-0">
                    <!-- Complete Toggle -->
                    <button onclick="toggleMilestone({{ $milestone->id }}, {{ $milestone->completed_at ? 'false' : 'true' }})"
                            class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors duration-200 {{ $milestone->completed_at ? 'bg-green-500 border-green-500' : 'border-gray-300 hover:border-green-400' }}">
                        @if($milestone->completed_at)
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @endif
                    </button>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 {{ $milestone->completed_at ? 'line-through text-gray-400' : '' }}">
                            {{ $milestone->title }}
                        </p>
                        <div class="flex items-center space-x-3 mt-1">
                            @if($milestone->amount)
                                <span class="text-xs font-medium {{ $milestone->is_billable ? 'text-violet-600' : 'text-gray-500' }}">
                                    ₦{{ number_format($milestone->amount, 2) }}
                                    @if($milestone->is_billable) (Billable) @endif
                                </span>
                            @endif
                            @if($milestone->due_date)
                                <span class="text-xs text-gray-500">Due: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}</span>
                            @endif
                            @if($milestone->invoice_id)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Invoiced</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 ml-3">
                    @if($milestone->completed_at && $milestone->is_billable && $milestone->amount && !$milestone->invoice_id)
                        <button onclick="invoiceMilestone({{ $milestone->id }})"
                                class="inline-flex items-center px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 border border-green-200 transition" title="Create Invoice">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                            </svg>
                            Invoice
                        </button>
                    @endif
                    <button onclick="deleteMilestone({{ $milestone->id }})" class="p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete milestone">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div id="milestones-empty-state" class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                </svg>
                <p>No milestones yet. Add one above to track project deliverables.</p>
            </div>
        @endforelse
    </div>
</div>

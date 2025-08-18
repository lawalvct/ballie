@php
    // Group accounts by their account type and parent relationship for better tree structure
    $groupedAccounts = $accounts->groupBy(function($account) {
        return $account->accountGroup->nature ?? 'other';
    });
    
    // Define the order of account nature for display
    $natureOrder = ['assets', 'liabilities', 'equity', 'income', 'expense'];
    $natureLabels = [
        'assets' => 'ASSETS',
        'liabilities' => 'LIABILITIES', 
        'equity' => 'EQUITY',
        'income' => 'REVENUE',
        'expense' => 'EXPENSES'
    ];
    $natureIcons = [
        'assets' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
        'liabilities' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'equity' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'income' => 'M7 11l5-9 5 9M12 2v7m0 0l4 4m-4 0l-4 4',
        'expense' => 'M17 13l-5 5-5-5M12 18V6'
    ];
@endphp

<div class="accounting-tree-container">
    @forelse($groupedAccounts as $nature => $natureAccounts)
        @if(in_array($nature, $natureOrder))
            <div class="nature-group mb-8">
                <!-- Nature Header -->
                <div class="nature-header bg-gradient-to-r from-slate-50 to-slate-100 border-l-4 border-slate-400 px-6 py-4 mb-4 rounded-r-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $natureIcons[$nature] ?? 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' }}"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wider">{{ $natureLabels[$nature] ?? ucfirst($nature) }}</h2>
                                <p class="text-sm text-slate-600">{{ $natureAccounts->count() }} {{ Str::plural('account', $natureAccounts->count()) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $totalBalance = $natureAccounts->sum(fn($account) => $account->getCurrentBalance());
                                $balanceClass = $totalBalance > 0 ? 'text-green-700' : ($totalBalance < 0 ? 'text-red-700' : 'text-slate-600');
                            @endphp
                            <div class="text-sm text-slate-600">Total Balance</div>
                            <div class="text-lg font-bold {{ $balanceClass }}">
                                ₦{{ number_format(abs($totalBalance), 2) }}
                                <span class="text-sm font-normal">{{ $totalBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Groups within this Nature -->
                @php
                    $accountGroups = $natureAccounts->groupBy('account_group_id');
                @endphp
                
                @foreach($accountGroups as $groupId => $groupAccounts)
                    @php
                        $accountGroup = $groupAccounts->first()->accountGroup;
                        $parentAccounts = $groupAccounts->where('parent_id', null);
                    @endphp

                    <div class="account-group mb-6">
                        <!-- Group Header -->
                        <div class="group-header bg-white border border-slate-200 rounded-lg px-4 py-3 mb-3 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <button class="group-toggle p-1 rounded hover:bg-slate-100 transition-colors" type="button" onclick="toggleGroup(this)">
                                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0v-3.5a1.5 1.5 0 013 0V21m-4-3h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-slate-800">{{ $accountGroup->name ?? 'Uncategorized' }}</h3>
                                        <p class="text-sm text-slate-500">{{ $groupAccounts->count() }} {{ Str::plural('account', $groupAccounts->count()) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $groupBalance = $groupAccounts->sum(fn($account) => $account->getCurrentBalance());
                                        $groupBalanceClass = $groupBalance > 0 ? 'text-green-600' : ($groupBalance < 0 ? 'text-red-600' : 'text-slate-500');
                                    @endphp
                                    <div class="text-xs text-slate-500">Group Total</div>
                                    <div class="text-sm font-semibold {{ $groupBalanceClass }}">
                                        ₦{{ number_format(abs($groupBalance), 2) }}
                                        <span class="text-xs">{{ $groupBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Group Accounts -->
                        <div class="group-accounts ml-4">
                            @foreach($parentAccounts as $account)
                                @include('tenant.accounting.ledger-accounts.partials.account-node', ['account' => $account, 'level' => 0, 'allAccounts' => $groupAccounts])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @empty
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No ledger accounts found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'account_type', 'account_group_id', 'is_active']))
                    No accounts match your current filters.
                @else
                    Get started by creating your first ledger account.
                @endif
            </p>
            <div class="mt-6">
                @if(request()->hasAny(['search', 'account_type', 'account_group_id', 'is_active']))
                    <a href="{{ route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug, 'view' => 'tree']) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                        Clear Filters
                    </a>
                @else
                    <a href="{{ route('tenant.accounting.ledger-accounts.create', $tenant) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create First Account
                    </a>
                @endif
            </div>
        </div>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tree state
    initializeTreeState();
    
    // Auto-expand first level by default
    document.querySelectorAll('.group-toggle').forEach(button => {
        toggleGroup(button);
    });
});

// Function to toggle account group visibility
function toggleGroup(button) {
    const groupHeader = button.closest('.group-header');
    const groupAccounts = groupHeader.nextElementSibling;
    const icon = button.querySelector('svg');
    
    if (groupAccounts.classList.contains('hidden')) {
        groupAccounts.classList.remove('hidden');
        button.classList.add('expanded');
        icon.style.transform = 'rotate(90deg)';
    } else {
        groupAccounts.classList.add('hidden');
        button.classList.remove('expanded');
        icon.style.transform = 'rotate(0deg)';
    }
    
    // Save state to localStorage
    saveTreeState();
}

// Function to toggle individual account visibility
function toggleAccount(button) {
    const accountNode = button.closest('.account-node');
    const childAccounts = accountNode.querySelector('.child-accounts');
    const icon = button.querySelector('svg');
    
    if (childAccounts && childAccounts.classList.contains('hidden')) {
        childAccounts.classList.remove('hidden');
        button.classList.add('expanded');
        icon.style.transform = 'rotate(90deg)';
    } else if (childAccounts) {
        childAccounts.classList.add('hidden');
        button.classList.remove('expanded');
        icon.style.transform = 'rotate(0deg)';
    }
    
    // Save state to localStorage
    saveTreeState();
}

// Function to save tree state to localStorage
function saveTreeState() {
    const expandedGroups = [];
    const expandedAccounts = [];
    
    // Save expanded groups
    document.querySelectorAll('.group-toggle.expanded').forEach(button => {
        const groupHeader = button.closest('.group-header');
        const groupName = groupHeader.querySelector('h3').textContent.trim();
        expandedGroups.push(groupName);
    });
    
    // Save expanded accounts
    document.querySelectorAll('.account-toggle.expanded').forEach(button => {
        const accountNode = button.closest('.account-node');
        const accountId = accountNode.getAttribute('data-account-id');
        if (accountId) {
            expandedAccounts.push(accountId);
        }
    });
    
    localStorage.setItem('accounting-tree-groups', JSON.stringify(expandedGroups));
    localStorage.setItem('accounting-tree-accounts', JSON.stringify(expandedAccounts));
}

// Function to restore tree state from localStorage
function initializeTreeState() {
    try {
        const expandedGroups = JSON.parse(localStorage.getItem('accounting-tree-groups') || '[]');
        const expandedAccounts = JSON.parse(localStorage.getItem('accounting-tree-accounts') || '[]');
        
        // Restore group states
        expandedGroups.forEach(groupName => {
            const groupHeaders = document.querySelectorAll('.group-header h3');
            groupHeaders.forEach(header => {
                if (header.textContent.trim() === groupName) {
                    const button = header.closest('.group-header').querySelector('.group-toggle');
                    if (button) {
                        toggleGroup(button);
                    }
                }
            });
        });
        
        // Restore account states
        expandedAccounts.forEach(accountId => {
            const accountNode = document.querySelector(`[data-account-id="${accountId}"]`);
            if (accountNode) {
                const button = accountNode.querySelector('.account-toggle');
                if (button) {
                    toggleAccount(button);
                }
            }
        });
        
    } catch (error) {
        console.warn('Failed to restore tree state:', error);
    }
}

// Function to confirm account deletion
function confirmDelete(accountId, accountName) {
    if (confirm(`Are you sure you want to delete the account "${accountName}"? This action cannot be undone.`)) {
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}/${accountId}`;

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';

        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Function to expand all groups and accounts
function expandAll() {
    document.querySelectorAll('.group-toggle:not(.expanded)').forEach(button => {
        toggleGroup(button);
    });
    
    document.querySelectorAll('.account-toggle:not(.expanded)').forEach(button => {
        toggleAccount(button);
    });
}

// Function to collapse all groups and accounts
function collapseAll() {
    document.querySelectorAll('.account-toggle.expanded').forEach(button => {
        toggleAccount(button);
    });
    
    document.querySelectorAll('.group-toggle.expanded').forEach(button => {
        toggleGroup(button);
    });
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'e':
                e.preventDefault();
                expandAll();
                break;
            case 'c':
                e.preventDefault();
                collapseAll();
                break;
        }
    }
});
</script>



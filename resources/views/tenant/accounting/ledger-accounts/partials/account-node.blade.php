@php
    $indentClass = match($level) {
        0 => 'ml-0',
        1 => 'ml-6',
        2 => 'ml-12',
        3 => 'ml-18',
        default => 'ml-24'
    };
    
    $children = $allAccounts->where('parent_id', $account->id);
    $hasChildren = $children->count() > 0;
    
    $balance = $account->getCurrentBalance();
    $balanceClass = $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-slate-500');
@endphp

<div class="account-node {{ $indentClass }} mb-2" data-account-id="{{ $account->id }}">
    <!-- Account Row -->
    <div class="account-row bg-white border border-slate-200 rounded-lg px-4 py-3 hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 flex-1">
                <!-- Expand/Collapse Button -->
                @if($hasChildren)
                    <button class="account-toggle p-1 rounded hover:bg-slate-100 transition-colors" 
                            type="button" onclick="toggleAccount(this)">
                        <svg class="w-3 h-3 text-slate-500 transition-transform duration-200" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @else
                    <div class="w-5 h-5 flex items-center justify-center">
                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                    </div>
                @endif

                <!-- Account Icon -->
                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $level === 0 ? 'bg-blue-100' : 'bg-slate-100' }}">
                    @if($level === 0)
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0v-3.5a1.5 1.5 0 013 0V21m-4-3h4"></path>
                        </svg>
                    @else
                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    @endif
                </div>

                <!-- Account Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3">
                        <!-- Account Code -->
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-medium {{ $level === 0 ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ $account->code }}
                        </span>
                        
                        <!-- Account Name -->
                        <h4 class="font-medium text-slate-900 truncate {{ $level === 0 ? 'text-base' : 'text-sm' }}">
                            <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $account]) }}" 
                               class="hover:text-blue-600 transition-colors">
                                {{ $account->name }}
                            </a>
                        </h4>

                        <!-- Status Badge -->
                        @if(!$account->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif

                        <!-- Account Type Badge -->
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $account->account_type === 'asset' ? 'bg-green-100 text-green-800' :
                               ($account->account_type === 'liability' ? 'bg-red-100 text-red-800' :
                               ($account->account_type === 'equity' ? 'bg-yellow-100 text-yellow-800' :
                               ($account->account_type === 'income' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'))) }}">
                            {{ ucfirst($account->account_type) }}
                        </span>
                    </div>

                    <!-- Account Description -->
                    @if($account->description && $level === 0)
                        <p class="mt-1 text-xs text-slate-500 truncate max-w-md">{{ $account->description }}</p>
                    @endif
                </div>

                <!-- Balance Display -->
                <div class="text-right">
                    <div class="text-sm font-semibold {{ $balanceClass }}">
                        ₦{{ number_format(abs($balance), 2) }}
                    </div>
                    <div class="text-xs text-slate-500">
                        {{ $balance >= 0 ? 'Dr' : 'Cr' }}
                    </div>
                </div>

                <!-- Actions (Show on Hover) -->
                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity ml-3">
                    <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $account]) }}"
                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                       title="View Account">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('tenant.accounting.ledger-accounts.edit', [$tenant, $account]) }}"
                       class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                       title="Edit Account">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <button type="button"
                            onclick="confirmDelete('{{ $account->id }}', '{{ $account->name }}')"
                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                            title="Delete Account">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Child Accounts -->
    @if($hasChildren)
        <div class="child-accounts mt-2 ml-4 space-y-2 hidden">
            @foreach($children as $child)
                @include('tenant.accounting.ledger-accounts.partials.account-node', [
                    'account' => $child, 
                    'level' => $level + 1, 
                    'allAccounts' => $allAccounts
                ])
            @endforeach
        </div>
    @endif
</div>

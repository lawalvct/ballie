{{-- Chart of Accounts - Columnar View by Account Groups --}}
@php
    // Group accounts by their account group
    $groupedAccounts = $accounts->groupBy('account_group_id');

    // Get all account groups that have accounts
    $activeGroups = $accountGroups->filter(function($group) use ($groupedAccounts) {
        return $groupedAccounts->has($group->id);
    });

    // Find the maximum number of accounts in any group (for row count)
    $maxAccounts = $groupedAccounts->map->count()->max() ?? 0;
@endphp

@if($activeGroups->isEmpty())
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No accounts found</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new ledger account.</p>
        <div class="mt-6">
            <a href="{{ route('tenant.accounting.ledger-accounts.create', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Account
            </a>
        </div>
    </div>
@else
    <div class="chart-of-accounts-container">
        <table class="chart-table min-w-full">
            <thead>
                <tr>
                    <th class="sn-column sticky left-0 z-20 bg-gray-100 border border-gray-300 px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                        S/N
                    </th>
                    @foreach($activeGroups as $group)
                        <th class="group-header-cell border border-gray-300 px-4 py-3 text-left text-xs font-bold uppercase tracking-wider min-w-[250px] {{
                            match($group->nature) {
                                'asset' => 'bg-blue-100 text-blue-800',
                                'liability' => 'bg-red-100 text-red-800',
                                'equity' => 'bg-yellow-100 text-yellow-800',
                                'income' => 'bg-green-100 text-green-800',
                                'expense' => 'bg-purple-100 text-purple-800',
                                default => 'bg-gray-100 text-gray-800'
                            }
                        }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="block">{{ $group->name }}</span>
                                    <span class="block text-[10px] font-normal opacity-75 capitalize">{{ $group->nature }}</span>
                                </div>
                                <span class="text-xs font-normal bg-white/50 px-2 py-0.5 rounded">
                                    {{ $groupedAccounts[$group->id]->count() }} accounts
                                </span>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < $maxAccounts; $i++)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100">
                        {{-- Serial Number --}}
                        <td class="sn-column sticky left-0 z-10 border border-gray-200 px-3 py-2 text-center text-sm font-medium text-gray-500 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            {{ $i + 1 }}
                        </td>
                        @foreach($activeGroups as $group)
                            @php
                                $groupAccounts = $groupedAccounts[$group->id] ?? collect();
                                $account = $groupAccounts->values()->get($i);
                            @endphp
                            <td class="border border-gray-200 px-4 py-2 min-w-[250px]">
                                @if($account)
                                    <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $account]) }}"
                                       class="block hover:bg-{{ match($group->nature) { 'asset' => 'blue', 'liability' => 'red', 'equity' => 'yellow', 'income' => 'green', 'expense' => 'purple', default => 'gray' } }}-50 rounded p-1 -m-1 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-gray-500 font-mono">{{ $account->code }}</span>
                                                    @if($account->is_system_account)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-200 text-gray-700">
                                                            System
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm font-medium text-gray-900 truncate" title="{{ $account->name }}">
                                                    {{ $account->name }}
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-shrink-0 text-right">
                                                @php
                                                    $balance = $account->current_balance ?? 0;
                                                @endphp
                                                <span class="text-sm font-semibold {{ $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                                    {{ number_format(abs($balance), 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endfor
                {{-- Group Totals Row --}}
                <tr class="bg-gray-200 font-semibold">
                    <td class="sticky left-0 z-10 bg-gray-200 border border-gray-300 px-3 py-3 text-center text-sm font-bold text-gray-700">
                        Total
                    </td>
                    @foreach($activeGroups as $group)
                        @php
                            $groupAccounts = $groupedAccounts[$group->id] ?? collect();
                            $groupTotal = $groupAccounts->sum('current_balance');
                        @endphp
                        <td class="border border-gray-300 px-4 py-3 text-right {{
                            match($group->nature) {
                                'asset' => 'bg-blue-50',
                                'liability' => 'bg-red-50',
                                'equity' => 'bg-yellow-50',
                                'income' => 'bg-green-50',
                                'expense' => 'bg-purple-50',
                                default => 'bg-gray-100'
                            }
                        }}">
                            <span class="text-sm font-bold {{ $groupTotal > 0 ? 'text-green-700' : ($groupTotal < 0 ? 'text-red-700' : 'text-gray-600') }}">
                                {{ number_format(abs($groupTotal), 2) }}
                            </span>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <style>
        .chart-of-accounts-container {
            overflow-x: auto;
            max-width: 100%;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .chart-of-accounts-container::-webkit-scrollbar {
            height: 10px;
        }

        .chart-of-accounts-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 5px;
        }

        .chart-of-accounts-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 5px;
        }

        .chart-of-accounts-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .chart-table {
            border-collapse: collapse;
            table-layout: fixed;
        }

        .sn-column {
            width: 60px;
            min-width: 60px;
        }

        .group-header-cell {
            white-space: nowrap;
        }

        /* Print styles */
        @media print {
            .chart-of-accounts-container {
                overflow: visible;
            }

            .sn-column {
                position: relative !important;
            }

            .chart-table {
                font-size: 10px;
            }

            .chart-table th,
            .chart-table td {
                padding: 4px 8px;
            }
        }
    </style>
@endif

@if($accounts->count() > 0)


    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Account
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Group
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Parent
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Balance
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($accounts as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full {{ $account->account_type === 'asset' ? 'bg-green-100' : ($account->account_type === 'liability' ? 'bg-red-100' : ($account->account_type === 'equity' ? 'bg-yellow-100' : ($account->account_type === 'income' ? 'bg-blue-100' : 'bg-gray-100'))) }} flex items-center justify-center">
                                        <span class="text-sm font-medium {{ $account->account_type === 'asset' ? 'text-green-600' : ($account->account_type === 'liability' ? 'text-red-600' : ($account->account_type === 'equity' ? 'text-yellow-600' : ($account->account_type === 'income' ? 'text-blue-600' : 'text-gray-600'))) }}">
                                            {{ strtoupper(substr($account->account_type, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $account->code }}
                                        </span>
                                        <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $account]) }}"
                                           class="text-sm font-medium text-gray-900 hover:text-primary-600">
                                            {{ $account->name }}
                                        </a>
                                    </div>
                                    @if($account->description)
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ Str::limit($account->description, 50) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $account->account_type === 'asset' ? 'bg-green-100 text-green-800' :
                                   ($account->account_type === 'liability' ? 'bg-red-100 text-red-800' :
                                   ($account->account_type === 'equity' ? 'bg-yellow-100 text-yellow-800' :
                                   ($account->account_type === 'income' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($account->account_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $account->accountGroup->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($account->parent)
                                {{ $account->parent->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            @php
                                $balance = $account->getCurrentBalance();
                                $balanceClass = $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-500');
                            @endphp
                            <span class="{{ $balanceClass }} font-medium">
                                {{ number_format(abs($balance), 2) }}
                                <small class="text-xs">{{ $balance >= 0 ? 'Dr' : 'Cr' }}</small>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($account->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $account]) }}"
                                   class="text-primary-600 hover:text-primary-900">
                                    View
                                </a>
                                <a href="{{ route('tenant.accounting.ledger-accounts.edit', [$tenant, $account]) }}"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="confirmDelete('{{ $account->id }}', '{{ $account->name }}')"
                                        class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($accounts, 'links') && $accounts->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $accounts->appends(request()->query())->links() }}
        </div>
    @endif
@else
    <!-- Empty State -->
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
                <a href="{{ route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug, 'view' => 'list']) }}"
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
@endif

<script>
function confirmDelete(accountId, accountName) {
    if (confirm(`Are you sure you want to delete the account "${accountName}"?`)) {
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
</script>

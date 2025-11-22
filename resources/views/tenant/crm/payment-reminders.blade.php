@extends('layouts.tenant')

@section('title', 'Payment Reminders')
@section('page-title', 'Payment Reminders')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4 border border-green-200">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customers with Outstanding Balance</h3>
        
        <form method="POST" action="{{ route('tenant.crm.payment-reminders.send', ['tenant' => $tenant->slug]) }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="form-checkbox h-4 w-4 text-blue-600 rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outstanding Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($customers as $customer)
                        <tr>
                            <td class="px-6 py-4">
                                <input type="checkbox" name="customers[]" value="{{ $customer->id }}" class="customer-checkbox form-checkbox h-4 w-4 text-blue-600 rounded">
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $customer->customer_type == 'individual' ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $customer->email }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-orange-600">
                                ₦{{ number_format($customer->ledgerAccount->getCurrentBalance(), 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No customers with outstanding balance</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->count() > 0)
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Send Reminders
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection

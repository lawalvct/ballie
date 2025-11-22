@extends('layouts.tenant')

@section('title', 'Purchase Orders - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Purchase Orders (LPO)</h1>
            <p class="mt-2 text-gray-600">Manage local purchase orders</p>
        </div>
        <a href="{{ route('tenant.procurement.purchase-orders.create', $tenant->slug) }}"
           class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create LPO
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">LPO Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purchaseOrders as $po)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $po->lpo_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $po->vendor->getFullNameAttribute() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $po->lpo_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($po->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($po->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($po->status === 'sent') bg-blue-100 text-blue-800
                                    @elseif($po->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($po->status === 'received') bg-purple-100 text-purple-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('tenant.procurement.purchase-orders.show', [$tenant->slug, $po->id]) }}"
                                   class="text-orange-600 hover:text-orange-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No purchase orders found. <a href="{{ route('tenant.procurement.purchase-orders.create', $tenant->slug) }}" class="text-orange-600 hover:text-orange-900">Create one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($purchaseOrders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

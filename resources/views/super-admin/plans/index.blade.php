@extends('layouts.super-admin')

@section('title', 'Plans & Pricing')
@section('page-title', 'Plans & Pricing')
@section('page-description', 'Manage subscription plans and pricing tiers')

@section('content')
<div class="max-w-full space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Plans</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-1"></div>
        </div>
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Active Plans</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active'] }}</p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 h-1"></div>
        </div>
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Inactive Plans</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-gray-400 to-gray-500 h-1"></div>
        </div>
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Popular Plans</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['popular'] }}</p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 h-1"></div>
        </div>
    </div>

    <!-- Header Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-initial">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <form method="GET" action="{{ route('super-admin.plans.index') }}" class="flex">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search plans..."
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                    </form>
                </div>
            </div>
            <a href="{{ route('super-admin.plans.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Plan
            </a>
        </div>
    </div>

    <!-- Plans Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plan</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Limits</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden xl:table-cell">Features</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($plans as $plan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Plan Name -->
                        <td class="px-3 py-3">
                            <div class="text-sm font-semibold text-gray-900">{{ $plan->name }}</div>
                            @if($plan->is_popular)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800 mt-1">
                                    <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    Popular
                                </span>
                            @endif
                            <div class="text-xs text-gray-500 mt-0.5 lg:hidden">{{ $plan->max_users }} users</div>
                        </td>
                        <!-- Price -->
                        <td class="px-3 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $plan->formatted_monthly_price }}</div>
                            <div class="text-xs text-gray-500">/month</div>
                        </td>
                        <!-- Limits -->
                        <td class="px-3 py-3 text-xs text-gray-600 hidden lg:table-cell">
                            <div>{{ $plan->max_users }} users</div>
                            <div class="text-gray-500">{{ $plan->max_customers == 0 ? 'Unlimited' : number_format($plan->max_customers) }} customers</div>
                        </td>
                        <!-- Features -->
                        <td class="px-3 py-3 hidden xl:table-cell">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @if($plan->has_pos) <span class="inline-block px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">POS</span> @endif
                                @if($plan->has_payroll) <span class="inline-block px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded">Payroll</span> @endif
                                @if($plan->has_ecommerce) <span class="inline-block px-1.5 py-0.5 bg-purple-100 text-purple-700 text-xs rounded">E-com</span> @endif
                                @if($plan->has_api_access) <span class="inline-block px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-xs rounded">API</span> @endif
                            </div>
                        </td>
                        <!-- Status -->
                        <td class="px-3 py-3">
                            <button onclick="togglePlanStatus({{ $plan->id }}, this)"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium cursor-pointer transition-colors {{ $plan->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1 {{ $plan->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                <span class="hidden sm:inline">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                            </button>
                        </td>
                        <!-- Actions -->
                        <td class="px-3 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('super-admin.plans.edit', $plan) }}"
                                   class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete the {{ $plan->name }} plan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <p class="mt-2 text-sm text-gray-500">No plans found.</p>
                            <a href="{{ route('super-admin.plans.create') }}" class="mt-3 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Create your first plan
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function togglePlanStatus(planId, btn) {
    try {
        const response = await fetch(`/super-admin/plans/${planId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            const dot = btn.querySelector('span');
            if (data.is_active) {
                btn.className = btn.className.replace('bg-gray-100 text-gray-600 hover:bg-gray-200', 'bg-green-100 text-green-800 hover:bg-green-200');
                dot.className = dot.className.replace('bg-gray-400', 'bg-green-500');
                btn.lastChild.textContent = 'Active';
            } else {
                btn.className = btn.className.replace('bg-green-100 text-green-800 hover:bg-green-200', 'bg-gray-100 text-gray-600 hover:bg-gray-200');
                dot.className = dot.className.replace('bg-green-500', 'bg-gray-400');
                btn.lastChild.textContent = 'Inactive';
            }
        }
    } catch (e) {
        console.error('Toggle failed:', e);
    }
}
</script>
@endpush

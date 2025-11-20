@extends('layouts.super-admin')

@section('title', 'Affiliate Details - ' . $affiliate->user->name)
@section('page-title', 'Affiliate Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('super-admin.affiliates.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Affiliates
        </a>
    </div>

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-8 py-8 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-20 w-20 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white font-bold text-2xl mr-6">
                        {{ substr($affiliate->user->name, 0, 2) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $affiliate->user->name }}</h1>
                        <p class="text-blue-100 mt-1">{{ $affiliate->user->email }}</p>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="px-3 py-1 text-xs font-mono font-semibold bg-white/20 backdrop-blur-md rounded-full">
                                {{ $affiliate->affiliate_code }}
                            </span>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-500',
                                    'active' => 'bg-green-500',
                                    'suspended' => 'bg-red-500',
                                    'rejected' => 'bg-gray-500',
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$affiliate->status] ?? 'bg-gray-500' }} text-white">
                                {{ ucfirst($affiliate->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-100">Member Since</p>
                    <p class="text-xl font-semibold">{{ $affiliate->created_at->format('M d, Y') }}</p>
                    @if($affiliate->approved_at)
                    <p class="text-xs text-blue-200 mt-1">Approved: {{ $affiliate->approved_at->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-3">
        @if($affiliate->status === 'pending')
        <form method="POST" action="{{ route('super-admin.affiliates.approve', $affiliate) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Approve Affiliate
            </button>
        </form>
        <button onclick="showRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Reject
        </button>
        @endif

        @if($affiliate->status === 'active')
        <button onclick="showSuspendModal()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            Suspend
        </button>
        @endif

        @if($affiliate->status === 'suspended')
        <form method="POST" action="{{ route('super-admin.affiliates.reactivate', $affiliate) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Reactivate
            </button>
        </form>
        @endif

        <a href="{{ route('super-admin.affiliates.edit', $affiliate) }}" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Details
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-600">Total Referrals</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($activityStats['total_referrals']) }}</p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-600 font-medium">{{ $activityStats['confirmed_referrals'] }} confirmed</span>
                <span class="text-gray-400 mx-2">•</span>
                <span class="text-yellow-600 font-medium">{{ $activityStats['pending_referrals'] }} pending</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-600">Total Earnings</p>
            <p class="text-3xl font-bold text-green-600 mt-2">₦{{ number_format($activityStats['total_commissions'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $affiliate->getCommissionRate() }}% commission rate</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <p class="text-sm font-medium text-gray-600">Total Paid</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">₦{{ number_format($affiliate->total_paid, 2) }}</p>
            @if($affiliate->last_payout_at)
            <p class="text-xs text-gray-500 mt-2">Last: {{ $affiliate->last_payout_at->format('M d, Y') }}</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-600">Current Balance</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">₦{{ number_format($activityStats['balance'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">
                ₦{{ number_format($activityStats['approved_commissions'], 2) }} approved
            </p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Profile Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Company Name</label>
                    <p class="text-gray-900 mt-1">{{ $affiliate->company_name }}</p>
                </div>
                @if($affiliate->phone)
                <div>
                    <label class="text-sm font-medium text-gray-500">Phone</label>
                    <p class="text-gray-900 mt-1">{{ $affiliate->phone }}</p>
                </div>
                @endif
                @if($affiliate->bio)
                <div>
                    <label class="text-sm font-medium text-gray-500">Bio</label>
                    <p class="text-gray-900 mt-1">{{ $affiliate->bio }}</p>
                </div>
                @endif
                <div>
                    <label class="text-sm font-medium text-gray-500">Commission Rate</label>
                    <p class="text-gray-900 mt-1">{{ $affiliate->getCommissionRate() }}%</p>
                </div>
                @if($affiliate->payment_details)
                <div>
                    <label class="text-sm font-medium text-gray-500">Payment Details</label>
                    <div class="mt-1 bg-gray-50 rounded p-3 text-sm">
                        @foreach($affiliate->payment_details as $key => $value)
                        <div class="flex justify-between py-1">
                            <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                            <span class="text-gray-900 font-medium">
                                @if(is_array($value))
                                    {{ implode(', ', array_filter($value)) }}
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Referrals -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Referrals</h3>
                @if($affiliate->referrals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($affiliate->referrals as $referral)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $referral->tenant->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">₦{{ number_format($referral->conversion_value, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $referral->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($referral->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $referral->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No referrals yet</p>
                @endif
            </div>

            <!-- Recent Commissions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Commissions</h3>
                @if($affiliate->commissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($affiliate->commissions as $commission)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $commission->description ?? 'Commission' }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">₦{{ number_format($commission->commission_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$commission->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $commission->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No commissions yet</p>
                @endif
            </div>

            <!-- Recent Payouts -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Payouts</h3>
                @if($affiliate->payouts->count() > 0)
                <div class="space-y-3">
                    @foreach($affiliate->payouts as $payout)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">₦{{ number_format($payout->net_amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $payout->payout_method }} - {{ $payout->completed_at ? $payout->completed_at->format('M d, Y') : 'Pending' }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $payout->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($payout->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No payouts yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Affiliate</h3>
        <form method="POST" action="{{ route('super-admin.affiliates.reject', $affiliate) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                <textarea name="rejection_reason" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Explain why this affiliate is being rejected..."></textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Reject
                </button>
                <button type="button" onclick="hideRejectModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Suspend Affiliate</h3>
        <form method="POST" action="{{ route('super-admin.affiliates.suspend', $affiliate) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Suspension Reason</label>
                <textarea name="suspension_reason" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Explain why this affiliate is being suspended..."></textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Suspend
                </button>
                <button type="button" onclick="hideSuspendModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function showSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
}

function hideSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}
</script>
@endsection

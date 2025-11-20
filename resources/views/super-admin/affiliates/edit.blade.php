@extends('layouts.super-admin')

@section('title', 'Edit Affiliate - ' . $affiliate->user->name)
@section('page-title', 'Edit Affiliate')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('super-admin.affiliates.show', $affiliate) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Affiliate Details
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Affiliate Settings</h1>
            <p class="text-gray-600 mt-1">Update commission rates, status, and other settings for {{ $affiliate->user->name }}</p>
        </div>

        <form method="POST" action="{{ route('super-admin.affiliates.update', $affiliate) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Affiliate Info (Read-only) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-200">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Affiliate Name</label>
                    <input type="text" value="{{ $affiliate->user->name }}" disabled class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" value="{{ $affiliate->user->email }}" disabled class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Affiliate Code</label>
                    <input type="text"
                           name="affiliate_code"
                           value="{{ $affiliate->affiliate_code }}"
                           required
                           maxlength="20"
                           pattern="[A-Za-z0-9]+"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono uppercase"
                           placeholder="Enter meaningful affiliate code"
                           oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                    <p class="text-sm text-gray-500 mt-1">
                        Must be unique, alphanumeric only (max 20 characters).
                        <span class="text-blue-600">Current: {{ $affiliate->affiliate_code }}</span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input type="text" value="{{ $affiliate->company_name }}" disabled class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                </div>
            </div>

            <!-- Editable Fields -->
            <div class="space-y-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pending" {{ $affiliate->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ $affiliate->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ $affiliate->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="rejected" {{ $affiliate->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Current status: <span class="font-medium">{{ ucfirst($affiliate->status) }}</span></p>
                </div>

                <!-- Custom Commission Rate -->
                <div>
                    <label for="custom_commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        Custom Commission Rate (%)
                    </label>
                    <input type="number"
                           name="custom_commission_rate"
                           id="custom_commission_rate"
                           value="{{ $affiliate->custom_commission_rate }}"
                           step="0.01"
                           min="0"
                           max="100"
                           placeholder="Leave empty to use default rate"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">
                        Leave empty to use the default commission rate.
                        @if($affiliate->custom_commission_rate)
                            Current custom rate: <span class="font-medium">{{ $affiliate->custom_commission_rate }}%</span>
                        @else
                            Currently using default rate: <span class="font-medium">{{ config('affiliate.default_commission_rate', 10) }}%</span>
                        @endif
                    </p>
                </div>

                <!-- Notes (Optional) -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Admin Notes (Internal)
                    </label>
                    <textarea name="notes"
                              id="notes"
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Add any internal notes about this affiliate..."></textarea>
                    <p class="text-sm text-gray-500 mt-1">These notes are only visible to super admins.</p>
                </div>
            </div>

            <!-- Current Stats (Read-only) -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600 font-medium">Total Referrals</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ number_format($affiliate->total_referrals) }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600 font-medium">Total Commissions</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">₦{{ number_format($affiliate->total_commissions, 2) }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-purple-600 font-medium">Total Paid</p>
                        <p class="text-2xl font-bold text-purple-900 mt-1">₦{{ number_format($affiliate->total_paid, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('super-admin.affiliates.show', $affiliate) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-red-900 mb-2">Danger Zone</h3>
        <p class="text-sm text-red-700 mb-4">These actions are permanent and cannot be undone.</p>

        <div class="space-y-3">
            @if($affiliate->status === 'pending')
            <form method="POST" action="{{ route('super-admin.affiliates.reject', $affiliate) }}" onsubmit="return confirm('Are you sure you want to reject this affiliate? This action cannot be undone.');">
                @csrf
                <input type="hidden" name="rejection_reason" value="Rejected by admin">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Reject Affiliate Application
                </button>
            </form>
            @endif

            @if($affiliate->status === 'active')
            <form method="POST" action="{{ route('super-admin.affiliates.suspend', $affiliate) }}" onsubmit="return confirm('Are you sure you want to suspend this affiliate?');">
                @csrf
                <input type="hidden" name="suspension_reason" value="Suspended by admin">
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    Suspend Affiliate Account
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

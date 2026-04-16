@extends('layouts.tenant-onboarding')

@section('title', 'Chart of Accounts - Ballie Setup')

@section('content')
<!-- Progress Steps -->
<div class="mb-10">
    <div class="flex items-center justify-center">
        <div class="flex items-center space-x-3 md:space-x-6 overflow-x-auto pb-2">
            @foreach (['Company Info', 'Preferences', 'Accounts', 'Complete'] as $i => $label)
            @if ($i > 0)
            <div class="w-6 md:w-12 h-0.5 {{ $i <= 1 ? 'bg-green-400' : ($i == 2 ? 'bg-brand-blue' : 'bg-gray-200') }} rounded hidden sm:block"></div>
            @endif
            <div class="flex items-center flex-shrink-0">
                @if ($i < 2)
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                @elseif ($i == 2)
                <div class="w-8 h-8 bg-brand-blue text-white rounded-full flex items-center justify-center font-semibold shadow-sm">
                    3
                </div>
                @else
                <div class="w-8 h-8 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center font-semibold">
                    4
                </div>
                @endif
                <span class="ml-2 text-xs font-medium {{ $i < 2 ? 'text-green-600' : ($i == 2 ? 'text-brand-blue' : 'text-gray-400') }} whitespace-nowrap hidden sm:inline">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data="accountSelector()">
    <!-- Header -->
    <div class="bg-gradient-to-br from-brand-teal to-brand-blue text-white p-6 md:p-8">
        <div class="text-center">
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Chart of Accounts</h2>
            <p class="text-blue-100 max-w-lg mx-auto">Select the ledger accounts for your business. Core accounts are required and pre-selected.</p>

            <!-- Category Badge -->
            <div class="mt-3">
                @php
                    $categoryColors = [
                        'trading' => 'bg-blue-500 bg-opacity-30 text-blue-100',
                        'manufacturing' => 'bg-purple-500 bg-opacity-30 text-purple-100',
                        'service' => 'bg-green-500 bg-opacity-30 text-green-100',
                        'hybrid' => 'bg-amber-500 bg-opacity-30 text-amber-100',
                    ];
                    $categoryLabels = [
                        'trading' => '🏪 Trading Business',
                        'manufacturing' => '🏭 Manufacturing Business',
                        'service' => '💼 Service Business',
                        'hybrid' => '🔄 Hybrid Business',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $categoryColors[$businessCategory] ?? $categoryColors['hybrid'] }}">
                    {{ $categoryLabels[$businessCategory] ?? $categoryLabels['hybrid'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Selection Summary Bar -->
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-3 flex flex-wrap items-center justify-between gap-2">
        <div class="text-sm text-gray-600">
            <span class="font-semibold text-brand-blue" x-text="selectedCount"></span> accounts selected
            (<span x-text="coreCount"></span> core +
            <span x-text="selectedCount - coreCount"></span> optional)
        </div>
        <div class="flex items-center gap-4 text-xs">
            <span class="inline-flex items-center gap-1">
                <span class="w-3 h-3 bg-gray-300 rounded"></span> 🔒 Core (required)
            </span>
            <span class="inline-flex items-center gap-1">
                <span class="w-3 h-3 bg-green-300 rounded"></span> ★ Recommended
            </span>
            <span class="inline-flex items-center gap-1">
                <span class="w-3 h-3 bg-white border border-gray-300 rounded"></span> Optional
            </span>
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-6 md:p-8">
        <form method="POST" action="{{ route('tenant.onboarding.save-step', ['tenant' => $currentTenant->slug, 'step' => 'accounts']) }}" id="accounts-form-main" @submit="loading = true">
            @csrf

            @if(session('error'))
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-lg border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @error('selected_accounts')
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-lg border border-red-200">
                    {{ $message }}
                </div>
            @enderror

            <!-- Account Groups -->
            <div class="space-y-4">
                @php
                    $groupIcons = [
                        'Current Asset' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>',
                        'Fixed Asset' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>',
                        'Current Liability' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
                        'Direct Income' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>',
                        'Indirect Income' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                        'Direct Expense' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m0 0l6-6m-6 6l6 6"></path>',
                        'Indirect Expense' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"></path>',
                        'Capital Account' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>',
                    ];
                    $groupColors = [
                        'Current Asset' => 'text-emerald-600',
                        'Fixed Asset' => 'text-blue-600',
                        'Current Liability' => 'text-red-500',
                        'Direct Income' => 'text-green-600',
                        'Indirect Income' => 'text-teal-600',
                        'Direct Expense' => 'text-orange-500',
                        'Indirect Expense' => 'text-amber-600',
                        'Capital Account' => 'text-indigo-600',
                    ];
                    $groupBgs = [
                        'Current Asset' => 'bg-emerald-50 border-emerald-200',
                        'Fixed Asset' => 'bg-blue-50 border-blue-200',
                        'Current Liability' => 'bg-red-50 border-red-200',
                        'Direct Income' => 'bg-green-50 border-green-200',
                        'Indirect Income' => 'bg-teal-50 border-teal-200',
                        'Direct Expense' => 'bg-orange-50 border-orange-200',
                        'Indirect Expense' => 'bg-amber-50 border-amber-200',
                        'Capital Account' => 'bg-indigo-50 border-indigo-200',
                    ];
                @endphp

                @foreach($accountCatalog as $group => $accounts)
                    @php
                        $groupKey = \Illuminate\Support\Str::slug($group, '_');
                        $totalInGroup = count($accounts);
                        $coreInGroup = collect($accounts)->where('is_core', true)->count();
                        $suggestedInGroup = collect($accounts)->where('is_suggested', true)->where('is_core', false)->count();
                    @endphp

                    <div class="border rounded-lg overflow-hidden {{ $groupBgs[$group] ?? 'bg-gray-50 border-gray-200' }}"
                         x-data="{ open: true }">
                        <!-- Group Header (Collapsible) -->
                        <button type="button"
                                @click="open = !open"
                                class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-white hover:bg-opacity-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ $groupColors[$group] ?? 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $groupIcons[$group] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>' !!}
                                </svg>
                                <div>
                                    <span class="font-semibold text-gray-900">{{ $group }}</span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        (<span x-text="getGroupSelectedCount('{{ $groupKey }}')"></span>/{{ $totalInGroup }} selected)
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <!-- Select / Deselect All Optional -->
                                @if($totalInGroup - $coreInGroup > 0)
                                    <span class="text-xs text-brand-blue cursor-pointer hover:underline"
                                          @click.stop="toggleGroupOptional('{{ $groupKey }}')">
                                        <span x-text="isGroupAllOptionalSelected('{{ $groupKey }}') ? 'Deselect Optional' : 'Select All'"></span>
                                    </span>
                                @endif
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                     :class="{ 'rotate-180': open }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </button>

                        <!-- Account Rows -->
                        <div x-show="open" x-collapse>
                            <div class="border-t {{ str_replace('bg-', 'border-', explode(' ', $groupBgs[$group] ?? 'border-gray-200')[1] ?? 'border-gray-200') }}">
                                @foreach($accounts as $account)
                                    @php
                                        $isCore = $account['is_core'];
                                        $isSuggested = $account['is_suggested'];
                                        $isDefault = in_array($account['code'], $defaultSelection);
                                    @endphp
                                    <label class="flex items-start gap-3 px-5 py-3 hover:bg-white hover:bg-opacity-60 transition-colors cursor-pointer border-b last:border-b-0 {{ str_replace('bg-', 'border-', explode(' ', $groupBgs[$group] ?? 'border-gray-200')[1] ?? 'border-gray-200') }}">
                                        <div class="pt-0.5">
                                            @if($isCore)
                                                {{-- Core: always checked, disabled, hidden input for form submission --}}
                                                <input type="checkbox"
                                                       class="w-4 h-4 rounded border-gray-300 text-gray-400 bg-gray-100 cursor-not-allowed"
                                                       checked disabled>
                                                <input type="hidden" name="selected_accounts[]" value="{{ $account['code'] }}">
                                            @else
                                                {{-- Optional: toggle via Alpine --}}
                                                <input type="checkbox"
                                                       name="selected_accounts[]"
                                                       value="{{ $account['code'] }}"
                                                       class="w-4 h-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue"
                                                       x-model="selected"
                                                       data-group="{{ $groupKey }}">
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-mono text-gray-400">{{ $account['code'] }}</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $account['name'] }}</span>
                                                @if($isCore)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                                        🔒 Core
                                                    </span>
                                                @elseif($isSuggested)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                        ★ Recommended
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $account['description'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 mt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                <div class="text-sm text-gray-400">
                    Step 3 of 4
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('tenant.onboarding.step', ['tenant' => $currentTenant->slug, 'step' => 'preferences']) }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit"
                            :disabled="loading"
                            :class="loading ? 'opacity-75 cursor-not-allowed' : 'hover:bg-brand-dark-purple hover:shadow-lg'"
                            class="inline-flex items-center px-8 py-3 bg-brand-blue text-white rounded-xl transition-all font-semibold shadow-md">
                        <svg x-show="loading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="loading ? 'Setting up...' : 'Complete Setup'"></span>
                        <svg x-show="!loading" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@php
    $jsDefaultOptional = array_values(array_diff($defaultSelection, $coreAccounts));
    $jsAllGroups = collect($accountCatalog)->map(function($accounts, $group) {
        return collect($accounts)->map(fn($a) => [
            'code'    => $a['code'],
            'group'   => \Illuminate\Support\Str::slug($group, '_'),
            'is_core' => $a['is_core'],
        ])->toArray();
    })->collapse()->values()->toArray();
@endphp
<script>
function accountSelector() {
    const coreCodes = @json($coreAccounts);
    const defaultOptional = @json($jsDefaultOptional);
    const allGroups = @json($jsAllGroups);

    return {
        // x-model array: only optional account codes
        selected: [...defaultOptional],
        coreCodes: coreCodes,
        allAccounts: allGroups,
        loading: false,

        get selectedCount() {
            // Total = core (always) + optional checked
            return this.coreCodes.length + this.selected.length;
        },
        get coreCount() {
            return this.coreCodes.length;
        },

        getGroupSelectedCount(groupKey) {
            const groupAccounts = this.allAccounts.filter(a => a.group === groupKey);
            const coreInGroup = groupAccounts.filter(a => a.is_core).length;
            const optionalSelected = groupAccounts
                .filter(a => !a.is_core)
                .filter(a => this.selected.includes(a.code)).length;
            return coreInGroup + optionalSelected;
        },

        isGroupAllOptionalSelected(groupKey) {
            const optional = this.allAccounts
                .filter(a => a.group === groupKey && !a.is_core);
            return optional.length > 0 && optional.every(a => this.selected.includes(a.code));
        },

        toggleGroupOptional(groupKey) {
            const optional = this.allAccounts
                .filter(a => a.group === groupKey && !a.is_core);
            const allSelected = this.isGroupAllOptionalSelected(groupKey);

            if (allSelected) {
                const optCodes = optional.map(a => a.code);
                this.selected = this.selected.filter(c => !optCodes.includes(c));
            } else {
                optional.forEach(a => {
                    if (!this.selected.includes(a.code)) {
                        this.selected.push(a.code);
                    }
                });
            }
        }
    };
}
</script>
@endpush

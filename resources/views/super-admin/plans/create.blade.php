@extends('layouts.super-admin')

@section('title', 'Create Plan')
@section('page-title', 'Create New Plan')
@section('page-description', 'Set up a new subscription plan with pricing and features')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('super-admin.plans.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Plans
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-medium text-red-800">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('super-admin.plans.store') }}">
        @include('super-admin.plans._form')
    </form>
</div>

@push('scripts')
<script>
    function formatKobo(kobo) {
        return '₦' + (kobo / 100).toLocaleString('en-NG', { minimumFractionDigits: 2 });
    }
    ['monthly', 'quarterly', 'biannual', 'yearly'].forEach(function(period) {
        const input = document.getElementById(period + '_price');
        const display = document.getElementById(period + '_display');
        if (input && display) {
            function update() { display.textContent = input.value ? formatKobo(parseInt(input.value) || 0) : ''; }
            input.addEventListener('input', update);
            update();
        }
    });
</script>
@endpush
@endsection

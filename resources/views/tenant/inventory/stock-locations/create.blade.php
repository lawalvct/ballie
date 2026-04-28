@extends('layouts.tenant')

@section('title', 'New Stock Location')
@section('page-title', 'New Stock Location')

@section('content')
<div class="max-w-2xl">
    @include('tenant.inventory.stock-locations._form', [
        'action' => route('tenant.inventory.stock-locations.store', ['tenant' => $tenant->slug]),
        'method' => 'POST',
        'location' => null,
        'types' => $types,
    ])
</div>
@endsection

@extends('layouts.tenant')

@section('title', 'Edit Stock Location')
@section('page-title', 'Edit Stock Location')

@section('content')
<div class="max-w-2xl">
    @include('tenant.inventory.stock-locations._form', [
        'action' => route('tenant.inventory.stock-locations.update', ['tenant' => $tenant->slug, 'stockLocation' => $stockLocation->id]),
        'method' => 'PUT',
        'location' => $stockLocation,
        'types' => $types,
    ])
</div>
@endsection

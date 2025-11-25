@extends('layouts.tenant')

@section('title', 'Edit PFA')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Edit PFA</h2>
        <a href="{{ route('tenant.payroll.pfas.index', ['tenant' => $tenant->slug]) }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <form action="{{ route('tenant.payroll.pfas.update', [$tenant, $pfa]) }}" method="POST" class="bg-white rounded-lg shadow-sm border p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">PFA Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $pfa->name) }}" required class="w-full px-3 py-2 border rounded-lg @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $pfa->code) }}" required class="w-full px-3 py-2 border rounded-lg @error('code') border-red-500 @enderror">
                @error('code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $pfa->contact_person) }}" class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $pfa->phone) }}" class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $pfa->email) }}" class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="is_active" class="w-full px-3 py-2 border rounded-lg">
                    <option value="1" {{ old('is_active', $pfa->is_active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $pfa->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
            <textarea name="address" rows="3" class="w-full px-3 py-2 border rounded-lg">{{ old('address', $pfa->address) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.payroll.pfas.index', ['tenant' => $tenant->slug]) }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Update PFA</button>
        </div>
    </form>
</div>
@endsection

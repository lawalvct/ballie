<!-- Register Name -->
<div>
    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
        Register Name <span class="text-red-500">*</span>
    </label>
    <input type="text"
           id="name"
           name="name"
           value="{{ old('name', $cashRegister->name ?? '') }}"
           required
           maxlength="255"
           placeholder="e.g., Counter 1, Drive-through, Express Lane"
           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    <p class="mt-1 text-xs text-gray-500">
        Choose a descriptive name to help identify this register
    </p>
</div>

<!-- Location -->
<div>
    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
        Location
    </label>
    <input type="text"
           id="location"
           name="location"
           value="{{ old('location', $cashRegister->location ?? '') }}"
           maxlength="500"
           placeholder="e.g., Main Floor, Second Floor, Front Desk"
           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror">
    @error('location')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    <p class="mt-1 text-xs text-gray-500">
        Optional: Specify where this register is physically located
    </p>
</div>

<!-- Opening Balance (Only on Create) -->
@if(!isset($edit))
<div>
    <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-2">
        Opening Balance <span class="text-red-500">*</span>
    </label>
    <div class="relative">
        <span class="absolute left-4 top-3 text-gray-500">₦</span>
        <input type="number"
               id="opening_balance"
               name="opening_balance"
               value="{{ old('opening_balance', '0.00') }}"
               required
               min="0"
               step="0.01"
               placeholder="0.00"
               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('opening_balance') border-red-500 @enderror">
    </div>
    @error('opening_balance')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    <p class="mt-1 text-xs text-gray-500">
        Initial cash float for this register (typically 0, but can be any amount)
    </p>
</div>
@else
<!-- Display Opening Balance as Read-Only on Edit -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Opening Balance
    </label>
    <div class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
        ₦{{ number_format($cashRegister->opening_balance ?? 0, 2) }}
    </div>
    <p class="mt-1 text-xs text-gray-500">
        Opening balance cannot be changed after creation
    </p>
</div>

<!-- Display Current Balance as Read-Only on Edit -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Current Balance
    </label>
    <div class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-900 font-medium">
        ₦{{ number_format($cashRegister->current_balance ?? 0, 2) }}
    </div>
    <p class="mt-1 text-xs text-gray-500">
        Current balance is automatically updated based on session transactions
    </p>
</div>
@endif

<!-- Active Status -->
<div class="flex items-start">
    <div class="flex items-center h-5">
        <input type="checkbox"
               id="is_active"
               name="is_active"
               value="1"
               {{ old('is_active', $cashRegister->is_active ?? true) ? 'checked' : '' }}
               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
    </div>
    <div class="ml-3">
        <label for="is_active" class="font-medium text-gray-700">
            Active
        </label>
        <p class="text-xs text-gray-500">
            Only active registers can be selected when opening new POS sessions
        </p>
    </div>
</div>

<!-- Help Text -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-2">Quick Guide:</p>
            <ul class="space-y-1 list-disc list-inside">
                <li>Name and opening balance are required fields</li>
                <li>Use clear, descriptive names for easy identification</li>
                <li>Location helps users find the correct physical register</li>
                <li>Inactive registers won't appear in the session selection dropdown</li>
            </ul>
        </div>
    </div>
</div>

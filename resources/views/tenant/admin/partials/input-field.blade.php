{{-- Input Field Component --}}
@php
    $type = $type ?? 'text';
    $value = $value ?? null;
    $placeholder = $placeholder ?? null;
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $readonly = $readonly ?? false;
    $help = $help ?? null;
    $class = $class ?? '';
    $id = $id ?? $name;
    $label = $label ?? null;

    $value = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div class="form-field">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 {{ $required ? 'required' : '' }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="mt-1 {{ $label ? 'mt-1' : '' }}">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            class="block w-full rounded-md shadow-sm {{ $hasError
                ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            }} {{ $class }}"
        >
    </div>

    @if($hasError)
        <p class="mt-2 text-sm text-red-600">{{ $errors->first($name) }}</p>
    @endif

    @if($help)
        <p class="mt-2 text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>

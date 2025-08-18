{{-- Select Field Component --}}
@php
    $options = $options ?? [];
    $value = $value ?? null;
    $placeholder = $placeholder ?? 'Select an option';
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $multiple = $multiple ?? false;
    $help = $help ?? null;
    $class = $class ?? '';
    $id = $id ?? $name;
    $label = $label ?? null;

    $value = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div class="form-field">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="mt-1">
        <select
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            id="{{ $id }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
            class="block w-full rounded-md shadow-sm {{ $hasError
                ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500'
                : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500'
            }} {{ $class }}"
        >
            @if(!$multiple && $placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $optionValue => $optionLabel)
                @if(is_array($optionLabel))
                    {{-- Option group --}}
                    <optgroup label="{{ $optionValue }}">
                        @foreach($optionLabel as $groupValue => $groupLabel)
                            <option
                                value="{{ $groupValue }}"
                                @if($multiple)
                                    @if(is_array($value) && in_array($groupValue, $value)) selected @endif
                                @else
                                    @if($groupValue == $value) selected @endif
                                @endif
                            >
                                {{ $groupLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option
                        value="{{ $optionValue }}"
                        @if($multiple)
                            @if(is_array($value) && in_array($optionValue, $value)) selected @endif
                        @else
                            @if($optionValue == $value) selected @endif
                        @endif
                    >
                        {{ $optionLabel }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>

    @if($hasError)
        <p class="mt-2 text-sm text-red-600">{{ $errors->first($name) }}</p>
    @endif

    @if($help)
        <p class="mt-2 text-sm text-gray-500">{{ $help }}</p>
    @endif
</div>

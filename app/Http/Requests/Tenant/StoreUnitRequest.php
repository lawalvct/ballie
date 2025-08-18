<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $tenantId = $this->route('tenant')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
                Rule::unique('units')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'description' => 'nullable|string|max:1000',
            'is_base_unit' => 'required|boolean',
            'base_unit_id' => [
                'nullable',
                'required_if:is_base_unit,false',
                'exists:units,id',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if ($value && !\App\Models\Unit::where('id', $value)->where('tenant_id', $tenantId)->where('is_base_unit', true)->exists()) {
                        $fail('The selected base unit is invalid.');
                    }
                },
            ],
            'conversion_factor' => [
                'nullable',
                'required_if:is_base_unit,false',
                'numeric',
                'min:0.000001',
                'max:999999.999999',
            ],
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Unit name is required.',
            'name.unique' => 'A unit with this name already exists.',
            'symbol.required' => 'Unit symbol is required.',
            'symbol.unique' => 'A unit with this symbol already exists.',
            'base_unit_id.required_if' => 'Base unit is required for derived units.',
            'conversion_factor.required_if' => 'Conversion factor is required for derived units.',
            'conversion_factor.min' => 'Conversion factor must be greater than 0.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string boolean values to actual booleans
        $this->merge([
            'is_base_unit' => $this->boolean('is_base_unit'),
            'is_active' => $this->boolean('is_active', true),
        ]);

        // If it's a base unit, clear base_unit_id and conversion_factor
        if ($this->boolean('is_base_unit')) {
            $this->merge([
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ]);
        }
    }
}
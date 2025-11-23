<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        
        // Owner role has all permissions
        if ($user->roles()->where('name', 'Owner')->exists()) {
            return true;
        }
        
        // Check for explicit permission
        return $user->can('admin.roles.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
            'is_active' => ['boolean'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The role name is required.',
            'name.string' => 'The role name must be a string.',
            'name.max' => 'The role name may not be greater than 255 characters.',
            'name.unique' => 'This role name already exists.',

            'description.max' => 'The description may not be greater than 1000 characters.',

            'permissions.array' => 'The permissions field must be an array.',
            'permissions.*.exists' => 'The selected permission is invalid.',

            'color.regex' => 'The color must be a valid hex color code (e.g., #FF0000).',

            'priority.integer' => 'The priority must be a number.',
            'priority.min' => 'The priority must be at least 0.',
            'priority.max' => 'The priority may not be greater than 999.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'role name',
            'description' => 'role description',
            'permissions' => 'permissions',
            'is_active' => 'status',
            'color' => 'color',
            'priority' => 'priority',
        ];
    }
}

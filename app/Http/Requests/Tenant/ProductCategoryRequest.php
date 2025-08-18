<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories')
                    ->where('tenant_id', $tenantId)
                    ->ignore($categoryId)
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('product_categories')
                    ->where('tenant_id', $tenantId)
                    ->ignore($categoryId)
            ],
            'description' => 'nullable|string|max:1000',
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) use ($tenantId, $categoryId) {
                    if ($value) {
                        // Check if parent belongs to same tenant
                        $parent = \App\Models\ProductCategory::find($value);
                        if (!$parent || $parent->tenant_id !== $tenantId) {
                            $fail('The selected parent category is invalid.');
                        }

                        // Prevent circular reference
                        if ($categoryId && $this->wouldCreateCircularReference($categoryId, $value)) {
                            $fail('Cannot set this category as parent as it would create a circular reference.');
                        }
                    }
                }
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'A category with this slug already exists.',
            'parent_id.exists' => 'The selected parent category does not exist.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2MB.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order must not exceed 999999.',
            'meta_title.max' => 'Meta title may not be greater than 255 characters.',
            'meta_description.max' => 'Meta description may not be greater than 500 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        if ($this->parent_id === '') {
            $this->merge(['parent_id' => null]);
        }

        if ($this->sort_order === '') {
            $this->merge(['sort_order' => null]);
        }

        // Ensure is_active is boolean
        $this->merge([
            'is_active' => $this->boolean('is_active', true)
        ]);
    }

    /**
     * Check if setting parent would create circular reference
     */
    private function wouldCreateCircularReference($categoryId, $parentId)
    {
        $current = \App\Models\ProductCategory::find($parentId);

        while ($current) {
            if ($current->id == $categoryId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }
}

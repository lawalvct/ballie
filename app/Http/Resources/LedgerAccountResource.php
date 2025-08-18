<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LedgerAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'account_type' => $this->account_type,
            'account_type_label' => ucfirst($this->account_type),
            'balance_type' => $this->balance_type,
            'balance_type_label' => $this->balance_type === 'dr' ? 'Debit' : 'Credit',
            'opening_balance' => $this->opening_balance,
            'current_balance' => $this->getCurrentBalance(),
            'formatted_balance' => $this->getFormattedBalance(),
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'is_system_defined' => $this->is_system_defined,
            'hierarchy_path' => $this->getHierarchyPath(),
            'has_children' => $this->children()->count() > 0,
            'children_count' => $this->children()->count(),
            'transaction_count' => $this->voucherEntries()->count(),
            'last_transaction_date' => $this->getLastTransactionDate()?->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Relationships
            'account_group' => $this->whenLoaded('accountGroup', function () {
                return [
                    'id' => $this->accountGroup->id,
                    'name' => $this->accountGroup->name,
                    'nature' => $this->accountGroup->nature,
                ];
            }),

            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'code' => $this->parent->code,
                    'name' => $this->parent->name,
                ];
            }),

            'children' => LedgerAccountResource::collection($this->whenLoaded('children')),
        ];
    }
}
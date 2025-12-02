<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isBuyer() && auth()->user()->isActive();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'farmer_id' => ['required', 'exists:users,id'],
            'payment_method' => ['required', 'string', 'max:50', 'in:credit_card,debit_card,cod,bank_transfer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'farmer_id.required' => 'The farmer ID is required.',
            'farmer_id.exists' => 'The selected farmer does not exist.',
            'payment_method.required' => 'The payment method is required.',
            'payment_method.in' => 'The selected payment method is not valid.',
            'items.required' => 'At least one item must be in the order.',
            'items.*.product_id.required' => 'The product ID is required for each item.',
            'items.*.product_id.exists' => 'One or more products do not exist.',
            'items.*.quantity.required' => 'The quantity is required for each item.',
            'items.*.quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}

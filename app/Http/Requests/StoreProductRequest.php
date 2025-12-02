<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isFarmer() && auth()->user()->isActive();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:0'],
            'category' => ['required', 'string', 'max:50', 'in:Vegetables,Fruits,Grains,Dairy,Meat,Spices,Other'],
            'image_url' => ['required', 'string', 'url'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'description.required' => 'A product description is required.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be at least 0.01.',
            'stock.required' => 'The product stock quantity is required.',
            'stock.integer' => 'Stock must be a whole number.',
            'stock.min' => 'Stock cannot be negative.',
            'category.required' => 'A product category is required.',
            'category.in' => 'The selected category is not valid.',
            'image_url.required' => 'A product image URL is required.',
            'image_url.url' => 'The image URL must be valid.',
        ];
    }
}

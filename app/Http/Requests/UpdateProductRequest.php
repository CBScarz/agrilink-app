<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0.01'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'category' => ['sometimes', 'string', 'max:50', 'in:Vegetables,Fruits,Grains,Dairy,Meat,Spices,Other'],
            'image_url' => ['sometimes', 'string', 'url'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be at least 0.01.',
            'stock.integer' => 'Stock must be a whole number.',
            'stock.min' => 'Stock cannot be negative.',
            'category.in' => 'The selected category is not valid.',
            'image_url.url' => 'The image URL must be valid.',
        ];
    }
}

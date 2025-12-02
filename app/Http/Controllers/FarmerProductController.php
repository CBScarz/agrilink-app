<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FarmerProductController extends Controller
{
    public function store(Request $request)
    {
        // Check if farmer is active
        if (auth()->user()->status !== 'active') {
            return redirect()->back()->with('error', 'Your account is pending approval. Please wait for admin verification before creating products.');
        }

        if (auth()->user()->role !== 'farmer') {
            return redirect()->back()->with('error', 'Only farmers can create products.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string',
            'availability' => 'nullable|string',
            'unit' => 'nullable|string',
            'origin' => 'nullable|string',
            'harvestDate' => 'nullable|date',
            'expirationDate' => 'nullable|date',
            'certification' => 'nullable|string',
            'features' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['farmer_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_url'] = $imagePath;
        }

        // Remove the temporary image file from validated data
        unset($validated['image']);

        $product = Product::create($validated);

        return redirect('/farmer/products')->with('success', 'Product created successfully!');
    }
}

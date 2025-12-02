<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'farmer_id',
        'name',
        'description',
        'price',
        'stock',
        'category',
        'availability',
        'unit',
        'origin',
        'harvestDate',
        'expirationDate',
        'certification',
        'features',
        'image_url',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Get the farmer that owns the product.
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Reduce stock by given quantity.
     */
    public function reduceStock(int $quantity): void
    {
        $this->stock -= $quantity;
        $this->save();
    }

    /**
     * Increase stock by given quantity.
     */
    public function increaseStock(int $quantity): void
    {
        $this->stock += $quantity;
        $this->save();
    }
}

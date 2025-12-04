<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $table = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the farmer profile associated with the user.
     */
    public function farmerProfile()
    {
        return $this->hasOne(FarmerProfile::class);
    }

    /**
     * Get the products owned by the farmer.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'farmer_id');
    }

    /**
     * Get the orders placed by the buyer.
     */
    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get the orders received by the farmer.
     */
    public function farmerOrders()
    {
        return $this->hasMany(Order::class, 'farmer_id');
    }

    /**
     * Get the ratings given by the buyer.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'buyer_id');
    }

    /**
     * Get the farmer's average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->products()
            ->with('ratings')
            ->get()
            ->flatMap(fn($product) => $product->ratings)
            ->avg('rating') ?? 0;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a farmer.
     */
    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    /**
     * Check if user is a buyer.
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}

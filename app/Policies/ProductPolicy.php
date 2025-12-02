<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a specific product.
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Determine if the user can create a product.
     */
    public function create(User $user): bool
    {
        return $user->isFarmer() && $user->isActive();
    }

    /**
     * Determine if the user can update a product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->isFarmer() && $user->id === $product->farmer_id && $user->isActive();
    }

    /**
     * Determine if the user can delete a product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->isFarmer() && $user->id === $product->farmer_id && $user->isActive();
    }

    /**
     * Determine if the user can restore a product.
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->isAdmin() || ($user->isFarmer() && $user->id === $product->farmer_id);
    }

    /**
     * Determine if the user can permanently delete a product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }
}

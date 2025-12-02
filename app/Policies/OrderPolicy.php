<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isFarmer() || $user->isBuyer();
    }

    /**
     * Determine if the user can view a specific order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || 
               $user->id === $order->buyer_id || 
               $user->id === $order->farmer_id;
    }

    /**
     * Determine if the user can create an order.
     */
    public function create(User $user): bool
    {
        return $user->isBuyer() && $user->isActive();
    }

    /**
     * Determine if the user can update an order status.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || 
               ($user->isFarmer() && $user->id === $order->farmer_id && $user->isActive());
    }

    /**
     * Determine if the user can cancel an order.
     */
    public function cancel(User $user, Order $order): bool
    {
        return $user->isBuyer() && $user->id === $order->buyer_id && $order->isPending();
    }

    /**
     * Determine if the user can delete an order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}

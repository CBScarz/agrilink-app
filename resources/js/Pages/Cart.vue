<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Shopping Cart</h1>
        <p class="text-gray-600">Review and manage your items</p>
      </div>

      <!-- Empty Cart Message -->
      <div v-if="cartItems.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
        <p class="text-gray-600 text-lg mb-4">Your cart is empty</p>
        <Link href="/products" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
          Continue Shopping
        </Link>
      </div>

      <!-- Cart Content -->
      <div v-else class="grid lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
          <!-- Group items by farmer -->
          <div v-for="farmerGroup in itemsGroupedByFarmer" :key="farmerGroup.farmerId" class="bg-white rounded-lg shadow-md mb-6">
            <!-- Farmer Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-transparent">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-bold text-gray-900">{{ farmerGroup.farmerName }}</h3>
                  <p class="text-sm text-gray-600">{{ farmerGroup.farmerLocation }}</p>
                </div>
                <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                  {{ farmerGroup.items.length }} {{ farmerGroup.items.length === 1 ? 'item' : 'items' }}
                </span>
              </div>
            </div>

            <!-- Items from this farmer -->
            <div class="divide-y divide-gray-200">
              <div v-for="item in farmerGroup.items" :key="item.id" class="px-6 py-4 flex gap-4 hover:bg-gray-50 transition">
                <!-- Product Image -->
                <div class="w-24 h-24 bg-gradient-to-br from-green-200 to-green-400 rounded-lg flex items-center justify-center flex-shrink-0">
                  <img 
                    v-if="item.product.image_url" 
                    :src="`/storage/${item.product.image_url}`" 
                    :alt="item.product.name"
                    class="w-full h-full object-cover rounded-lg"
                  />
                  <span v-else class="text-green-700 font-semibold text-xs text-center px-2">{{ item.product.name }}</span>
                </div>

                <!-- Product Details -->
                <div class="flex-1 min-w-0">
                  <Link 
                    :href="`/products/${item.product.id}`"
                    class="text-lg font-bold text-gray-900 hover:text-green-600 transition line-clamp-1"
                  >
                    {{ item.product.name }}
                  </Link>
                  <p class="text-sm text-gray-600 mt-1">{{ item.product.category }}</p>
                  <p class="text-sm text-gray-600">Stock available: {{ item.product.stock }}</p>
                  
                  <!-- Quantity Selector -->
                  <div class="flex items-center gap-2 mt-3">
                    <button 
                      @click="decreaseQuantity(item)"
                      class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-100 transition"
                    >
                      −
                    </button>
                    <input 
                      v-model.number="item.quantity" 
                      type="number" 
                      min="1"
                      :max="item.product.stock"
                      class="w-16 px-2 py-1 text-center border border-gray-300 rounded focus:ring-2 focus:ring-green-600"
                    />
                    <button 
                      @click="increaseQuantity(item)"
                      :disabled="item.quantity >= item.product.stock"
                      class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      +
                    </button>
                  </div>
                </div>

                <!-- Price & Remove -->
                <div class="text-right flex flex-col justify-between">
                  <div>
                    <p class="text-sm text-gray-600">₱{{ Number(item.product.price).toFixed(2) }}/{{ item.product.unit }}</p>
                    <p class="text-lg font-bold text-green-600 mt-1">₱{{ (item.quantity * item.product.price).toFixed(2) }}</p>
                  </div>
                  <button 
                    @click="handleRemoveFromCart(item)"
                    class="text-red-600 hover:text-red-700 text-sm font-semibold transition"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h3>

            <!-- Summary Items -->
            <div class="space-y-3 mb-4 pb-4 border-b border-gray-200">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal ({{ totalItems }} {{ totalItems === 1 ? 'item' : 'items' }})</span>
                <span class="font-semibold text-gray-900">₱{{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Delivery Fee</span>
                <span class="font-semibold text-gray-900">₱{{ deliveryFee.toFixed(2) }}</span>
              </div>
              <div v-if="discount > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Discount</span>
                <span class="font-semibold text-green-600">-₱{{ discount.toFixed(2) }}</span>
              </div>
            </div>

            <!-- Total -->
            <div class="flex justify-between mb-6">
              <span class="text-lg font-bold text-gray-900">Total</span>
              <span class="text-2xl font-bold text-green-600">₱{{ total.toFixed(2) }}</span>
            </div>

            <!-- Checkout Button -->
            <button 
              @click="checkout"
              class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold mb-2"
            >
              Proceed to Checkout
            </button>

            <!-- Continue Shopping -->
            <Link 
              href="/products"
              class="block w-full text-center px-4 py-2 border border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition font-semibold"
            >
              Continue Shopping
            </Link>

            <!-- Notes -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
              <p class="text-xs text-blue-800">
                <span class="font-semibold">Note:</span> Delivery fees will be calculated based on your delivery address during checkout.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { useCart } from '../composables/useCart.js';

const { props } = usePage();
const { auth } = props;

const { cartItems, removeFromCart, updateQuantity, getItemsGroupedByFarmer } = useCart();

const deliveryFee = ref(50);
const discount = ref(0);

const itemsGroupedByFarmer = computed(() => {
  return getItemsGroupedByFarmer.value;
});

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.quantity * item.product.price), 0);
});

const totalItems = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

const total = computed(() => {
  return subtotal.value + deliveryFee.value - discount.value;
});

const increaseQuantity = (item) => {
  if (item.quantity < item.product.stock) {
    updateQuantity(item.id, item.quantity + 1);
  }
};

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    updateQuantity(item.id, item.quantity - 1);
  }
};

const handleRemoveFromCart = (item) => {
  removeFromCart(item.id);
};

const checkout = () => {
  // TODO: Implement checkout functionality
  alert(`Proceeding to checkout with ${totalItems.value} items for ₱${total.value.toFixed(2)}`);
  // In a real app, this would navigate to a checkout page or process payment
};
</script>

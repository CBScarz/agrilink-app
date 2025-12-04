<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-5xl w-full max-h-[95vh] overflow-y-auto">
      <!-- Close Button -->
      <button
        @click="close"
        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition z-10"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Modal Content -->
      <div class="p-6">
        <div class="grid md:grid-cols-2 gap-8">
          <!-- Left Column: Product Images -->
          <div>
            <!-- Main Image -->
            <div class="mb-4 bg-gray-100 rounded-lg overflow-hidden">
              <img 
                v-if="product.image_url"
                :src="`/storage/${product.image_url}`" 
                :alt="product.name"
                class="w-full h-96 object-cover"
              />
              <div v-else class="w-full h-96 bg-gradient-to-br from-green-200 to-green-400 flex items-center justify-center rounded-lg">
                <span class="text-green-700 font-semibold text-lg text-center px-4">{{ product.name }}</span>
              </div>
            </div>
          </div>

          <!-- Right Column: Product Details -->
          <div class="space-y-4">
            <!-- Title & Rating -->
            <div>
              <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ product.name }}</h2>
              
              <!-- Rating & Reviews -->
              <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center gap-2">
                  <div class="flex gap-1">
                    <span v-for="i in 5" :key="i" class="text-xl" :class="i <= Math.floor(product.average_rating) ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
                  </div>
                  <span class="text-lg font-bold text-gray-900">{{ product.average_rating || 0 }}</span>
                </div>
                <span class="text-sm text-gray-600">{{ product.rating_count || 0 }} Ratings</span>
              </div>

              <!-- Category Badge -->
              <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                {{ product.category }}
              </span>
            </div>

            <!-- Price & Discount Section -->
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
              <p class="text-gray-600 text-xs font-medium mb-2">PRICE</p>
              <div class="flex items-center gap-3">
                <span class="text-4xl font-bold text-green-600">₱{{ Number(product.price).toFixed(2) }}</span>
                <span class="text-xs text-gray-600">per {{ product.unit }}</span>
              </div>
            </div>

            <!-- Stock & Shipping -->
            <div class="space-y-3 text-sm">
              <div class="flex items-start gap-3">
                <span v-if="product.stock > 0" class="inline-block w-5 h-5 bg-green-100 rounded-full flex items-center justify-center text-xs text-green-700 font-bold">✓</span>
                <span v-else class="inline-block w-5 h-5 bg-red-100 rounded-full flex items-center justify-center text-xs text-red-700 font-bold">✕</span>
                <div>
                  <p class="font-semibold text-gray-900" v-if="product.stock > 0">{{ product.stock }} {{ product.unit }} In Stock</p>
                  <p class="font-semibold text-gray-900" v-else>Out of Stock</p>
                  <p class="text-gray-600 text-xs">{{ product.stock > 0 ? 'Ready to ship' : 'Not available' }}</p>
                </div>
              </div>
            </div>

            <!-- Quantity Selector -->
            <div v-if="product.stock > 0" class="border-t border-gray-200 pt-4">
              <label class="block text-sm font-semibold text-gray-900 mb-3">Quantity</label>
              <div class="flex items-center gap-2 w-48">
                <button 
                  @click="quantity = Math.max(1, quantity - 1)"
                  class="w-10 h-10 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100 text-lg"
                >
                  −
                </button>
                <input 
                  v-model.number="quantity"
                  type="number"
                  min="1"
                  :max="product.stock"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded text-center font-semibold focus:ring-2 focus:ring-green-600 focus:border-transparent"
                />
                <button 
                  @click="quantity = Math.min(product.stock, quantity + 1)"
                  class="w-10 h-10 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100 text-lg"
                >
                  +
                </button>
              </div>
            </div>

            <!-- Action Buttons -->
            <div v-if="product.stock > 0" class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-200">
              <button
                @click="handleAddToCart"
                class="border-2 border-green-600 text-green-600 py-3 rounded-lg font-semibold hover:bg-green-50 transition flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Add To Cart
              </button>
              <button
                @click="handleBuyNow"
                class="bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition"
              >
                Buy Now
              </button>
            </div>

            <div v-else class="pt-4 border-t border-gray-200">
              <button
                disabled
                class="w-full bg-gray-300 text-gray-600 py-3 rounded-lg font-semibold cursor-not-allowed"
              >
                Out of Stock
              </button>
            </div>

            <!-- Farmer Information -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
              <p class="text-gray-600 text-xs font-semibold mb-2">FROM</p>
              <p class="font-semibold text-gray-900">{{ product.farmer?.name || 'Local Farmer' }}</p>
              <p class="text-sm text-gray-600">{{ product.farmer?.farmerProfile?.location || 'Location not specified' }}</p>
            </div>

            <!-- Description -->
            <div class="text-sm text-gray-600 line-clamp-3">
              {{ product.description }}
            </div>

            <!-- Features -->
            <div v-if="product.features" class="pt-2">
              <div class="space-y-2">
                <div v-for="feature in formatFeatures(product.features).slice(0, 3)" :key="feature" class="flex items-start gap-2 text-sm">
                  <span class="text-green-600 mt-0.5 flex-shrink-0">✓</span>
                  <span class="text-gray-700">{{ feature }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  product: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['close', 'add-to-cart', 'buy-now']);

const { props } = usePage();
const { auth } = props;

const quantity = ref(1);

const close = () => {
  quantity.value = 1;
  emit('close');
};

const handleAddToCart = () => {
  emit('add-to-cart', { product, quantity: quantity.value });
  alert(`Added ${quantity.value} ${product.unit} of ${product.name} to cart!`);
  quantity.value = 1;
  close();
};

const handleBuyNow = () => {
  emit('buy-now', { product, quantity: quantity.value });
  alert(`Proceeding to checkout with ${quantity.value} ${product.unit} of ${product.name}!`);
  quantity.value = 1;
  close();
};

const formatFeatures = (features) => {
  if (!features) return [];
  if (typeof features === 'string') {
    return features.split(',').map(f => f.trim());
  }
  return Array.isArray(features) ? features : [];
};
</script>

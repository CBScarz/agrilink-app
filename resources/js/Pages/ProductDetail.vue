<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <div class="mb-8 text-sm text-gray-600">
        <Link href="/" class="hover:text-green-600">Home</Link>
        <span class="mx-2">/</span>
        <Link href="/products" class="hover:text-green-600">Products</Link>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-medium">{{ product.name }}</span>
      </div>

      <!-- Product Section -->
      <div class="grid md:grid-cols-2 gap-8 mb-12">
        <!-- Left: Images -->
        <div>
          <!-- Main Image -->
          <div class="mb-4 bg-gray-100 rounded-lg overflow-hidden h-80">
            <img 
              v-if="product.image_url"
              :src="`/storage/${product.image_url}`" 
              :alt="product.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-green-200 to-green-400 flex items-center justify-center">
              <span class="text-green-700 font-semibold text-lg text-center px-4">{{ product.name }}</span>
            </div>
          </div>
        </div>

        <!-- Right: Details -->
        <div class="space-y-6">
          <!-- Title & Rating -->
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ product.name }}</h1>
            
            <!-- Rating -->
            <div class="flex items-center gap-4 mb-4">
              <div class="flex items-center gap-2">
                <div class="flex gap-1">
                  <span v-for="i in 5" :key="i" class="text-2xl" :class="i <= Math.floor(product.average_rating) ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
                </div>
                <span class="text-lg font-bold text-gray-900">{{ product.average_rating || 0 }}/5</span>
              </div>
              <span class="text-gray-600">({{ product.rating_count || 0 }} reviews)</span>
            </div>

            <!-- Category -->
            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
              {{ product.category }}
            </span>
          </div>

          <!-- Price -->
          <div class="bg-green-50 p-6 rounded-lg border border-green-100">
            <p class="text-gray-600 text-sm font-medium mb-2">PRICE PER {{ product.unit.toUpperCase() }}</p>
            <p class="text-4xl font-bold text-green-600">₱{{ Number(product.price).toFixed(2) }}</p>
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
            <div class="flex items-center gap-2 w-56">
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

          <!-- Cart Success Notification -->
          <div v-if="cartNotification" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg animate-pulse">
            <p class="font-semibold">✓ {{ cartNotification }}</p>
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
        </div>
      </div>

      <!-- Seller Info - Full Width Long Form -->
      <div class="bg-white p-6 rounded-lg border border-gray-200 mb-12">
        <div class="flex items-center justify-between gap-6">
          <!-- Left Section: Avatar & Info -->
          <div class="flex items-center gap-4">
            <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-3xl flex-shrink-0">
              {{ product.farmer?.name?.charAt(0).toUpperCase() || 'F' }}
            </div>
            <div class="flex-1">
              <p class="font-bold text-gray-900 text-base">{{ product.farmer?.name || 'Local Farmer' }}</p>
              <p class="text-xs text-gray-600 mb-1">{{ product.farmer?.farmerProfile?.location || 'Location not specified' }}</p>
              <div class="flex gap-2 mt-3">
                <button class="px-4 py-2 bg-red-500 text-white text-xs font-bold rounded hover:bg-red-600 transition">
                  Chat Now
                </button>
                <button class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-bold rounded hover:bg-gray-50 transition">
                  View Shop
                </button>
              </div>
            </div>
          </div>

          <!-- Right Section: Metrics -->
          <div class="flex gap-8 border-l border-gray-200 pl-8">
            <div class="text-center">
              <p class="text-gray-600 text-xs uppercase font-semibold mb-1">Ratings</p>
              <p class="text-xl font-bold text-gray-900">{{ product.average_rating || 0 }}/5</p>
            </div>
            <div class="text-center">
              <p class="text-gray-600 text-xs uppercase font-semibold mb-1">Response Rate</p>
              <p class="text-xl font-bold" :class="product.farmer?.response_rate >= 90 ? 'text-green-600' : 'text-yellow-600'">{{ product.farmer?.response_rate || 0 }}%</p>
            </div>
            <div class="text-center">
              <p class="text-gray-600 text-xs uppercase font-semibold mb-1">Products</p>
              <p class="text-xl font-bold text-gray-900">{{ product.farmer?.product_count || 0 }}</p>
            </div>
            <div class="text-center">
              <p class="text-gray-600 text-xs uppercase font-semibold mb-1">Joined</p>
              <p class="text-xl font-bold text-gray-900">{{ formatJoinDate(product.farmer?.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs Section -->
      <div class="border-b border-gray-200 mb-8">
        <div class="flex gap-8">
          <button
            @click="activeTab = 'description'"
            :class="[
              'py-4 font-semibold border-b-2 transition',
              activeTab === 'description'
                ? 'border-green-600 text-green-600'
                : 'border-transparent text-gray-600 hover:text-gray-900'
            ]"
          >
            Description
          </button>
          <button
            @click="activeTab = 'features'"
            :class="[
              'py-4 font-semibold border-b-2 transition',
              activeTab === 'features'
                ? 'border-green-600 text-green-600'
                : 'border-transparent text-gray-600 hover:text-gray-900'
            ]"
          >
            Features
          </button>
          <button
            @click="activeTab = 'reviews'"
            :class="[
              'py-4 font-semibold border-b-2 transition',
              activeTab === 'reviews'
                ? 'border-green-600 text-green-600'
                : 'border-transparent text-gray-600 hover:text-gray-900'
            ]"
          >
            Reviews ({{ product.rating_count || 0 }})
          </button>
        </div>
      </div>

      <!-- Tab Content -->
      <div class="mb-12">
        <!-- Description Tab -->
        <div v-if="activeTab === 'description'" class="space-y-6">
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Product Description</h3>
            <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
          </div>

          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h4 class="font-semibold text-gray-900 mb-3">Product Information</h4>
              <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <dt class="text-gray-600">Category:</dt>
                  <dd class="font-semibold text-gray-900">{{ product.category }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-600">Unit:</dt>
                  <dd class="font-semibold text-gray-900">{{ product.unit }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-600">Stock:</dt>
                  <dd class="font-semibold text-gray-900">{{ product.stock }} available</dd>
                </div>
                <div v-if="product.origin" class="flex justify-between">
                  <dt class="text-gray-600">Origin:</dt>
                  <dd class="font-semibold text-gray-900">{{ product.origin }}</dd>
                </div>
                <div v-if="product.harvestDate" class="flex justify-between">
                  <dt class="text-gray-600">Harvest Date:</dt>
                  <dd class="font-semibold text-gray-900">{{ formatDate(product.harvestDate) }}</dd>
                </div>
                <div v-if="product.expirationDate" class="flex justify-between">
                  <dt class="text-gray-600">Expiration Date:</dt>
                  <dd :class="['font-semibold', isExpiringSoon(product.expirationDate) ? 'text-red-600' : 'text-gray-900']">{{ formatDate(product.expirationDate) }}</dd>
                </div>
              </dl>
            </div>

            <div>
              <h4 class="font-semibold text-gray-900 mb-3">Seller Information</h4>
              <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <dt class="text-gray-600">Seller:</dt>
                  <dd class="font-semibold text-gray-900">{{ product.farmer?.name }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-600">Location:</dt>
                  <dd class="font-semibold text-gray-900">{{ product.farmer?.farmerProfile?.location || 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-600">Rating:</dt>
                  <dd class="font-semibold text-gray-900">{{ product.average_rating || 0 }}/5</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <!-- Features Tab -->
        <div v-if="activeTab === 'features'" class="space-y-6">
          <h3 class="text-xl font-bold text-gray-900">Product Features</h3>
          <div v-if="product.features" class="grid md:grid-cols-2 gap-6">
            <div v-for="(feature, index) in formatFeatures(product.features)" :key="index" class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg">
              <span class="text-green-600 mt-1 flex-shrink-0">✓</span>
              <span class="text-gray-700">{{ feature }}</span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            No features specified for this product.
          </div>
        </div>

        <!-- Reviews Tab -->
        <div v-if="activeTab === 'reviews'" class="space-y-6">
          <div>
            <h3 class="text-xl font-bold text-gray-900 mb-6">Customer Reviews</h3>
            
            <!-- Rating Summary -->
            <div class="bg-gray-50 p-6 rounded-lg mb-8">
              <div class="grid md:grid-cols-2 gap-8">
                <div>
                  <div class="text-5xl font-bold text-gray-900">{{ product.average_rating || 0 }}</div>
                  <div class="flex gap-1 mt-2">
                    <span v-for="i in 5" :key="i" class="text-2xl" :class="i <= Math.floor(product.average_rating) ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
                  </div>
                  <p class="text-gray-600 text-sm mt-2">Based on {{ product.rating_count || 0 }} reviews</p>
                </div>

                <div class="space-y-2">
                  <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 w-12">{{ star }} ⭐</span>
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                      <div class="h-full bg-yellow-400" :style="{ width: getStarPercentage(star) + '%' }"></div>
                    </div>
                    <span class="text-sm text-gray-600 w-12 text-right">{{ getStarCount(star) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Reviews List -->
            <div v-if="product.ratings && product.ratings.length > 0" class="space-y-4">
              <div v-for="review in product.ratings" :key="review.id" class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between mb-2">
                  <div>
                    <p class="font-semibold text-gray-900">{{ review.buyer?.name || 'Anonymous' }}</p>
                    <div class="flex gap-1 mt-1">
                      <span v-for="i in 5" :key="i" class="text-lg" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
                    </div>
                  </div>
                  <p class="text-sm text-gray-600">{{ formatDate(review.created_at) }}</p>
                </div>
                <p v-if="review.comment" class="text-gray-700">{{ review.comment }}</p>
                <p v-else class="text-gray-500 italic">No comment provided</p>
              </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
              No reviews yet. Be the first to review this product!
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="relatedProducts.length > 0" class="border-t pt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
        <div class="grid md:grid-cols-4 gap-6">
          <div v-for="relProduct in relatedProducts.slice(0, 4)" :key="relProduct.id" class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden cursor-pointer" @click="goToProduct(relProduct.id)">
            <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
              <img 
                v-if="relProduct.image_url"
                :src="`/storage/${relProduct.image_url}`" 
                :alt="relProduct.name"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-gray-600">{{ relProduct.category }}</span>
            </div>
            <div class="p-4">
              <h3 class="font-bold text-gray-900 line-clamp-2">{{ relProduct.name }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ relProduct.category }}</p>
              <p class="text-lg font-bold text-green-600 mt-2">₱{{ Number(relProduct.price).toFixed(2) }}</p>
              <div class="flex items-center gap-2 mt-2">
                <div class="flex gap-1">
                  <span v-for="i in 5" :key="i" class="text-sm" :class="i <= Math.floor(relProduct.average_rating) ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
                </div>
                <span class="text-xs text-gray-600">({{ relProduct.rating_count }})</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { useCart } from '../composables/useCart.js';

const { props } = usePage();
const { product, relatedProducts = [] } = props;

const { addToCart } = useCart();
const activeTab = ref('description');
const quantity = ref(1);
const cartNotification = ref(null);

const goToProduct = (productId) => {
  window.location.href = `/products/${productId}`;
};

const handleAddToCart = () => {
  const status = addToCart(product, quantity.value);
  
  // Show success notification
  cartNotification.value = `Added ${quantity.value} ${product.unit} of ${product.name} to cart!`;
  
  // Clear notification after 3 seconds
  setTimeout(() => {
    cartNotification.value = null;
  }, 3000);
  
  quantity.value = 1;
};

const handleBuyNow = () => {
  // First add to cart, then redirect
  addToCart(product, quantity.value);
  // In a real app, this would navigate to checkout
  alert(`Proceeding to checkout with ${quantity.value} ${product.unit} of ${product.name}!`);
  quantity.value = 1;
};

const formatFeatures = (features) => {
  if (!features) return [];
  if (typeof features === 'string') {
    return features.split(',').map(f => f.trim());
  }
  return Array.isArray(features) ? features : [];
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const isExpiringSoon = (expirationDate) => {
  if (!expirationDate) return false;
  const expDate = new Date(expirationDate);
  const today = new Date();
  const daysUntilExpiration = Math.floor((expDate - today) / (1000 * 60 * 60 * 24));
  return daysUntilExpiration <= 7; // Red if expires within 7 days
};

const formatJoinDate = (date) => {
  if (!date) return 'Recently joined';
  
  const joinDate = new Date(date);
  const now = new Date();
  const diffMs = now - joinDate;
  const diffYears = Math.floor(diffMs / (1000 * 60 * 60 * 24 * 365));
  const diffMonths = Math.floor((diffMs % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24 * 30));
  
  if (diffYears > 0) {
    return `${diffYears} year${diffYears > 1 ? 's' : ''} ago`;
  } else if (diffMonths > 0) {
    return `${diffMonths} month${diffMonths > 1 ? 's' : ''} ago`;
  } else {
    return 'Recently joined';
  }
};

const getStarCount = (star) => {
  if (!product.ratings) return 0;
  return product.ratings.filter(r => r.rating === star).length;
};

const getStarPercentage = (star) => {
  const count = getStarCount(star);
  const total = product.rating_count || 0;
  return total === 0 ? 0 : (count / total) * 100;
};
</script>

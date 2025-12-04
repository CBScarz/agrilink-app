<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Products</h1>
        <p class="text-gray-600">Browse fresh produce from local farmers</p>
      </div>

      <!-- Filters & Search -->
      <div class="mb-8 grid md:grid-cols-4 gap-4">
        <div>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search products..." 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
          />
        </div>

        <select v-model="selectedCategory" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent">
          <option value="">All Categories</option>
          <option value="Vegetables">Vegetables</option>
          <option value="Fruits">Fruits</option>
          <option value="Grains">Grains</option>
          <option value="Dairy">Dairy</option>
          <option value="Meat">Meat</option>
          <option value="Spices">Spices</option>
        </select>

        <select v-model="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent">
          <option value="newest">Newest</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="name">Name (A-Z)</option>
        </select>
      </div>

      <!-- Success Notification -->
      <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="text-green-700 font-medium">{{ successMessage }}</span>
      </div>

      <!-- No Products Message -->
      <div v-if="filteredProducts.length === 0" class="text-center py-12">
        <p class="text-gray-600 text-lg">No products found. Try adjusting your filters.</p>
      </div>

      <!-- Products Grid -->
      <div v-else class="grid md:grid-cols-4 gap-6">
        <div v-for="product in filteredProducts" :key="product.id" class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
          <!-- Product Image - Clickable to go to details -->
          <a :href="`/products/${product.id}`" class="block h-48 bg-gradient-to-br from-green-200 to-green-400 flex items-center justify-center hover:from-green-300 hover:to-green-500 transition cursor-pointer">
            <img 
              v-if="product.image_url" 
              :src="`/storage/${product.image_url}`" 
              :alt="product.name"
              class="w-full h-full object-cover"
            />
            <span v-else class="text-green-700 font-semibold">{{ product.name }}</span>
          </a>

          <!-- Product Info -->
          <div class="p-4">
            <a :href="`/products/${product.id}`" class="hover:text-green-600 transition">
              <h3 class="font-bold text-gray-900 line-clamp-2">{{ product.name }}</h3>
            </a>
            
            <p class="text-sm text-gray-600 mt-1">
              <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-xs mr-2">{{ product.category }}</span>
              <span class="text-xs">
                {{ product.stock > 0 ? `${product.stock} ${product.unit}` : 'Out of Stock' }}
              </span>
            </p>

            <!-- Description -->
            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ product.description }}</p>

            <!-- Price & Farmer -->
            <p class="text-sm text-gray-600 mt-2">By: {{ product.farmer?.name || 'Local Farmer' }}</p>
            <p class="text-2xl font-bold text-green-600 mt-2">₱{{ Number(product.price).toFixed(2) }}/{{ product.unit }}</p>

            <!-- Actions -->
            <div v-if="product.stock > 0 && auth.user?.role === 'buyer'" class="grid grid-cols-2 gap-2 mt-4">
              <a 
                :href="`/products/${product.id}`"
                class="text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-semibold"
              >
                Add to Cart
              </a>
              <button 
                @click="navigateToCheckout(product)"
                class="border-2 border-green-600 text-green-600 py-2 rounded-lg hover:bg-green-50 transition font-semibold"
              >
                Buy Now
              </button>
            </div>
            <button 
              v-else-if="product.stock === 0"
              disabled
              class="w-full mt-4 bg-gray-300 text-gray-600 py-2 rounded-lg cursor-not-allowed font-semibold"
            >
              Out of Stock
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { useCart } from '../composables/useCart.js';

const { props } = usePage();
const { auth } = props;
const products = ref([]);
const loading = ref(true);
const successMessage = ref('');

const searchQuery = ref('');
const selectedCategory = ref('');
const sortBy = ref('newest');

// Fetch products from API on mount
onMounted(async () => {
    try {
        const response = await fetch('/api/products');
        const data = await response.json();
        products.value = data.data || data;
    } catch (error) {
        console.error('Error fetching products:', error);
        products.value = [];
    } finally {
        loading.value = false;
    }
});

const filteredProducts = computed(() => {
  let result = products.value || [];

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.description.toLowerCase().includes(query)
    );
  }

  // Filter by category
  if (selectedCategory.value) {
    result = result.filter(p => p.category === selectedCategory.value);
  }

  // Sort
  if (sortBy.value === 'price-low') {
    result = [...result].sort((a, b) => a.price - b.price);
  } else if (sortBy.value === 'price-high') {
    result = [...result].sort((a, b) => b.price - a.price);
  } else if (sortBy.value === 'name') {
    result = [...result].sort((a, b) => a.name.localeCompare(b.name));
  } else {
    // newest - sort by id descending
    result = [...result].sort((a, b) => b.id - a.id);
  }

  return result;
});

const navigateToCheckout = (product) => {
  // Store product temporarily in sessionStorage to pass to checkout
  sessionStorage.setItem('buyNowProduct', JSON.stringify(product));
  window.location.href = '/checkout';
};
</script>

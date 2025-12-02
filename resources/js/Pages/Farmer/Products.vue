<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">My Products</h1>
          <p class="text-gray-600 mt-2">Manage all your listed products</p>
        </div>
        <Link href="/farmer/dashboard" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
          ← Back to Dashboard
        </Link>
      </div>

      <!-- Filters & Search -->
      <div class="mb-8 grid md:grid-cols-2 lg:grid-cols-5 gap-4">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search products..." 
          @keyup.enter="applyFilters"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
        />

        <select v-model="selectedCategory" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category" :value="category">
            {{ category }}
          </option>
        </select>

        <select v-model="stockStatus" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent">
          <option value="">All Stock Status</option>
          <option value="in_stock">In Stock</option>
          <option value="out_of_stock">Out of Stock</option>
        </select>

        <select v-model="sortBy" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent">
          <option value="newest">Newest</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="name">Name (A-Z)</option>
        </select>

        <button 
          @click="resetFilters"
          class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
        >
          Reset
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
          <p class="text-gray-600 text-sm font-medium">Total Products</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalProducts }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
          <p class="text-gray-600 text-sm font-medium">In Stock</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.inStockCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-600">
          <p class="text-gray-600 text-sm font-medium">Out of Stock</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.outOfStockCount }}</p>
        </div>
      </div>

      <!-- No Products Message -->
      <div v-if="products.data.length === 0" class="text-center py-12 bg-white rounded-lg shadow-md">
        <p class="text-gray-600 text-lg mb-4">You haven't created any products yet.</p>
        <Link href="/products/create" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
          + Create Your First Product
        </Link>
      </div>

      <!-- Products Grid -->
      <div v-else class="grid md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
        <div v-for="product in products.data" :key="product.id" class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
          <!-- Product Image -->
          <div class="h-48 bg-gradient-to-br from-green-200 to-green-400 flex items-center justify-center cursor-pointer hover:from-green-300 hover:to-green-500 transition relative">
            <img 
              v-if="product.image_url" 
              :src="`/storage/${product.image_url}`" 
              :alt="product.name"
              class="w-full h-full object-cover"
            />
            <span v-else class="text-green-700 font-semibold text-center px-4">{{ product.name }}</span>
            
            <!-- Stock Badge -->
            <div v-if="product.stock <= 5" class="absolute top-2 right-2">
              <span v-if="product.stock <= 0" class="inline-block px-3 py-1 bg-red-600 text-white rounded-full text-xs font-bold">
                OUT OF STOCK
              </span>
              <span v-else class="inline-block px-3 py-1 bg-orange-600 text-white rounded-full text-xs font-bold">
                LOW STOCK
              </span>
            </div>
          </div>

          <!-- Product Info -->
          <div class="p-4">
            <h3 class="font-bold text-gray-900 line-clamp-2">{{ product.name }}</h3>
            
            <p class="text-sm text-gray-600 mt-1">
              <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-xs mr-2">{{ product.category }}</span>
            </p>

            <!-- Description -->
            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ product.description }}</p>

            <!-- Price & Stock -->
            <p class="text-2xl font-bold text-green-600 mt-2">₱{{ formatPrice(product.price) }}</p>
            <p class="text-sm text-gray-600 mt-1">Stock: <span class="font-semibold">{{ product.stock }} {{product.unit}}</span></p>

            <!-- Actions -->
            <div class="flex items-center space-x-2 mt-4">
              <button 
                class="flex-1 border border-green-600 text-green-600 py-2 rounded-lg hover:bg-green-50 transition font-semibold text-sm"
              >
                Edit
              </button>
              <button 
                @click="deleteProduct(product.id, product.name)"
                class="flex-1 border border-red-600 text-red-600 py-2 rounded-lg hover:bg-red-50 transition font-semibold text-sm"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="products.links && products.links.length > 3" class="flex justify-center space-x-2">
        <Link 
          v-for="link in products.links"
          :key="link.label"
          :href="link.url || '#'"
          :class="{
            'px-4 py-2 rounded border': true,
            'bg-green-600 text-white border-green-600': link.active,
            'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active && link.url,
            'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': !link.url,
          }"
          v-html="link.label"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

const { props } = usePage();
const { products, categories, filters, stats } = props;

const searchQuery = ref(filters?.search || '');
const selectedCategory = ref(filters?.category || '');
const stockStatus = ref(filters?.stock_status || '');
const sortBy = ref(filters?.sort || 'newest');

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
};

const applyFilters = () => {
  const params = new URLSearchParams();
  if (searchQuery.value) params.append('search', searchQuery.value);
  if (selectedCategory.value) params.append('category', selectedCategory.value);
  if (stockStatus.value) params.append('stock_status', stockStatus.value);
  if (sortBy.value && sortBy.value !== 'newest') params.append('sort', sortBy.value);
  
  const url = `/farmer/products${params.toString() ? '?' + params.toString() : ''}`;
  window.location.href = url;
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  stockStatus.value = '';
  sortBy.value = 'newest';
  window.location.href = '/farmer/products';
};

const deleteProduct = (productId, productName) => {
  if (confirm(`Are you sure you want to delete "${productName}"?`)) {
    // TODO: Implement delete functionality
    alert('Delete functionality coming soon!');
  }
};
</script>

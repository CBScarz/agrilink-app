<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">Manage Products</h1>
          <p class="text-gray-600 mt-2">View, edit, and delete farmer products</p>
        </div>
        <Link href="/admin/dashboard" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
          ← Back to Dashboard
        </Link>
      </div>

      <!-- Status Messages -->
      <div v-if="$page.props.flash?.status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
        {{ $page.props.flash.status }}
      </div>
      <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
        {{ $page.props.flash.error }}
      </div>

      <!-- Filters -->
      <div class="mb-6 grid md:grid-cols-4 gap-4">
        <div>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search products..." 
            @keyup.enter="applyFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>
        <select v-model="farmerId" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Farmers</option>
          <option v-for="farmer in farmers" :key="farmer.id" :value="farmer.id">
            {{ farmer.name }}
          </option>
        </select>
        <select v-model="stockStatus" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Stock Status</option>
          <option value="in_stock">In Stock</option>
          <option value="out_of_stock">Out of Stock</option>
        </select>
        <button 
          @click="resetFilters"
          class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
        >
          Reset Filters
        </button>
      </div>

      <!-- Products Table -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Product Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Farmer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="products.data.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No products found</td>
              </tr>
              <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ product.name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ product.farmer?.name || 'Unknown' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ product.category }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 font-medium">₱{{ formatPrice(product.price) }}</td>
                <td class="px-6 py-4 text-sm">
                  <div class="flex items-center space-x-2">
                    <input 
                      type="number" 
                      :value="product.stock" 
                      @blur="(e) => updateStock(product.id, parseInt(e.target.value))"
                      :disabled="updatingStock === product.id"
                      class="w-16 px-2 py-1 border border-gray-300 rounded text-center disabled:opacity-50"
                    />
                    <span v-if="updatingStock === product.id" class="text-xs text-gray-500">Saving...</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm">
                  <span v-if="product.stock > 0" class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                    In Stock
                  </span>
                  <span v-else class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                    Out of Stock
                  </span>
                </td>
                <td class="px-6 py-4 text-sm">
                  <button
                    @click="deleteProduct(product.id, product.name)"
                    :disabled="deletingProduct === product.id"
                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition disabled:opacity-50 text-xs font-medium"
                  >
                    {{ deletingProduct === product.id ? 'Deleting...' : 'Delete' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="products.links" class="mt-6 flex justify-center space-x-2">
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

      <!-- Stats Cards -->
      <div class="grid md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
          <p class="text-gray-600 text-sm font-medium">Total Products</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalProducts }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
          <p class="text-gray-600 text-sm font-medium">In Stock</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ inStockCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-600">
          <p class="text-gray-600 text-sm font-medium">Out of Stock</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ outOfStockCount }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

const { props } = usePage();
const { products, farmers, filters } = props;

const searchQuery = ref(filters?.search || '');
const farmerId = ref(filters?.farmer_id || '');
const stockStatus = ref(filters?.stock_status || '');

const deletingProduct = ref(null);
const updatingStock = ref(null);

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
};

const totalProducts = computed(() => {
  return products.total || 0;
});

const inStockCount = computed(() => {
  return products.data?.filter(p => p.stock > 0).length || 0;
});

const outOfStockCount = computed(() => {
  return products.data?.filter(p => p.stock <= 0).length || 0;
});

const applyFilters = () => {
  const params = new URLSearchParams();
  if (searchQuery.value) params.append('search', searchQuery.value);
  if (farmerId.value) params.append('farmer_id', farmerId.value);
  if (stockStatus.value) params.append('stock_status', stockStatus.value);
  
  window.location.href = `/admin/products?${params.toString()}`;
};

const resetFilters = () => {
  searchQuery.value = '';
  farmerId.value = '';
  stockStatus.value = '';
  window.location.href = '/admin/products';
};

const deleteProduct = async (productId, productName) => {
  if (confirm(`Are you sure you want to delete "${productName}"?`)) {
    deletingProduct.value = productId;
    const form = useForm({});
    form.post(route('admin.products.delete', productId), {
      onFinish: () => {
        deletingProduct.value = null;
      },
    });
  }
};

const updateStock = async (productId, newStock) => {
  updatingStock.value = productId;
  const form = useForm({ stock: newStock });
  form.patch(route('admin.products.stock', productId), {
    onFinish: () => {
      updatingStock.value = null;
    },
  });
};
</script>

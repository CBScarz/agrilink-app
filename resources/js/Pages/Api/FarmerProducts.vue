<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow sticky top-0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <h1 class="text-2xl font-bold text-green-600">AgriLink</h1>
          <button
            @click="handleLogout"
            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
          >
            Logout
          </button>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Products</h1>
        <button
          v-if="isActive"
          @click="showCreateForm = !showCreateForm"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
        >
          {{ showCreateForm ? 'Cancel' : 'Add Product' }}
        </button>
      </div>

      <!-- Create Product Form -->
      <div v-if="showCreateForm && isActive" class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Create New Product</h2>
        <form @submit.prevent="createProduct" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Product Name *</label>
            <input
              v-model="productForm.name"
              type="text"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Description *</label>
            <textarea
              v-model="productForm.description"
              required
              rows="3"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Price (₱) *</label>
              <input
                v-model.number="productForm.price"
                type="number"
                step="0.01"
                min="0"
                required
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Stock *</label>
              <input
                v-model.number="productForm.stock"
                type="number"
                min="0"
                required
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Category *</label>
            <select
              v-model="productForm.category"
              required
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500"
            >
              <option value="">Select a category</option>
              <option>Vegetables</option>
              <option>Fruits</option>
              <option>Grains</option>
              <option>Dairy</option>
              <option>Meat</option>
              <option>Other</option>
            </select>
          </div>

          <div v-if="createError" class="rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-700">{{ createError }}</p>
          </div>

          <button
            :disabled="creating"
            type="submit"
            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
          >
            {{ creating ? 'Creating...' : 'Create Product' }}
          </button>
        </form>
      </div>

      <!-- Stats -->
      <div v-if="!loading" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">Total Products</p>
          <p class="text-2xl font-bold text-green-600">{{ stats.total }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">In Stock</p>
          <p class="text-2xl font-bold text-blue-600">{{ stats.in_stock }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">Out of Stock</p>
          <p class="text-2xl font-bold text-red-600">{{ stats.out_of_stock }}</p>
        </div>
      </div>

      <!-- Products Grid -->
      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-600">Loading products...</p>
      </div>

      <div v-else-if="products.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
        <p class="text-gray-600">No products yet. Create your first product!</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-lg shadow overflow-hidden">
          <div class="bg-gray-200 h-40 flex items-center justify-center">
            <span class="text-gray-400">No image</span>
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-lg mb-2">{{ product.name }}</h3>
            <p class="text-gray-600 text-sm mb-2">{{ product.description.substring(0, 50) }}...</p>
            
            <div class="flex justify-between items-center mb-3">
              <span class="text-green-600 font-bold text-lg">₱{{ product.price }}</span>
              <span
                :class="{
                  'bg-green-100 text-green-800': product.stock > 0,
                  'bg-red-100 text-red-800': product.stock === 0,
                }"
                class="px-2 py-1 rounded text-xs font-medium"
              >
                {{ product.stock }} in stock
              </span>
            </div>

            <div class="flex gap-2">
              <button
                @click="editProduct(product)"
                class="flex-1 px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700"
              >
                Edit
              </button>
              <button
                @click="deleteProduct(product.id)"
                class="flex-1 px-3 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="mt-6 flex justify-center gap-2">
        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="currentPage = page"
          :class="{
            'bg-green-600 text-white': page === currentPage,
            'bg-white text-gray-900': page !== currentPage,
          }"
          class="px-3 py-2 border rounded"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();
const loading = ref(true);
const creating = ref(false);
const createError = ref(null);
const showCreateForm = ref(false);
const currentPage = ref(1);

const isActive = ref(true);
const products = ref([]);
const stats = ref({ total: 0, in_stock: 0, out_of_stock: 0 });
const pagination = ref({ last_page: 1 });

const productForm = ref({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  category: '',
});

onMounted(async () => {
  await loadProducts();
});

const loadProducts = async () => {
  loading.value = true;
  try {
    const response = await api.getFarmerProducts({
      page: currentPage.value,
      per_page: 9,
    });
    products.value = response.data || [];
    stats.value = response.stats || {};
    pagination.value = response.pagination || {};
  } catch (error) {
    console.error('Failed to load products:', error);
  } finally {
    loading.value = false;
  }
};

const createProduct = async () => {
  creating.value = true;
  createError.value = null;
  try {
    await api.createProduct(productForm.value);
    showCreateForm.value = false;
    productForm.value = {
      name: '',
      description: '',
      price: 0,
      stock: 0,
      category: '',
    };
    await loadProducts();
  } catch (error) {
    createError.value = error.message || 'Failed to create product';
  } finally {
    creating.value = false;
  }
};

const editProduct = (product) => {
  console.log('Edit product:', product);
  // TODO: Implement edit functionality
};

const deleteProduct = async (productId) => {
  if (confirm('Are you sure you want to delete this product?')) {
    try {
      await api.deleteProduct(productId);
      await loadProducts();
    } catch (error) {
      alert('Failed to delete product: ' + error.message);
    }
  }
};

const handleLogout = async () => {
  try {
    await api.logout();
    router.push('/login');
  } catch (error) {
    console.error('Logout failed:', error);
  }
};
</script>

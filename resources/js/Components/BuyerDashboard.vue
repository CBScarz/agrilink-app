<template>
  <div>
    <!-- Product Details Modal -->
    <ProductDetailsModal 
      :isOpen="selectedProduct !== null" 
      :product="selectedProduct || {}"
      @close="selectedProduct = null"
      @add-to-cart="addToCart"
    />

    <div class="grid md:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-600">
      <p class="text-gray-600 text-sm font-medium">Total Orders</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">24</p>
      <p class="text-green-600 text-sm mt-2">↑ 5 this month</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
      <p class="text-gray-600 text-sm font-medium">Pending Orders</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">3</p>
      <p class="text-yellow-600 text-sm mt-2">Action required</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-400">
      <p class="text-gray-600 text-sm font-medium">Total Spent</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">₱8,450</p>
      <p class="text-green-600 text-sm mt-2">↓ 3.2% from last month</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-300">
      <p class="text-gray-600 text-sm font-medium">Favorite Farmers</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">7</p>
      <p class="text-gray-600 text-sm mt-2">Regular suppliers</p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="grid md:grid-cols-3 gap-6">
    <!-- Recent Orders -->
    <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Your Orders</h2>
        <router-link to="/shop" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
          Shop Now
        </router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Order ID</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Farmer</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Amount</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="i in 5" :key="i" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4 font-medium text-gray-900">#{{ 10000 + i }}</td>
              <td class="py-3 px-4 text-gray-600">Farmer {{ i }}</td>
              <td class="py-3 px-4 text-gray-900 font-semibold">₱{{ 500 + i * 150 }}</td>
              <td class="py-3 px-4">
                <span :class="getStatusClass(i)" class="px-3 py-1 rounded-full text-sm font-medium">
                  {{ getStatus(i) }}
                </span>
              </td>
              <td class="py-3 px-4">
                <button class="text-blue-600 hover:text-blue-800">View</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recommendations -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6">Recommended Products</h2>
      <div class="space-y-4">
        <div v-if="recommendedProducts.length === 0" class="text-center py-8 text-gray-500">
          No products available at the moment.
        </div>
        <div v-for="product in recommendedProducts" :key="product.id" class="border rounded-lg p-3 hover:shadow-md transition">
          <p class="font-semibold text-gray-900">{{ product.name }}</p>
          <p class="text-sm text-gray-600">By {{ product.farmer?.name || 'Local Farmer' }}</p>
          <p class="text-lg font-bold text-green-600 mt-2">₱{{ Number(product.price).toFixed(2) }}/{{ product.unit }}</p>
          <div class="mt-2 space-y-2">
            <button 
              @click="openProductDetails(product)"
              class="w-full py-2 border border-green-600 text-green-600 rounded text-sm font-medium hover:bg-green-50 transition"
            >
              View Details
            </button>
            <button 
              v-if="product.stock > 0"
              @click="addToCart(product)"
              class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium transition"
            >
              Add to Cart
            </button>
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
import { Link } from '@inertiajs/vue3';
import ProductDetailsModal from './ProductDetailsModal.vue';
import { useCart } from '../composables/useCart.js';

const { props } = usePage();
const { auth, products = [] } = props;

const { addToCart: cartAddToCart } = useCart();

const selectedProduct = ref(null);

// Get recommended products (first 3 products)
const recommendedProducts = ref(products.slice(0, 3) || []);

const getStatus = (i) => {
  const statuses = ['Delivered', 'Processing', 'Shipped', 'Pending', 'Delivered'];
  return statuses[i - 1];
};

const getStatusClass = (i) => {
  const status = getStatus(i);
  if (status === 'Delivered') return 'bg-green-100 text-green-700';
  if (status === 'Processing') return 'bg-yellow-100 text-yellow-700';
  if (status === 'Shipped') return 'bg-blue-100 text-blue-700';
  return 'bg-gray-100 text-gray-700';
};

const openProductDetails = (product) => {
  selectedProduct.value = product;
};

const addToCart = ({ product, quantity = 1 }) => {
  const result = cartAddToCart(product, quantity);
  alert(`Added ${quantity} ${product.unit} of ${product.name} to cart!`);
  selectedProduct.value = null;
};
</script>

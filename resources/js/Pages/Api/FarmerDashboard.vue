<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-green-600">AgriLink</h1>
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="handleLogout"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-600">Loading dashboard...</p>
      </div>

      <div v-else>
        <!-- Status Alert -->
        <div
          v-if="dashboard.status === 'pending'"
          class="mb-6 rounded-md bg-yellow-50 p-4 border border-yellow-200"
        >
          <p class="text-sm text-yellow-700">
            Your account is pending admin approval. You'll be able to create products once approved.
          </p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Products</p>
            <p class="text-3xl font-bold text-green-600">{{ dashboard.stats.total_products }}</p>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Orders</p>
            <p class="text-3xl font-bold text-green-600">{{ dashboard.stats.total_orders }}</p>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Earnings</p>
            <p class="text-3xl font-bold text-green-600">₱{{ dashboard.stats.total_earnings }}</p>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Average Rating</p>
            <p class="text-3xl font-bold text-green-600">⭐ {{ dashboard.stats.average_rating }}</p>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
          <h2 class="text-lg font-semibold mb-4">Recent Orders</h2>
          <div v-if="dashboard.recent_orders.length === 0" class="text-center py-8 text-gray-500">
            No orders yet
          </div>
          <table v-else class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left py-2 px-4">Order ID</th>
                <th class="text-left py-2 px-4">Buyer</th>
                <th class="text-left py-2 px-4">Status</th>
                <th class="text-left py-2 px-4">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in dashboard.recent_orders" :key="order.id" class="border-b">
                <td class="py-2 px-4">#{{ order.id }}</td>
                <td class="py-2 px-4">{{ order.buyer?.name }}</td>
                <td class="py-2 px-4">
                  <span
                    :class="{
                      'bg-yellow-100 text-yellow-800': order.status === 'pending',
                      'bg-blue-100 text-blue-800': order.status === 'processing',
                      'bg-green-100 text-green-800': order.status === 'delivered',
                    }"
                    class="px-3 py-1 rounded-full text-sm font-medium"
                  >
                    {{ order.status }}
                  </span>
                </td>
                <td class="py-2 px-4">₱{{ order.total_amount }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Top Products</h2>
          <div v-if="dashboard.top_products.length === 0" class="text-center py-8 text-gray-500">
            No products yet
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="product in dashboard.top_products" :key="product.id" class="border rounded-lg p-4">
              <h3 class="font-semibold mb-2">{{ product.name }}</h3>
              <p class="text-gray-600 text-sm mb-2">Category: {{ product.category }}</p>
              <p class="text-green-600 font-bold">₱{{ product.price }}</p>
              <p class="text-gray-500 text-sm">Stock: {{ product.stock }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();
const loading = ref(true);
const dashboard = ref({
  status: 'active',
  stats: {
    total_products: 0,
    total_orders: 0,
    total_earnings: 0,
    average_rating: 0,
  },
  recent_orders: [],
  top_products: [],
});

onMounted(async () => {
  try {
    const response = await api.getFarmerDashboard();
    dashboard.value = response;
  } catch (error) {
    console.error('Failed to load dashboard:', error);
  } finally {
    loading.value = false;
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

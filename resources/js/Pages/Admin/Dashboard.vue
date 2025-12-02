<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome back! Here's your platform overview.</p>
      </div>

      <!-- KPI Cards -->
      <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Sales -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Sales</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ Number(totalSales).toLocaleString('en-PH', { maximumFractionDigits: 2 }) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Farmers -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Farmers</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalFarmers }}</p>
              <p class="text-xs text-orange-600 font-medium mt-1">{{ pendingFarmers }} pending</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Buyers -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Buyers</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalBuyers }}</p>
              <p class="text-xs text-gray-500 font-medium mt-1">Active users</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Products</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalProducts }}</p>
              <p class="text-xs text-gray-500 font-medium mt-1">Available items</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Orders</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalOrders }}</p>
              <p class="text-xs text-gray-500 font-medium mt-1">Transactions</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Buyer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-if="recentOrders.length === 0">
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No orders yet</td>
                  </tr>
                  <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ order.id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ order.buyer?.name || 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600">₱{{ Number(order.total_amount).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-sm">
                      <span v-if="order.status === 'pending'" class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">Pending</span>
                      <span v-else-if="order.status === 'processing'" class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">Processing</span>
                      <span v-else-if="order.status === 'shipped'" class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-medium">Shipped</span>
                      <span v-else-if="order.status === 'delivered'" class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Delivered</span>
                      <span v-else class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">{{ order.status }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Top Products -->
        <div>
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900">Top Products</h2>
            </div>
            <div class="divide-y divide-gray-200">
              <div v-if="topProducts.length === 0" class="px-6 py-8 text-center text-gray-500">
                No products yet
              </div>
              <div v-for="(product, index) in topProducts" :key="product.id" class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                      <span class="inline-block w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ index + 1 }}</span>
                      <p class="font-medium text-gray-900">{{ product.name }}</p>
                    </div>
                    <p class="text-xs text-gray-600">{{ product.farmer?.name || 'Unknown' }}</p>
                  </div>
                  <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">{{ product.order_items_count }} sold</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-2">
              <Link href="/admin/farmers" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center font-medium">
                Manage Farmers
              </Link>
              <Link href="/admin/products" class="block w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition text-center font-medium">
                Manage Products
              </Link>
              <Link href="/admin/orders" class="block w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-center font-medium">
                View All Orders
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

const { props } = usePage();
const {
  totalSales,
  totalFarmers,
  totalBuyers,
  totalProducts,
  totalOrders,
  pendingFarmers,
  recentOrders,
  topProducts,
} = props;
</script>

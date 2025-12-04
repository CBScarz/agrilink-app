<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Welcome back, {{ farmer.name }}!</h1>
        <p class="text-gray-600 mt-2">{{ farmerProfile?.business_name || 'Your Farm' }} - Overview & Analytics</p>
      </div>

      <!-- KPI Cards -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Products</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalProducts }}</p>
              <p class="text-xs text-gray-500 font-medium mt-1">Listed for sale</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10M8 7l4-2 4 2" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Orders</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalOrders }}</p>
              <p class="text-xs text-blue-600 font-medium mt-1">Orders received</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Earnings -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Earnings</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ Number(totalEarnings).toLocaleString('en-PH', { maximumFractionDigits: 2 }) }}</p>
              <p class="text-xs text-yellow-600 font-medium mt-1">From all orders</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Average Rating</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ averageRating.toFixed(1) }}/5</p>
              <div class="flex items-center mt-1">
                <span v-for="i in 5" :key="i" :class="i <= Math.floor(averageRating) ? 'text-yellow-400' : 'text-gray-300'" class="text-lg">
                  ⭐
                </span>
              </div>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Account Status Alert -->
      <div v-if="farmer.status === 'pending'" class="mb-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-center space-x-3">
          <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="text-yellow-800 font-semibold">Account Pending Approval</p>
            <p class="text-yellow-700 text-sm">Your account is awaiting admin verification. You cannot post products until approved.</p>
          </div>
        </div>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Recent Orders -->
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-if="recentOrders.length === 0">
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No orders yet</td>
                  </tr>
                  <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ order.id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ order.buyer?.name || 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ order.items?.length || 0 }} item(s)</td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600">₱{{ Number(order.total_amount).toFixed(2) }}</td>
                    <td class="px-6 py-4 text-sm">
                      <span v-if="order.status === 'pending'" class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">Pending</span>
                      <span v-else-if="order.status === 'processing'" class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">Processing</span>
                      <span v-else-if="order.status === 'shipped'" class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-medium">Shipped</span>
                      <span v-else-if="order.status === 'delivered'" class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Delivered</span>
                      <span v-else class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">{{ order.status }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Top Products -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900">Top Performing Products</h2>
            </div>
            <div class="divide-y divide-gray-200">
              <div v-if="topProducts.length === 0" class="px-6 py-8 text-center text-gray-500">
                No products yet. Start creating products to see them here.
              </div>
              <div v-for="(product, index) in topProducts" :key="product.id" class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                      <span class="inline-block w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ index + 1 }}</span>
                      <p class="font-medium text-gray-900">{{ product.name }}</p>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">{{ product.category }}</p>
                    <div class="flex items-center space-x-4">
                      <p class="text-xs text-yellow-600">
                        <span v-if="product.average_rating > 0">⭐ {{ product.average_rating }}/5 ({{ product.rating_count }} reviews)</span>
                        <span v-else class="text-gray-500">No ratings yet</span>
                      </p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="font-semibold text-green-600">₱{{ Number(product.price).toFixed(2) }}</p>
                    <p class="text-xs text-gray-600">{{ product.order_items_count || 0 }} orders</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
          <!-- Low Stock Alert -->
          <div v-if="lowStockProducts.length > 0" class="bg-red-50 rounded-lg shadow-md p-6 border-l-4 border-red-600">
            <h3 class="text-lg font-bold text-red-900 mb-4">Low Stock Alert</h3>
            <div class="space-y-3">
              <div v-for="product in lowStockProducts" :key="product.id" class="flex items-start justify-between">
                <div>
                  <p class="font-medium text-gray-900">{{ product.name }}</p>
                  <p class="text-xs text-gray-600">Stock: {{ product.stock }}/unit</p>
                </div>
                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-bold">{{ product.stock }}</span>
              </div>
            </div>
          </div>

          <!-- Farm Info -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Farm Information</h3>
            <div class="space-y-3">
              <div>
                <p class="text-xs text-gray-600 uppercase font-medium">Business Name</p>
                <p class="text-gray-900 font-medium">{{ farmerProfile?.business_name || 'Not set' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-600 uppercase font-medium">Location</p>
                <p class="text-gray-900 font-medium">{{ farmerProfile?.location || 'Not set' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-600 uppercase font-medium">Account Status</p>
                <div class="mt-1">
                  <span v-if="farmer.status === 'pending'" class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending Approval</span>
                  <span v-else-if="farmer.status === 'active'" class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Active</span>
                  <span v-else-if="farmer.status === 'rejected'" class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-2">
              <Link 
                v-if="farmer.status === 'active'"
                href="/farmer/products/create" 
                class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center font-medium text-sm"
              >
                + Add New Product
              </Link>
              <Link href="/farmer/products" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center font-medium text-sm">
                View All Products
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const {
  farmer,
  farmerProfile,
  totalProducts,
  totalOrders,
  totalEarnings,
  averageRating,
  recentOrders,
  topProducts,
  lowStockProducts,
} = props;
</script>

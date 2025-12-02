<template>
  <div class="grid md:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-600">
      <p class="text-gray-600 text-sm font-medium">Total Products</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
      <p class="text-green-600 text-sm mt-2">↑ 2 this month</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
      <p class="text-gray-600 text-sm font-medium">Total Orders</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">48</p>
      <p class="text-green-600 text-sm mt-2">↑ 12 this month</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-400">
      <p class="text-gray-600 text-sm font-medium">Revenue</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">₱15,240</p>
      <p class="text-green-600 text-sm mt-2">↑ 8.2% from last month</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-300">
      <p class="text-gray-600 text-sm font-medium">Status</p>
      <p class="text-lg font-bold text-green-600 mt-2">Active</p>
      <p class="text-gray-600 text-sm mt-2">All systems operational</p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="grid md:grid-cols-3 gap-6">
    <!-- Products Section -->
    <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900">Your Products</h2>
        <router-link to="/products/create" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
          Add Product
        </router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Product</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Category</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Stock</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Price</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="i in 5" :key="i" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4 text-gray-900 font-medium">Fresh Tomatoes</td>
              <td class="py-3 px-4 text-gray-600">Vegetables</td>
              <td class="py-3 px-4">
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">{{ 50 + i * 10 }} kg</span>
              </td>
              <td class="py-3 px-4 text-gray-900 font-semibold">₱{{ 50 + i * 5 }}</td>
              <td class="py-3 px-4">
                <button class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                <button class="text-red-600 hover:text-red-800">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6">Recent Orders</h2>
      <div class="space-y-4">
        <div v-for="i in 4" :key="i" class="pb-4 border-b">
          <p class="font-semibold text-gray-900">Order #{{ 1000 + i }}</p>
          <p class="text-sm text-gray-600">2 items - ₱{{ 500 + i * 100 }}</p>
          <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded mt-2 inline-block">Processing</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

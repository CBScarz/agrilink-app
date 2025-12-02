<template>
  <div class="grid md:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-600">
      <p class="text-gray-600 text-sm font-medium">Total Users</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">342</p>
      <p class="text-green-600 text-sm mt-2">↑ 42 this month</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
      <p class="text-gray-600 text-sm font-medium">Pending Farmers</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">8</p>
      <p class="text-yellow-600 text-sm mt-2">Awaiting approval</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-400">
      <p class="text-gray-600 text-sm font-medium">Total Orders</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">1,250</p>
      <p class="text-green-600 text-sm mt-2">↑ 180 this month</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-300">
      <p class="text-gray-600 text-sm font-medium">Platform Revenue</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">₱542K</p>
      <p class="text-green-600 text-sm mt-2">↑ 12.5% from last month</p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="grid md:grid-cols-3 gap-6">
    <!-- Pending Farmer Applications -->
    <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6">Pending Farmer Applications</h2>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Business</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Location</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Applied</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="i in 5" :key="i" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4 font-medium text-gray-900">Farmer Name {{ i }}</td>
              <td class="py-3 px-4 text-gray-600">Farm Business {{ i }}</td>
              <td class="py-3 px-4 text-gray-600">Province {{ i }}</td>
              <td class="py-3 px-4 text-gray-600">2 days ago</td>
              <td class="py-3 px-4">
                <button class="text-green-600 hover:text-green-800 mr-2">Approve</button>
                <button class="text-red-600 hover:text-red-800">Reject</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- System Stats -->
    <div class="space-y-6">
      <!-- User Distribution -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">User Distribution</h3>
        <div class="space-y-3">
          <div>
            <div class="flex justify-between mb-1">
              <p class="text-sm font-medium text-gray-700">Farmers</p>
              <p class="text-sm font-bold text-gray-900">120 (35%)</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-green-600 h-2 rounded-full" style="width: 35%"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between mb-1">
              <p class="text-sm font-medium text-gray-700">Buyers</p>
              <p class="text-sm font-bold text-gray-900">180 (52%)</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-green-500 h-2 rounded-full" style="width: 52%"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between mb-1">
              <p class="text-sm font-medium text-gray-700">Admins</p>
              <p class="text-sm font-bold text-gray-900">42 (13%)</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-green-400 h-2 rounded-full" style="width: 13%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-green-50 rounded-lg p-6 border border-green-200">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
          <button class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
            View All Users
          </button>
          <button class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
            View All Orders
          </button>
          <button class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-medium">
            Generate Reports
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

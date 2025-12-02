<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">View All Orders</h1>
          <p class="text-gray-600 mt-2">Monitor and manage all customer orders</p>
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
      <div class="mb-6 grid md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search by Order ID..." 
            @keyup.enter="applyFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>
        <select v-model="statusFilter" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <select v-model="buyerId" @change="applyFilters" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Buyers</option>
          <option v-for="buyer in buyers" :key="buyer.id" :value="buyer.id">
            {{ buyer.name }}
          </option>
        </select>
        <button 
          @click="resetFilters"
          class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
        >
          Reset Filters
        </button>
      </div>

      <!-- Orders Table -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Order ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Buyer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Farmer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="orders.data.length === 0">
                <td colspan="8" class="px-6 py-8 text-center text-gray-500">No orders found</td>
              </tr>
              <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ order.id }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ order.buyer?.name || 'Unknown' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ order.farmer?.name || 'Unknown' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ order.items?.length || 0 }} item(s)</td>
                <td class="px-6 py-4 text-sm font-semibold text-green-600">₱{{ formatPrice(order.total_amount) }}</td>
                <td class="px-6 py-4 text-sm">
                  <select 
                    :value="order.status" 
                    @change="(e) => updateStatus(order.id, e.target.value)"
                    :disabled="updatingStatus === order.id"
                    :class="{
                      'px-3 py-1 rounded text-xs font-medium border disabled:opacity-50': true,
                      'bg-yellow-100 text-yellow-800 border-yellow-300': order.status === 'pending',
                      'bg-blue-100 text-blue-800 border-blue-300': order.status === 'processing',
                      'bg-purple-100 text-purple-800 border-purple-300': order.status === 'shipped',
                      'bg-green-100 text-green-800 border-green-300': order.status === 'delivered',
                      'bg-red-100 text-red-800 border-red-300': order.status === 'cancelled',
                    }"
                  >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(order.created_at) }}</td>
                <td class="px-6 py-4 text-sm">
                  <div class="flex items-center space-x-2">
                    <button
                      @click="deleteOrder(order.id)"
                      :disabled="deletingOrder === order.id"
                      class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition disabled:opacity-50 text-xs font-medium"
                    >
                      {{ deletingOrder === order.id ? 'Deleting...' : 'Delete' }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="orders.links" class="mt-6 flex justify-center space-x-2">
        <Link 
          v-for="link in orders.links"
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
      <div class="grid md:grid-cols-4 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-600">
          <p class="text-gray-600 text-sm font-medium">Pending Orders</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ pendingCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
          <p class="text-gray-600 text-sm font-medium">Processing</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ processingCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
          <p class="text-gray-600 text-sm font-medium">Delivered</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ deliveredCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-600">
          <p class="text-gray-600 text-sm font-medium">Total Orders</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ orders.total || 0 }}</p>
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
const { orders, buyers, farmers, filters } = props;

const searchQuery = ref(filters?.search || '');
const statusFilter = ref(filters?.status || '');
const buyerId = ref(filters?.buyer_id || '');

const deletingOrder = ref(null);
const updatingStatus = ref(null);

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const pendingCount = computed(() => {
  return orders.data?.filter(o => o.status === 'pending').length || 0;
});

const processingCount = computed(() => {
  return orders.data?.filter(o => o.status === 'processing').length || 0;
});

const deliveredCount = computed(() => {
  return orders.data?.filter(o => o.status === 'delivered').length || 0;
});

const applyFilters = () => {
  const params = new URLSearchParams();
  if (searchQuery.value) params.append('search', searchQuery.value);
  if (statusFilter.value) params.append('status', statusFilter.value);
  if (buyerId.value) params.append('buyer_id', buyerId.value);
  
  window.location.href = `/admin/orders?${params.toString()}`;
};

const resetFilters = () => {
  searchQuery.value = '';
  statusFilter.value = '';
  buyerId.value = '';
  window.location.href = '/admin/orders';
};

const updateStatus = async (orderId, newStatus) => {
  updatingStatus.value = orderId;
  const form = useForm({ status: newStatus });
  form.patch(route('admin.orders.status', orderId), {
    onFinish: () => {
      updatingStatus.value = null;
    },
  });
};

const deleteOrder = async (orderId) => {
  if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
    deletingOrder.value = orderId;
    const form = useForm({});
    form.post(route('admin.orders.delete', orderId), {
      onFinish: () => {
        deletingOrder.value = null;
      },
    });
  }
};
</script>

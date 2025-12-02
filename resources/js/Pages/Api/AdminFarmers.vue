<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow sticky top-0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <h1 class="text-2xl font-bold text-green-600">AgriLink Admin</h1>
          <button
            @click="handleLogout"
            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
          >
            Logout
          </button>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-6">Manage Farmers</h1>

      <!-- Stats -->
      <div v-if="!loadingStats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">Total Farmers</p>
          <p class="text-2xl font-bold text-green-600">{{ stats.total_farmers }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">Pending</p>
          <p class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">Active</p>
          <p class="text-2xl font-bold text-blue-600">{{ stats.active }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">Rejected</p>
          <p class="text-2xl font-bold text-red-600">{{ stats.rejected }}</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Search</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Name, email, or farm name"
              @input="applyFilters"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select
              v-model="filters.status"
              @change="applyFilters"
              class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500"
            >
              <option value="">All</option>
              <option value="pending">Pending</option>
              <option value="active">Active</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Farmers Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div v-if="loading" class="p-6 text-center">
          <p class="text-gray-600">Loading farmers...</p>
        </div>

        <div v-else-if="farmers.length === 0" class="p-6 text-center">
          <p class="text-gray-600">No farmers found</p>
        </div>

        <table v-else class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Name</th>
              <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Email</th>
              <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
              <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="farmer in farmers" :key="farmer.id" class="hover:bg-gray-50">
              <td class="px-6 py-3 text-sm text-gray-900">{{ farmer.name }}</td>
              <td class="px-6 py-3 text-sm text-gray-600">{{ farmer.email }}</td>
              <td class="px-6 py-3 text-sm">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800': farmer.status === 'pending',
                    'bg-green-100 text-green-800': farmer.status === 'active',
                    'bg-red-100 text-red-800': farmer.status === 'rejected',
                  }"
                  class="px-3 py-1 rounded-full text-xs font-medium"
                >
                  {{ farmer.status }}
                </span>
              </td>
              <td class="px-6 py-3 text-sm space-x-2">
                <button
                  v-if="farmer.status === 'pending'"
                  @click="approve(farmer.id)"
                  class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700"
                >
                  Approve
                </button>
                <button
                  v-if="farmer.status === 'pending'"
                  @click="reject(farmer.id)"
                  class="px-3 py-1 bg-orange-600 text-white rounded text-xs hover:bg-orange-700"
                >
                  Reject
                </button>
                <button
                  @click="viewPermit(farmer.id)"
                  class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700"
                >
                  Permit
                </button>
                <button
                  @click="deleteFarmer(farmer.id)"
                  class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="bg-gray-50 border-t px-6 py-3 flex justify-center gap-2">
          <button
            v-for="page in pagination.last_page"
            :key="page"
            @click="currentPage = page; loadFarmers()"
            :class="{
              'bg-green-600 text-white': page === currentPage,
              'bg-white text-gray-900 border': page !== currentPage,
            }"
            class="px-3 py-1 rounded text-sm"
          >
            {{ page }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();
const loading = ref(true);
const loadingStats = ref(true);
const currentPage = ref(1);

const farmers = ref([]);
const stats = ref({
  total_farmers: 0,
  pending: 0,
  active: 0,
  rejected: 0,
});
const pagination = ref({ last_page: 1 });

const filters = reactive({
  search: '',
  status: '',
});

onMounted(async () => {
  await loadStats();
  await loadFarmers();
});

const loadStats = async () => {
  try {
    const response = await api.getAdminFarmerStats();
    stats.value = response;
  } catch (error) {
    console.error('Failed to load stats:', error);
  } finally {
    loadingStats.value = false;
  }
};

const loadFarmers = async () => {
  loading.value = true;
  try {
    const response = await api.getAdminFarmers({
      search: filters.search,
      status: filters.status,
      page: currentPage.value,
      per_page: 20,
    });
    farmers.value = response.data || [];
    pagination.value = response.pagination || {};
  } catch (error) {
    console.error('Failed to load farmers:', error);
  } finally {
    loading.value = false;
  }
};

const applyFilters = () => {
  currentPage.value = 1;
  loadFarmers();
};

const approve = async (farmerId) => {
  try {
    await api.approveFarmer(farmerId);
    await loadStats();
    await loadFarmers();
  } catch (error) {
    alert('Failed to approve farmer: ' + error.message);
  }
};

const reject = async (farmerId) => {
  try {
    await api.rejectFarmer(farmerId);
    await loadStats();
    await loadFarmers();
  } catch (error) {
    alert('Failed to reject farmer: ' + error.message);
  }
};

const viewPermit = async (farmerId) => {
  try {
    const response = await api.downloadFarmerPermit(farmerId);
    // Trigger download or open in new window
    window.open(response.url, '_blank');
  } catch (error) {
    alert('Failed to download permit: ' + error.message);
  }
};

const deleteFarmer = async (farmerId) => {
  if (confirm('Are you sure you want to delete this farmer?')) {
    try {
      await api.deleteFarmer(farmerId);
      await loadStats();
      await loadFarmers();
    } catch (error) {
      alert('Failed to delete farmer: ' + error.message);
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

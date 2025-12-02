<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">Manage Farmers</h1>
          <p class="text-gray-600 mt-2">Accept, reject, or delete farmer applications</p>
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
      <div class="mb-6 grid md:grid-cols-2 gap-4">
        <div>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search farmers..." 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>
        <select v-model="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="active">Active</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <!-- Farmers Table -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Farm Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Permit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="filteredFarmers.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No farmers found</td>
              </tr>
              <tr v-for="farmer in filteredFarmers" :key="farmer.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ farmer.name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ farmer.email }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ getFarmName(farmer) }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ getLocation(farmer) }}</td>
                <td class="px-6 py-4 text-sm">
                  <span v-if="farmer.status === 'pending'" class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                    Pending
                  </span>
                  <span v-else-if="farmer.status === 'active'" class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                    Active
                  </span>
                  <span v-else-if="farmer.status === 'rejected'" class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                    Rejected
                  </span>
                </td>
                <td class="px-6 py-4 text-sm">
                  <a 
                    v-if="getPermitUrl(farmer)" 
                    :href="getPermitUrl(farmer)" 
                    target="_blank"
                    class="text-blue-600 hover:text-blue-700 font-medium"
                  >
                    View
                  </a>
                  <span v-else class="text-gray-500">-</span>
                </td>
                <td class="px-6 py-4 text-sm">
                  <div class="flex items-center space-x-2">
                    <button
                      v-if="farmer.status === 'pending'"
                      @click="acceptFarmer(farmer.id)"
                      :disabled="processing"
                      class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition disabled:opacity-50 text-xs font-medium"
                    >
                      Accept
                    </button>
                    <button
                      v-if="farmer.status === 'pending'"
                      @click="rejectFarmer(farmer.id)"
                      :disabled="processing"
                      class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 transition disabled:opacity-50 text-xs font-medium"
                    >
                      Reject
                    </button>
                    <button
                      @click="deleteFarmer(farmer.id, farmer.name)"
                      :disabled="processing"
                      class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition disabled:opacity-50 text-xs font-medium"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-600">
          <p class="text-gray-600 text-sm font-medium">Pending Applications</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ pendingCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
          <p class="text-gray-600 text-sm font-medium">Active Farmers</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ activeCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-600">
          <p class="text-gray-600 text-sm font-medium">Rejected Applications</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ rejectedCount }}</p>
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
const { farmers } = props;

const searchQuery = ref('');
const filterStatus = ref('');
const processing = ref(false);

const filteredFarmers = computed(() => {
  let result = farmers || [];

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(f => 
      f.name.toLowerCase().includes(query) ||
      f.email.toLowerCase().includes(query) ||
      getFarmName(f).toLowerCase().includes(query)
    );
  }

  // Filter by status
  if (filterStatus.value) {
    result = result.filter(f => f.status === filterStatus.value);
  }

  return result;
});

const getFarmName = (farmer) => {
  return farmer.farmerProfile?.business_name || farmer.farmer_profile?.business_name || 'N/A';
};

const getLocation = (farmer) => {
  return farmer.farmerProfile?.location || farmer.farmer_profile?.location || 'N/A';
};

const getPermitUrl = (farmer) => {
  const permitUrl = farmer.farmerProfile?.business_permit_url || farmer.farmer_profile?.business_permit_url;
  if (!permitUrl) return null;
  return route('admin.farmers.permit', farmer.id);
};

const pendingCount = computed(() => {
  return farmers?.filter(f => f.status === 'pending').length || 0;
});

const activeCount = computed(() => {
  return farmers?.filter(f => f.status === 'active').length || 0;
});

const rejectedCount = computed(() => {
  return farmers?.filter(f => f.status === 'rejected').length || 0;
});

const acceptFarmer = async (farmerId) => {
  if (confirm('Accept this farmer application?')) {
    processing.value = true;
    const form = useForm({});
    form.post(route('admin.farmers.accept', farmerId), {
      onFinish: () => {
        processing.value = false;
      },
    });
  }
};

const rejectFarmer = async (farmerId) => {
  if (confirm('Reject this farmer application?')) {
    processing.value = true;
    const form = useForm({});
    form.post(route('admin.farmers.reject', farmerId), {
      onFinish: () => {
        processing.value = false;
      },
    });
  }
};

const deleteFarmer = async (farmerId, farmerName) => {
  if (confirm(`Are you sure you want to delete ${farmerName}'s account? This action cannot be undone.`)) {
    processing.value = true;
    const form = useForm({});
    form.post(route('admin.farmers.delete', farmerId), {
      onFinish: () => {
        processing.value = false;
      },
    });
  }
};
</script>

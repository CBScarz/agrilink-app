<script setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import FarmerDashboard from '../Components/FarmerDashboard.vue';
import BuyerDashboard from '../Components/BuyerDashboard.vue';
import AdminDashboard from '../Components/AdminDashboard.vue';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props.auth;
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Dashboard Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
                <p class="text-gray-600">Welcome, {{ auth.user?.name }}!</p>
            </div>

            <!-- Role-based Dashboard -->
            <template v-if="auth.user?.role === 'farmer'">
                <FarmerDashboard />
            </template>
            <template v-else-if="auth.user?.role === 'buyer'">
                <BuyerDashboard />
            </template>
            <template v-else-if="auth.user?.role === 'admin'">
                <AdminDashboard />
            </template>
            <template v-else>
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600">Loading dashboard...</p>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

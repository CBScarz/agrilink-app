<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const { props } = usePage();
const { auth } = props;

const activeTab = ref('profile');

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Me" />

    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Page Header -->
            <div class="mb-8 flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ auth.user.name }}</h1>
                    <p class="text-gray-600">{{ auth.user.email }}</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-4 gap-6">
                <!-- Sidebar Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                        <nav class="space-y-0">
                            <button
                                @click="activeTab = 'profile'"
                                :class="activeTab === 'profile' ? 'bg-green-50 border-l-4 border-green-600 text-green-600' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full px-6 py-3 text-left font-medium transition flex items-center space-x-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Profile</span>
                            </button>

                            <button
                                @click="activeTab = 'purchases'"
                                :class="activeTab === 'purchases' ? 'bg-green-50 border-l-4 border-green-600 text-green-600' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full px-6 py-3 text-left font-medium transition flex items-center space-x-2 border-t"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Purchases</span>
                            </button>

                            <button
                                @click="activeTab = 'settings'"
                                :class="activeTab === 'settings' ? 'bg-green-50 border-l-4 border-green-600 text-green-600' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full px-6 py-3 text-left font-medium transition flex items-center space-x-2 border-t"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Settings</span>
                            </button>

                            <button
                                @click="activeTab = 'support'"
                                :class="activeTab === 'support' ? 'bg-green-50 border-l-4 border-green-600 text-green-600' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full px-6 py-3 text-left font-medium transition flex items-center space-x-2 border-t"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Support</span>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Profile Tab -->
                    <div v-if="activeTab === 'profile'" class="bg-white rounded-lg shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Profile Information</h2>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-300">{{ auth.user.name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-300">{{ auth.user.email }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                                <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-300 capitalize">{{ auth.user.role }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account Status</label>
                                <div class="flex items-center space-x-2">
                                    <span v-if="auth.user.status === 'active'" class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                        Active
                                    </span>
                                    <span v-else-if="auth.user.status === 'pending'" class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                        Pending Verification
                                    </span>
                                    <span v-else class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                        {{ auth.user.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchases Tab -->
                    <div v-if="activeTab === 'purchases'" class="bg-white rounded-lg shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">My Purchases</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- To Pay -->
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-orange-900">To Pay</h3>
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-3xl font-bold text-orange-900">0</p>
                                <p class="text-sm text-orange-700 mt-1">Awaiting Payment</p>
                            </div>

                            <!-- To Ship -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-blue-900">To Ship</h3>
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9-4v4m0 0v4" />
                                    </svg>
                                </div>
                                <p class="text-3xl font-bold text-blue-900">0</p>
                                <p class="text-sm text-blue-700 mt-1">Being Prepared</p>
                            </div>

                            <!-- To Receive -->
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-purple-900">To Receive</h3>
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-3xl font-bold text-purple-900">0</p>
                                <p class="text-sm text-purple-700 mt-1">In Transit</p>
                            </div>

                            <!-- Completed -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-green-900">Completed</h3>
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-3xl font-bold text-green-900">0</p>
                                <p class="text-sm text-green-700 mt-1">Delivered</p>
                            </div>
                        </div>
                        <div class="mt-8 text-center">
                            <p class="text-gray-600 text-sm">No orders yet. <a href="/products" class="text-green-600 hover:text-green-700 font-semibold">Start shopping</a></p>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div v-if="activeTab === 'settings'" class="bg-white rounded-lg shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Settings</h2>
                        <div class="space-y-6">
                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Notifications</h3>
                                <p class="text-gray-600 text-sm mb-4">Receive updates about your orders</p>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" checked class="rounded text-green-600" />
                                        <span class="text-gray-700">Order updates</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" checked class="rounded text-green-600" />
                                        <span class="text-gray-700">New products</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" class="rounded text-green-600" />
                                        <span class="text-gray-700">Promotional offers</span>
                                    </label>
                                </div>
                            </div>

                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Privacy & Security</h3>
                                <p class="text-gray-600 text-sm mb-4">Manage your account security</p>
                                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                    Change Password
                                </button>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Dangerous Zone</h3>
                                <p class="text-gray-600 text-sm mb-4">These actions cannot be undone</p>
                                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Support Tab -->
                    <div v-if="activeTab === 'support'" class="bg-white rounded-lg shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Help & Support</h2>
                        <div class="space-y-6">
                            <!-- Help Centre -->
                            <div class="border rounded-lg p-6 hover:bg-green-50 transition cursor-pointer">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Help Centre</h3>
                                        <p class="text-gray-600 text-sm">Browse our comprehensive guides and tutorials</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- FAQs -->
                            <div class="border rounded-lg p-6 hover:bg-green-50 transition cursor-pointer">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Frequently Asked Questions</h3>
                                        <p class="text-gray-600 text-sm">Find answers to common questions</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Contact Support -->
                            <div class="border rounded-lg p-6 hover:bg-green-50 transition cursor-pointer">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Contact Our Team</h3>
                                        <p class="text-gray-600 text-sm">Email us for detailed support - support@agrilink.com</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Agricultural green color scheme */
:root {
  --green-primary: #16a34a;
  --green-dark: #15803d;
  --green-light: #dcfce7;
  --green-accent: #22c55e;
}
</style>

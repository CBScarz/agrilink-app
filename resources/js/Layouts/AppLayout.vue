<template>
  <div class="min-h-screen bg-gradient-to-b from-green-50 to-white">
    <!-- Navigation -->
    <nav class="sticky top-0 z-40 bg-white shadow-md border-b-4 border-green-600">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center space-x-2">
            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.5 1.5H9.5V0h1v1.5zM14 3l-.707.707-1.414-1.414.707-.707L14 3zm-8 0l1.414-1.414.707.707-1.414 1.414-.707-.707zM10 5a5 5 0 100 10 5 5 0 000-10zm0 9a4 4 0 110-8 4 4 0 010 8z"/>
            </svg>
            <span class="text-2xl font-bold text-green-700">AgriLink</span>
          </div>

          <!-- Menu -->
          <div class="hidden md:flex space-x-8">
            <Link href="/" class="text-gray-700 hover:text-green-600 transition">Home</Link>
            <Link 
              v-if="auth.user?.role === 'farmer'" 
              href="/farmer/products" 
              class="text-gray-700 hover:text-green-600 transition"
            >
              Products
            </Link>
            <Link 
              v-else-if="auth.user?.role !== 'admin'" 
              href="/products" 
              class="text-gray-700 hover:text-green-600 transition"
            >
              Products
            </Link>
            <Link v-if="auth.user?.role === 'admin'" href="/admin/dashboard" class="text-gray-700 hover:text-green-600 transition font-semibold text-green-600">Admin Dashboard</Link>
            <Link v-if="auth.user && auth.user.role === 'farmer'" href="/dashboard" class="text-gray-700 hover:text-green-600 transition">Dashboard</Link>
          </div>

          <!-- Auth & User Menu -->
          <div class="flex items-center space-x-4">
            <template v-if="auth.user">
              <!-- Cart Icon for Buyers -->
              <Link v-if="auth.user.role === 'buyer'" href="/cart" class="relative text-gray-700 hover:text-green-600 transition" title="Shopping Cart">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ cartCount }}</span>
              </Link>

              <div class="relative">
                <button 
                  @click="dropdownOpen = !dropdownOpen"
                  class="flex items-center space-x-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                >
                  <span class="text-sm font-medium">{{ auth.user.name }}</span>
                  <svg :class="{ 'rotate-180': dropdownOpen }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                  </svg>
                </button>
                <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                  <Link href="/profile" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 border-b transition">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Me
                  </Link>
                  <button 
                    @click="logoutUser"
                    class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                  </button>
                </div>
              </div>
              <!-- Close dropdown when clicking outside -->
              <div v-if="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0"></div>
            </template>
            <template v-else>
              <Link href="/login" class="text-green-600 hover:text-green-700 font-medium">Login</Link>
              <Link href="/register" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Register
              </Link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <slot />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { useCart } from '../composables/useCart.js';

const page = usePage();
const auth = page.props.auth;
const dropdownOpen = ref(false);

const { getTotalItems } = useCart();
const cartCount = computed(() => getTotalItems.value);

const logoutForm = useForm({});

const logoutUser = () => {
  dropdownOpen.value = false;
  logoutForm.post(route('logout'));
};
</script>

<style scoped>
/* Agriculture green color scheme */
:root {
  --green-primary: #16a34a;   /* Agricultural green */
  --green-dark: #15803d;       /* Dark forest green */
  --green-light: #dcfce7;      /* Light mint green */
  --green-accent: #22c55e;     /* Bright lime green */
}
</style>

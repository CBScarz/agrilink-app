<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

const page = usePage();
const auth = page.props.auth;
const products = ref([]);
const loading = ref(true);

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

// Fetch products via API instead of embedding in page
onMounted(async () => {
    try {
        const response = await fetch('/api/products');
        const data = await response.json();
        products.value = data.data || data;
    } catch (error) {
        console.error('Error fetching products:', error);
        products.value = [];
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <Head title="Welcome" />
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Hero Section -->
            <div class="mb-12">
                <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl p-12 text-white shadow-lg">
                    <h1 class="text-4xl font-bold mb-4">Welcome to AgriLink</h1>
                    <p class="text-lg text-green-100 mb-8">
                        Connect farmers with buyers. Fresh produce, fair prices, direct from farm to table.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link href="/products" class="px-6 py-3 bg-white text-green-600 rounded-lg font-semibold hover:bg-green-50 transition">
                            Browse Products
                        </Link>
                        <Link v-if="!auth.user" href="/register" class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-400 transition">
                            Become a Farmer
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg transition border-l-4 border-green-600">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.5 1.5H9.5V0h1v1.5zM14 3l-.707.707-1.414-1.414.707-.707L14 3zm-8 0l1.414-1.414.707.707-1.414 1.414-.707-.707zM10 5a5 5 0 100 10 5 5 0 000-10zm0 9a4 4 0 110-8 4 4 0 010 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Fresh Produce</h3>
                    <p class="text-gray-600">Direct from local farmers. Quality assured, freshness guaranteed.</p>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg transition border-l-4 border-green-500">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 11.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.471 1.106l-5.587 2.793a1 1 0 01-.894 0l-5.587-2.793a1 1 0 01-.471-1.106l1.738-5.42-1.233-.616a1 1 0 01.894-1.79l1.599.8L9 4.323V3a1 1 0 011-1h0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Fair Prices</h3>
                    <p class="text-gray-600">No middlemen. Farmers get paid fairly, buyers save money.</p>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg transition border-l-4 border-green-400">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.808-1.456.5.5 0 00.888.471A5 5 0 1016 13H5.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Easy Ordering</h3>
                    <p class="text-gray-600">Simple checkout process. Multiple payment options available.</p>
                </div>
            </div>

            <!-- Products Preview -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Featured Products</h2>
                <div v-if="products.length > 0" class="grid md:grid-cols-4 gap-6">
                    <Link v-for="product in products.slice(0, 4)" :key="product.id" :href="`/products/${product.id}`" class="bg-white rounded-xl shadow-md hover:shadow-lg transition overflow-hidden cursor-pointer block">
                        <div class="h-48 bg-gradient-to-br from-green-200 to-green-400 flex items-center justify-center hover:from-green-300 hover:to-green-500 transition">
                            <img v-if="product.image_url" :src="`/storage/${product.image_url}`" :alt="product.name" class="w-full h-full object-cover" />
                            <span v-else class="text-green-700 font-semibold">{{ product.category }}</span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900">{{ product.name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ product.category }} • {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}</p>
                            <p class="text-2xl font-bold text-green-600 mt-2">₱{{ product.price }}</p>
                        </div>
                    </Link>
                </div>
                <div v-else class="text-center py-12">
                    <p class="text-gray-600 text-lg">No products available yet. Check back soon!</p>
                </div>
            </div>

            <!-- CTA Section -->
            <div v-if="!auth.user" class="bg-green-100 rounded-xl p-12 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
                <p class="text-lg text-gray-700 mb-8">Join AgriLink today and support local agriculture.</p>
                <Link href="/register" class="px-8 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                    Create Your Account
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

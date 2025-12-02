<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-green-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-green-700 mb-2">AgriLink</h1>
                <p class="text-gray-600">Connect farmers with buyers</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-green-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Create Account</h2>

                <!-- Role Selection (Step 1) -->
                <div v-if="!roleSelected" class="space-y-6">
                    <p class="text-center text-gray-600 mb-8">I want to register as a:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Farmer Option -->
                        <button
                            @click="selectRole('farmer')"
                            type="button"
                            class="p-6 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition cursor-pointer"
                        >
                            <div class="text-3xl mb-3">🌾</div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Farmer</h3>
                            <p class="text-sm text-gray-600">
                                Sell your agricultural products directly to buyers
                            </p>
                        </button>

                        <!-- Buyer Option -->
                        <button
                            @click="selectRole('buyer')"
                            type="button"
                            class="p-6 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition cursor-pointer"
                        >
                            <div class="text-3xl mb-3">🛒</div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Buyer</h3>
                            <p class="text-sm text-gray-600">
                                Buy fresh agricultural products from local farmers
                            </p>
                        </button>
                    </div>
                </div>

                <!-- Registration Form (Step 2) -->
                <div v-else class="space-y-4">
                    <!-- Back Button -->
                    <button
                        @click="roleSelected = false"
                        type="button"
                        class="text-green-600 hover:text-green-700 text-sm font-medium mb-4 flex items-center"
                    >
                        ← Change role
                    </button>

                    <!-- Error Messages -->
                    <div v-if="hasErrors" class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm">
                        <p v-if="form.errors.name" class="text-red-600 mb-2">{{ form.errors.name }}</p>
                        <p v-if="form.errors.email" class="text-red-600 mb-2">{{ form.errors.email }}</p>
                        <p v-if="form.errors.password" class="text-red-600 mb-2">{{ form.errors.password }}</p>
                        <p v-if="form.errors.password_confirmation" class="text-red-600 mb-2">{{ form.errors.password_confirmation }}</p>
                        <p v-if="form.errors.business_name" class="text-red-600 mb-2">{{ form.errors.business_name }}</p>
                        <p v-if="form.errors.business_permit_url" class="text-red-600 mb-2">{{ form.errors.business_permit_url }}</p>
                        <p v-if="form.errors.location" class="text-red-600">{{ form.errors.location }}</p>
                    </div>

                    <!-- Registration Form -->
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Role Badge -->
                        <div class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            Registering as: {{ form.role === 'farmer' ? '🌾 Farmer' : '🛒 Buyer' }}
                        </div>

                        <!-- Name Input -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ roleSelected === 'farmer' ? 'Your Name' : 'Full Name' }}
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :placeholder="roleSelected === 'farmer' ? 'John Farmer' : 'John Doe'"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                required
                                autofocus
                                autocomplete="name"
                            />
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="you@example.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                required
                                autocomplete="username"
                            />
                        </div>

                        <!-- Farmer-Specific Fields -->
                        <template v-if="form.role === 'farmer'">
                            <!-- Business Name -->
                            <div>
                                <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Farm/Business Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="business_name"
                                    v-model="form.business_name"
                                    type="text"
                                    placeholder="Green Valley Farm"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                    required
                                />
                            </div>

                            <!-- Business Permit File Upload -->
                            <div>
                                <label for="business_permit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business License/Permit <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        id="business_permit"
                                        type="file"
                                        accept="image/*,.pdf"
                                        @change="handleFileUpload"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        required
                                    />
                                    <div class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 transition flex items-center justify-center">
                                        <div class="text-center">
                                            <svg v-if="!permitFileName" class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <p v-if="!permitFileName" class="text-gray-600">Click to upload or drag and drop</p>
                                            <p v-else class="text-green-600 font-medium">{{ permitFileName }}</p>
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, PDF up to 10MB</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Farm Location <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="location"
                                    v-model="form.location"
                                    type="text"
                                    placeholder="City, Province"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                    required
                                />
                            </div>
                        </template>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                required
                                autocomplete="new-password"
                            />
                            <p class="text-xs text-gray-500 mt-1">At least 8 characters</p>
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="••••••••"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                required
                                autocomplete="new-password"
                            />
                        </div>

                        <!-- Terms Agreement -->
                        <div class="flex items-start gap-3 pt-2">
                            <input
                                id="agree"
                                v-model="form.agree"
                                type="checkbox"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mt-1"
                                required
                            />
                            <label for="agree" class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-green-600 hover:text-green-700">Terms of Service</a> and <a href="#" class="text-green-600 hover:text-green-700">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 mt-6"
                        >
                            <span v-if="!form.processing">Create {{ form.role === 'farmer' ? 'Farmer' : 'Buyer' }} Account</span>
                            <span v-else>Creating account...</span>
                        </button>
                    </form>
                </div>

                <!-- Divider (Role Selection Only) -->
                <div v-if="!roleSelected" class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or</span>
                    </div>
                </div>

                <!-- Login Link -->
                <div v-if="!roleSelected" class="text-center text-sm text-gray-600">
                    Already have an account?
                    <Link :href="route('login')" class="text-green-600 hover:text-green-700 font-medium transition">
                        Sign in here
                    </Link>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-8">
                By signing up, you agree to our
                <a href="#" class="text-green-600 hover:text-green-700">Terms of Service</a>
                and
                <a href="#" class="text-green-600 hover:text-green-700">Privacy Policy</a>
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const roleSelected = ref(false);
const permitFileName = ref('');

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'buyer',
    // Farmer-specific fields
    business_name: '',
    business_permit: null,
    location: '',
    // Agreement
    agree: false,
});

const selectRole = (role) => {
    form.role = role;
    roleSelected.value = role;
};

const handleFileUpload = (event) => {
    const file = event.target.files?.[0];
    if (file) {
        // Validate file type
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please upload a PNG, JPG, or PDF file');
            event.target.value = '';
            permitFileName.value = '';
            form.business_permit = null;
            return;
        }
        
        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File size must be less than 10MB');
            event.target.value = '';
            permitFileName.value = '';
            form.business_permit = null;
            return;
        }
        
        permitFileName.value = file.name;
        form.business_permit = file;
    }
};

const hasErrors = computed(() => {
    return Object.keys(form.errors).length > 0;
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

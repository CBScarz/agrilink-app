<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-green-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-green-700 mb-2">AgriLink</h1>
                <p class="text-gray-600">Connect farmers with buyers</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-green-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Reset Password</h2>

                <!-- Info Message -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-blue-700 text-sm">
                        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                    </p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-600 text-sm">{{ status }}</p>
                </div>

                <!-- Error Messages -->
                <div v-if="form.errors.email" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-600 text-sm">{{ form.errors.email }}</p>
                </div>

                <!-- Forgot Password Form -->
                <form @submit.prevent="submit" class="space-y-4">
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
                            autofocus
                            autocomplete="username"
                        />
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 mt-6"
                    >
                        <span v-if="!form.processing">Send Reset Link</span>
                        <span v-else>Sending...</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or</span>
                    </div>
                </div>

                <!-- Links -->
                <div class="space-y-3">
                    <!-- Login Link -->
                    <Link
                        :href="route('login')"
                        class="block w-full text-center text-sm text-green-600 hover:text-green-700 font-medium transition"
                    >
                        Back to login
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

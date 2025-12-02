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
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Welcome Back</h2>

                <!-- Status Message -->
                <div v-if="status" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-600 text-sm">{{ status }}</p>
                </div>

                <!-- Error Messages -->
                <div v-if="form.errors.email || form.errors.password" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p v-if="form.errors.email" class="text-red-600 text-sm mb-2">{{ form.errors.email }}</p>
                    <p v-if="form.errors.password" class="text-red-600 text-sm">{{ form.errors.password }}</p>
                </div>

                <!-- Login Form -->
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
                            autocomplete="current-password"
                        />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        />
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 mt-6"
                    >
                        <span v-if="!form.processing">Sign In</span>
                        <span v-else>Signing in...</span>
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
                    <!-- Forgot Password Link -->
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="block w-full text-center text-sm text-green-600 hover:text-green-700 font-medium transition"
                    >
                        Forgot your password?
                    </Link>

                    <!-- Register Link -->
                    <div class="text-center text-sm text-gray-600">
                        Don't have an account?
                        <Link href="/register" class="text-green-600 hover:text-green-700 font-medium transition">
                            Sign up here
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-8">
                By signing in, you agree to our
                <a href="#" class="text-green-600 hover:text-green-700">Terms of Service</a>
                and
                <a href="#" class="text-green-600 hover:text-green-700">Privacy Policy</a>
            </p>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

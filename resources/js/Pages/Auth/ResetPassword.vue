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
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Set New Password</h2>

                <!-- Error Messages -->
                <div v-if="form.errors.email || form.errors.password || form.errors.password_confirmation" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm">
                    <p v-if="form.errors.email" class="text-red-600 mb-2">{{ form.errors.email }}</p>
                    <p v-if="form.errors.password" class="text-red-600 mb-2">{{ form.errors.password }}</p>
                    <p v-if="form.errors.password_confirmation" class="text-red-600">{{ form.errors.password_confirmation }}</p>
                </div>

                <!-- Reset Password Form -->
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
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
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

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 mt-6"
                    >
                        <span v-if="!form.processing">Reset Password</span>
                        <span v-else>Resetting...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

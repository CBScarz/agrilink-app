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
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Confirm Password</h2>

                <!-- Info Message -->
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800 text-sm">
                        This is a secure area of the application. Please confirm your password before continuing.
                    </p>
                </div>

                <!-- Error Messages -->
                <div v-if="form.errors.password" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-600 text-sm">{{ form.errors.password }}</p>
                </div>

                <!-- Confirm Password Form -->
                <form @submit.prevent="submit" class="space-y-4">
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
                            autofocus
                        />
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 mt-6"
                    >
                        <span v-if="!form.processing">Confirm</span>
                        <span v-else>Confirming...</span>
                    </button>
                </form>
            </div>

            <!-- Security Notice -->
            <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">
                    <strong>🔒 Secure:</strong> Your password is encrypted and never shared.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

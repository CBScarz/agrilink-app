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
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Verify Email</h2>

                <!-- Info Message -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-blue-700 text-sm">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                    </p>
                </div>

                <!-- Success Message -->
                <div v-if="verificationLinkSent" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-600 text-sm">
                        A new verification link has been sent to the email address you provided during registration.
                    </p>
                </div>

                <!-- Resend Form -->
                <form @submit.prevent="submit" class="space-y-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                    >
                        <span v-if="!form.processing">Resend Verification Email</span>
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

                <!-- Logout Link -->
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full text-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                >
                    Log Out
                </Link>
            </div>

            <!-- Help Section -->
            <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">
                    <strong>Didn't receive an email?</strong> Check your spam folder or contact us for help.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

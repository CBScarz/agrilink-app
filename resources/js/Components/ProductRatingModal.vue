<template>
  <div v-if="open" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
      <!-- Header -->
      <div class="mb-4">
        <button
          @click="closeModal"
          class="float-right text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <h3 class="text-xl font-bold text-gray-900">Rate Product</h3>
        <p class="text-sm text-gray-600 mt-1">{{ product.name }}</p>
      </div>

      <!-- Rating Stars -->
      <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <p class="text-sm font-medium text-gray-700 mb-3">How would you rate this product?</p>
        <RatingStars v-model="form.rating" />
      </div>

      <!-- Comment Section -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
        <textarea
          v-model="form.comment"
          placeholder="Share your experience with this product..."
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent resize-none"
        />
        <p class="text-xs text-gray-500 mt-1">{{ form.comment.length }}/1000 characters</p>
      </div>

      <!-- Error Message -->
      <div v-if="errors.error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-700">{{ errors.error }}</p>
      </div>

      <!-- Buttons -->
      <div class="flex gap-3">
        <button
          @click="closeModal"
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium"
        >
          Cancel
        </button>
        <button
          @click="submitRating"
          :disabled="loading || form.rating === 0"
          :class="[
            'flex-1 px-4 py-2 rounded-lg text-white font-medium transition',
            loading || form.rating === 0
              ? 'bg-gray-400 cursor-not-allowed'
              : 'bg-green-600 hover:bg-green-700'
          ]"
        >
          {{ loading ? 'Submitting...' : 'Submit Rating' }}
        </button>
      </div>

      <!-- Success Message -->
      <div v-if="success" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm text-green-700">✓ Rating submitted successfully!</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import RatingStars from './RatingStars.vue';

defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  product: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'rated']);

const form = ref({
  rating: 0,
  comment: '',
});

const loading = ref(false);
const success = ref(false);
const errors = ref({});

const closeModal = () => {
  form.value = { rating: 0, comment: '' };
  errors.value = {};
  success.value = false;
  emit('close');
};

const submitRating = async () => {
  if (form.value.rating === 0) {
    errors.value.error = 'Please select a rating.';
    return;
  }

  loading.value = true;
  errors.value = {};
  success.value = false;

  try {
    const response = await fetch(`/api/products/${props.product.id}/ratings`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        rating: form.value.rating,
        comment: form.value.comment || null,
      }),
    });

    if (!response.ok) {
      const data = await response.json();
      errors.value.error = data.error || 'Failed to submit rating.';
      return;
    }

    success.value = true;
    setTimeout(() => {
      emit('rated');
      closeModal();
    }, 1500);
  } catch (error) {
    errors.value.error = 'An error occurred. Please try again.';
    console.error(error);
  } finally {
    loading.value = false;
  }
};
</script>

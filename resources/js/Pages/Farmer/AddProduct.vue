<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8">
        <Link href="/farmer/products" class="text-blue-600 hover:text-blue-800 flex items-center space-x-1 mb-4">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          <span>Back to Products</span>
        </Link>
        <h1 class="text-4xl font-bold text-gray-900">Add New Product</h1>
        <p class="text-gray-600 mt-2">Create a new product listing for your farm</p>
      </div>

      <!-- Error Alert -->
      <div v-if="form.errors && Object.keys(form.errors).length > 0" class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-start space-x-3">
          <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="text-red-800 font-semibold">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-red-700 text-sm mt-2 space-y-1">
              <li v-for="(messages, field) in form.errors" :key="field">
                {{ field }}: {{ Array.isArray(messages) ? messages[0] : messages }}
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-8 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center space-x-3">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-green-800 font-medium">{{ successMessage }}</p>
        </div>
      </div>

      <!-- Form Card -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <form @submit.prevent="submitForm" class="space-y-8">
          <!-- Product Basic Info Section -->
          <div>
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Product Information</h2>
            
            <!-- Product Name -->
            <div class="mb-6">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Product Name <span class="text-red-600">*</span>
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="e.g., Fresh Tomatoes, Organic Carrots"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
              <p v-if="form.errors?.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
            </div>

            <!-- Description -->
            <div class="mb-6">
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description <span class="text-red-600">*</span>
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                placeholder="Describe your product, quality, farming methods, etc."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              ></textarea>
              <p class="text-gray-500 text-xs mt-1">{{ form.description?.length || 0 }}/500 characters</p>
              <p v-if="form.errors?.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
            </div>

            <!-- Category and Availability -->
            <div class="grid md:grid-cols-2 gap-6 mb-6">
              <!-- Category -->
              <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                  Category <span class="text-red-600">*</span>
                </label>
                <select
                  id="category"
                  v-model="form.category"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Select a category</option>
                  <option value="Vegetables">🥬 Vegetables</option>
                  <option value="Fruits">🍎 Fruits</option>
                  <option value="Grains">🌾 Grains</option>
                  <option value="Dairy">🥛 Dairy</option>
                  <option value="Meat">🥩 Meat</option>
                  <option value="Herbs">🌿 Herbs & Spices</option>
                  <option value="Organic">🌱 Organic Products</option>
                  <option value="Other">📦 Other</option>
                </select>
                <p v-if="form.errors?.category" class="text-red-600 text-sm mt-1">{{ form.errors.category }}</p>
              </div>

              <!-- Availability -->
              <div>
                <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">
                  Availability <span class="text-red-600">*</span>
                </label>
                <select
                  id="availability"
                  v-model="form.availability"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="In Stock">✅ In Stock</option>
                  <option value="Limited">⚠️ Limited Stock</option>
                  <option value="Pre-order">📅 Pre-order</option>
                  <option value="Seasonal">🌤️ Seasonal</option>
                </select>
                <p v-if="form.errors?.availability" class="text-red-600 text-sm mt-1">{{ form.errors.availability }}</p>
              </div>
            </div>

            <!-- Product Image -->
            <div class="mb-6">
              <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                Product Image
              </label>
              <div class="flex items-center space-x-4">
                <div v-if="imagePreview" class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-300">
                  <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                </div>
                <div class="flex-1">
                  <label for="image" class="flex items-center justify-center w-full px-4 py-6 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition">
                    <div class="text-center">
                      <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <p class="text-gray-600 text-sm">Click to upload or drag and drop</p>
                      <p class="text-gray-400 text-xs mt-1">PNG, JPG, GIF up to 2MB</p>
                    </div>
                    <input
                      id="image"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="handleImageUpload"
                    />
                  </label>
                </div>
              </div>
              <p v-if="form.errors?.image" class="text-red-600 text-sm mt-1">{{ form.errors.image }}</p>
            </div>
          </div>

          <!-- Pricing & Stock Section -->
          <div>
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Pricing & Stock</h2>
            
            <div class="grid md:grid-cols-3 gap-6">
              <!-- Price -->
              <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                  Price per Unit (₱) <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                  <span class="absolute left-4 top-2.5 text-gray-500 font-medium">₱</span>
                  <input
                    id="price"
                    v-model.number="form.price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  />
                </div>
                <p v-if="form.errors?.price" class="text-red-600 text-sm mt-1">{{ form.errors.price }}</p>
              </div>

              <!-- Stock/Quantity -->
              <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                  Initial Stock <span class="text-red-600">*</span>
                </label>
                <input
                  id="stock"
                  v-model.number="form.stock"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
                <p class="text-gray-500 text-xs mt-1">units available</p>
                <p v-if="form.errors?.stock" class="text-red-600 text-sm mt-1">{{ form.errors.stock }}</p>
              </div>

              <!-- Unit Type -->
              <div>
                <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                  Unit Type <span class="text-red-600">*</span>
                </label>
                <select
                  id="unit"
                  v-model="form.unit"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Select unit</option>
                  <option value="kg">Kilogram (kg)</option>
                  <option value="g">Gram (g)</option>
                  <option value="lb">Pound (lb)</option>
                  <option value="piece">Piece</option>
                  <option value="dozen">Dozen</option>
                  <option value="bundle">Bundle</option>
                  <option value="box">Box</option>
                  <option value="liter">Liter (L)</option>
                  <option value="ml">Milliliter (ml)</option>
                </select>
                <p v-if="form.errors?.unit" class="text-red-600 text-sm mt-1">{{ form.errors.unit }}</p>
              </div>
            </div>
          </div>

          <!-- Product Details Section -->
          <div>
            <h2 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200">Additional Details</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
              <!-- Origin -->
              <div>
                <label for="origin" class="block text-sm font-medium text-gray-700 mb-2">
                  Origin / Farm Location
                </label>
                <input
                  id="origin"
                  v-model="form.origin"
                  type="text"
                  placeholder="e.g., Benguet, Philippines"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
              </div>

              <!-- Harvest Date -->
              <div>
                <label for="harvestDate" class="block text-sm font-medium text-gray-700 mb-2">
                  Harvest Date / Production Date
                </label>
                <input
                  id="harvestDate"
                  v-model="form.harvestDate"
                  type="date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
              </div>

              <!-- Expiration Date -->
              <div>
                <label for="expirationDate" class="block text-sm font-medium text-gray-700 mb-2">
                  Expiration / Best Before Date
                </label>
                <input
                  id="expirationDate"
                  v-model="form.expirationDate"
                  type="date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
              </div>

              <!-- Certification -->
              <div>
                <label for="certification" class="block text-sm font-medium text-gray-700 mb-2">
                  Certification / Badge
                </label>
                <select
                  id="certification"
                  v-model="form.certification"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">None</option>
                  <option value="Organic">🌱 Organic Certified</option>
                  <option value="Fair Trade">🤝 Fair Trade</option>
                  <option value="Non-GMO">🧬 Non-GMO</option>
                  <option value="Pesticide-Free">🚫 Pesticide-Free</option>
                  <option value="Local">🏘️ Local Product</option>
                </select>
              </div>
            </div>

            <!-- Features / Highlights -->
            <div class="mt-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Product Features / Highlights
              </label>
              <div class="space-y-2">
                <label class="flex items-center space-x-3">
                  <input v-model="form.features" type="checkbox" value="Fresh" class="w-4 h-4 text-green-600 rounded">
                  <span class="text-gray-700">🌿 Fresh & Recently Harvested</span>
                </label>
                <label class="flex items-center space-x-3">
                  <input v-model="form.features" type="checkbox" value="Organic" class="w-4 h-4 text-green-600 rounded">
                  <span class="text-gray-700">🌱 100% Organic</span>
                </label>
                <label class="flex items-center space-x-3">
                  <input v-model="form.features" type="checkbox" value="Local" class="w-4 h-4 text-green-600 rounded">
                  <span class="text-gray-700">📍 Local & Sustainable</span>
                </label>
                <label class="flex items-center space-x-3">
                  <input v-model="form.features" type="checkbox" value="Premium" class="w-4 h-4 text-green-600 rounded">
                  <span class="text-gray-700">⭐ Premium Quality</span>
                </label>
                <label class="flex items-center space-x-3">
                  <input v-model="form.features" type="checkbox" value="FastShipping" class="w-4 h-4 text-green-600 rounded">
                  <span class="text-gray-700">🚚 Fast Shipping Available</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <Link
              href="/farmer/products"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
            >
              <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>{{ isSubmitting ? 'Creating...' : 'Create Product' }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Help Section -->
      <div class="mt-12 grid md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
          <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <h3 class="font-semibold text-blue-900 mb-1">Product Tips</h3>
              <p class="text-blue-800 text-sm">Use clear product names and detailed descriptions to attract more buyers. Include harvest dates and certifications when available.</p>
            </div>
          </div>
        </div>

        <div class="bg-green-50 rounded-lg p-6 border border-green-200">
          <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <h3 class="font-semibold text-green-900 mb-1">Pricing Guide</h3>
              <p class="text-green-800 text-sm">Set competitive prices based on market rates. Consider freshness, quality, and certifications when determining your price.</p>
            </div>
          </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
          <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            <div>
              <h3 class="font-semibold text-purple-900 mb-1">Stock Management</h3>
              <p class="text-purple-800 text-sm">Keep your stock updated to avoid over-selling. Low stock alerts will help you manage inventory efficiently.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import { ref, watch } from 'vue';

const successMessage = ref('');
const imagePreview = ref('');

const form = useForm({
  name: '',
  description: '',
  category: '',
  availability: 'In Stock',
  price: '',
  stock: '',
  unit: '',
  origin: '',
  harvestDate: '',
  expirationDate: '',
  certification: '',
  features: [],
  image: null,
});

// Handle image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

// Auto-format description length
watch(() => form.description, (newVal) => {
  if (newVal && newVal.length > 500) {
    form.description = newVal.substring(0, 500);
  }
});

const submitForm = () => {
  successMessage.value = '';
  
  form.transform((data) => ({
    ...data,
    features: data.features.join(',')
  })).post('/farmer/products', {
    onSuccess: () => {
      successMessage.value = 'Product created successfully! Redirecting...';
      setTimeout(() => {
        window.location.href = '/farmer/products';
      }, 1500);
    },
    onError: (errors) => {
      console.error('Form errors:', errors);
    },
  });
};
</script>

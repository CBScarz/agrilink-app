<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Checkout</h1>
        <p class="text-gray-600">Complete your purchase</p>
      </div>

      <!-- Two Column Layout -->
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Left Side: Form -->
        <div class="lg:col-span-2">
          <form @submit.prevent="submitCheckout" class="space-y-6">
            <!-- Error Messages -->
            <div v-if="errors.length > 0" class="p-4 bg-red-50 border border-red-200 rounded-lg">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                  <h3 class="font-semibold text-red-900">Please fix the following errors:</h3>
                  <ul class="mt-2 space-y-1 text-sm text-red-800">
                    <li v-for="(error, index) in errors" :key="index">• {{ error }}</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Delivery Information</h2>
              
              <!-- Saved Addresses Section -->
              <div v-if="savedAddresses.length > 0" class="mb-6 pb-6 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Saved Addresses</h3>
                <div class="space-y-2 mb-4">
                  <div 
                    v-for="address in savedAddresses" 
                    :key="address.id"
                    class="p-3 border border-gray-300 rounded-lg hover:bg-green-50 transition cursor-pointer"
                    :class="{ 'border-green-600 bg-green-50': selectedAddressId === address.id }"
                  >
                    <div class="flex items-start gap-3">
                      <input 
                        type="radio" 
                        :value="address.id"
                        v-model="selectedAddressId"
                        @change="selectSavedAddress(address)"
                        class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0"
                      />
                      <div class="flex-1">
                        <p class="font-medium text-gray-900">
                          {{ address.fullName }}
                          <span v-if="address.isDefault" class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Default</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ address.address }}, {{ address.city }} {{ address.postalCode }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ address.phone }}</p>
                        <div class="flex gap-3 mt-2">
                          <button 
                            @click.prevent="editingAddressId === address.id ? editingAddressId = null : startEditingAddress(address)"
                            type="button"
                            class="text-xs text-blue-600 hover:underline"
                          >
                            {{ editingAddressId === address.id ? 'Cancel' : 'Edit' }}
                          </button>
                          <button 
                            @click.prevent="deleteAddressFromList(address.id)"
                            type="button"
                            class="text-xs text-red-600 hover:underline"
                          >
                            Delete
                          </button>
                          <button 
                            v-if="!address.isDefault"
                            @click.prevent="setDefaultAddressHandler(address.id)"
                            type="button"
                            class="text-xs text-green-600 hover:underline"
                          >
                            Set as Default
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Edit Address Form (inline) -->
                    <div 
                      v-if="editingAddressId === address.id"
                      class="mt-4 p-4 bg-gray-50 rounded border border-gray-200"
                    >
                      <h4 class="font-semibold text-gray-900 mb-3 text-sm">Edit Address</h4>
                      <div class="space-y-3">
                        <input 
                          v-model="editingAddress.fullName"
                          type="text" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 text-sm"
                          placeholder="Full Name"
                        />
                        <input 
                          v-model="editingAddress.phone"
                          type="tel" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 text-sm"
                          placeholder="Phone Number"
                        />
                        <input 
                          v-model="editingAddress.address"
                          type="text" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 text-sm"
                          placeholder="Street Address"
                        />
                        <div class="grid md:grid-cols-2 gap-3">
                          <input 
                            v-model="editingAddress.city"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 text-sm"
                            placeholder="City"
                          />
                          <input 
                            v-model="editingAddress.postalCode"
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 text-sm"
                            placeholder="Postal Code"
                          />
                        </div>
                        <div class="flex gap-2 pt-2">
                          <button 
                            @click.prevent="updateAddressFromList"
                            type="button"
                            class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition"
                          >
                            Save Changes
                          </button>
                          <button 
                            @click.prevent="editingAddressId = null"
                            type="button"
                            class="px-3 py-2 bg-gray-300 text-gray-900 text-sm rounded hover:bg-gray-400 transition"
                          >
                            Cancel
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Add New Address Toggle -->
                <button 
                  v-if="editingAddressId !== 'new'"
                  @click.prevent="editingAddressId = 'new'"
                  type="button"
                  class="text-sm text-green-600 hover:text-green-700 font-medium"
                >
                  + Add New Address
                </button>
              </div>

              <!-- Add/Edit New Address Form -->
              <div 
                v-if="editingAddressId === 'new' || savedAddresses.length === 0"
                class="mb-6 pb-6 border-b border-gray-200"
              >
                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                  {{ savedAddresses.length === 0 ? 'Enter Delivery Address' : 'Add New Address' }}
                </h3>
                
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input 
                      v-model="form.fullName"
                      type="text" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                      placeholder="Enter your full name"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input 
                      v-model="form.email"
                      type="email" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                      placeholder="Enter your email address"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input 
                      v-model="form.phone"
                      type="tel" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                      placeholder="Enter your phone number"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                    <input 
                      v-model="form.address"
                      type="text" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                      placeholder="Enter your street address"
                    />
                  </div>

                  <div class="grid md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                      <input 
                        v-model="form.city"
                        type="text" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        placeholder="Enter your city"
                      />
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                      <input 
                        v-model="form.postalCode"
                        type="text" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                        placeholder="Enter your postal code"
                      />
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea 
                      v-model="form.notes"
                      rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                      placeholder="Add any special delivery instructions"
                    ></textarea>
                  </div>

                  <!-- Save Address Option -->
                  <label class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg cursor-pointer">
                    <input 
                      v-model="shouldSaveAddress"
                      type="checkbox" 
                      class="w-4 h-4 text-green-600"
                    />
                    <span class="text-sm text-gray-700">Save this address for future orders</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Method</h2>
              
              <div class="space-y-3">
                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition" :class="{ 'border-green-600 bg-green-50': form.paymentMethod === 'card' }">
                  <input 
                    v-model="form.paymentMethod"
                    type="radio" 
                    value="card"
                    class="w-4 h-4 text-green-600"
                  />
                  <div class="ml-3">
                    <p class="font-medium text-gray-900">Credit/Debit Card</p>
                    <p class="text-sm text-gray-600">Visa, Mastercard, or Amex</p>
                  </div>
                </label>

                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition" :class="{ 'border-green-600 bg-green-50': form.paymentMethod === 'gcash' }">
                  <input 
                    v-model="form.paymentMethod"
                    type="radio" 
                    value="gcash"
                    class="w-4 h-4 text-green-600"
                  />
                  <div class="ml-3">
                    <p class="font-medium text-gray-900">GCash</p>
                    <p class="text-sm text-gray-600">Mobile wallet payment</p>
                  </div>
                </label>

                <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition" :class="{ 'border-green-600 bg-green-50': form.paymentMethod === 'cod' }">
                  <input 
                    v-model="form.paymentMethod"
                    type="radio" 
                    value="cod"
                    class="w-4 h-4 text-green-600"
                  />
                  <div class="ml-3">
                    <p class="font-medium text-gray-900">Cash on Delivery</p>
                    <p class="text-sm text-gray-600">Pay when you receive your order</p>
                  </div>
                </label>
              </div>
            </div>

            <!-- Card Details (shown when card is selected) -->
            <div v-if="form.paymentMethod === 'card'" class="bg-white rounded-lg shadow p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Card Details</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                  <input 
                    v-model="form.cardholderName"
                    type="text" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                    placeholder="Full name on card"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                  <input 
                    v-model="form.cardNumber"
                    type="text" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                    placeholder="1234 5678 9012 3456"
                    @input="formatCardNumber"
                  />
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input 
                      v-model="form.cardExpiry"
                      type="text" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                      placeholder="MM/YY"
                      @input="formatCardExpiry"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                    <input 
                      v-model="form.cardCvv"
                      type="text" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
                      placeholder="123"
                      maxlength="3"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- GCash Details (shown when GCash is selected) -->
            <div v-if="form.paymentMethod === 'gcash'" class="bg-blue-50 rounded-lg p-4 border border-blue-200">
              <p class="text-sm text-blue-800">
                <span class="font-semibold">GCash Payment:</span> You will receive a GCash payment link after confirming your order. Complete the payment through the secure GCash portal.
              </p>
            </div>

            <!-- Terms & Conditions -->
            <div class="bg-white rounded-lg shadow p-6">
              <label class="flex items-start gap-3">
                <input 
                  v-model="form.agreeTerms"
                  type="checkbox" 
                  class="w-4 h-4 text-green-600 mt-1"
                />
                <span class="text-sm text-gray-700">
                  I agree to the <a href="#" class="text-green-600 hover:underline">Terms & Conditions</a> and <a href="#" class="text-green-600 hover:underline">Privacy Policy</a>
                </span>
              </label>
            </div>

            <!-- Submit Button -->
            <button 
              type="submit"
              :disabled="isProcessing || !form.agreeTerms"
              class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!isProcessing">Confirm & Pay ₱{{ orderTotal.toFixed(2) }}</span>
              <span v-else>Processing...</span>
            </button>

            <!-- Back to Cart -->
            <Link href="/cart" class="block text-center text-gray-600 hover:text-gray-900 transition">
              Back to Cart
            </Link>
          </form>
        </div>

        <!-- Right Side: Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow p-6 sticky top-4">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Order Summary</h3>

            <!-- Buy Now Mode - Quantity Selector -->
            <div v-if="buyNowProduct" class="mb-6 pb-6 border-b border-gray-200">
              <div class="flex items-start gap-4">
                <div class="w-20 h-20 bg-gradient-to-br from-green-200 to-green-400 rounded-lg flex items-center justify-center flex-shrink-0">
                  <img 
                    v-if="buyNowProduct.image_url" 
                    :src="`/storage/${buyNowProduct.image_url}`" 
                    :alt="buyNowProduct.name"
                    class="w-full h-full object-cover rounded-lg"
                  />
                  <span v-else class="text-green-700 font-semibold text-xs text-center px-2">{{ buyNowProduct.name }}</span>
                </div>

                <div class="flex-1">
                  <p class="font-semibold text-gray-900">{{ buyNowProduct.name }}</p>
                  <p class="text-sm text-gray-600 mt-1">By: {{ buyNowProduct.farmer?.name || 'Local Farmer' }}</p>
                  <p class="text-lg font-bold text-green-600 mt-2">₱{{ Number(buyNowProduct.price).toFixed(2) }}/{{ buyNowProduct.unit }}</p>
                  
                  <!-- Quantity Selector -->
                  <div class="flex items-center gap-2 mt-3">
                    <button 
                      @click="buyNowQuantity = Math.max(1, buyNowQuantity - 1)"
                      class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-100 transition"
                    >
                      −
                    </button>
                    <input 
                      v-model.number="buyNowQuantity" 
                      type="number" 
                      min="1"
                      :max="buyNowProduct.stock"
                      class="w-16 px-2 py-1 text-center border border-gray-300 rounded focus:ring-2 focus:ring-green-600"
                    />
                    <button 
                      @click="buyNowQuantity = Math.min(buyNowProduct.stock, buyNowQuantity + 1)"
                      :disabled="buyNowQuantity >= buyNowProduct.stock"
                      class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      +
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Items -->
            <div v-if="groupedItems.length > 0" class="space-y-4 mb-6 pb-6 border-b border-gray-200 max-h-96 overflow-y-auto">
              <div v-for="farmerGroup in groupedItems" :key="farmerGroup.farmerId" class="text-sm">
                <p class="font-semibold text-gray-900 mb-2">{{ farmerGroup.farmerName }}</p>
                <div v-for="item in farmerGroup.items" :key="item.id" class="flex justify-between text-gray-600 ml-2 mb-2">
                  <span>{{ item.product.name }} x{{ item.quantity }}</span>
                  <span>₱{{ (item.quantity * item.product.price).toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
              <p>No items in your order</p>
              <Link href="/products" class="text-green-600 hover:underline mt-2 inline-block">
                Continue Shopping
              </Link>
            </div>

            <!-- Summary Breakdown -->
            <div v-if="groupedItems.length > 0" class="space-y-3 mb-4 pb-4 border-b border-gray-200">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold text-gray-900">₱{{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Delivery Fee</span>
                <span class="font-semibold text-gray-900">₱{{ deliveryFee.toFixed(2) }}</span>
              </div>
              <div v-if="discount > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Discount</span>
                <span class="font-semibold text-green-600">-₱{{ discount.toFixed(2) }}</span>
              </div>
            </div>

            <!-- Total -->
            <div v-if="groupedItems.length > 0" class="flex justify-between mb-6">
              <span class="text-lg font-bold text-gray-900">Total</span>
              <span class="text-2xl font-bold text-green-600">₱{{ orderTotal.toFixed(2) }}</span>
            </div>

            <!-- Product Count -->
            <div v-if="groupedItems.length > 0" class="text-xs text-gray-600 text-center p-3 bg-gray-50 rounded">
              {{ totalItems }} {{ totalItems === 1 ? 'item' : 'items' }} from {{ groupedItems.length }} {{ groupedItems.length === 1 ? 'farmer' : 'farmers' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { useCart } from '../composables/useCart.js';
import { useSavedAddresses } from '../composables/useSavedAddresses.js';

const { props } = usePage();
const { auth } = props;
const { cartItems } = useCart();
const { savedAddresses, selectedAddressId, initializeSavedAddresses, addAddress, updateAddress, deleteAddress, setDefaultAddress } = useSavedAddresses();

const isProcessing = ref(false);
const errors = ref([]);
const buyNowProduct = ref(null);
const buyNowQuantity = ref(1);
const shouldSaveAddress = ref(false);
const editingAddressId = ref(null);
const editingAddress = ref({
  fullName: '',
  phone: '',
  address: '',
  city: '',
  postalCode: '',
});

const form = ref({
  fullName: auth.user?.name || '',
  email: auth.user?.email || '',
  phone: '',
  address: '',
  city: '',
  postalCode: '',
  notes: '',
  paymentMethod: 'cod',
  cardholderName: '',
  cardNumber: '',
  cardExpiry: '',
  cardCvv: '',
  agreeTerms: false,
});

const deliveryFee = ref(50);
const discount = ref(0);

// Determine items to checkout (either from cart or buy-now)
const checkoutItems = computed(() => {
  if (buyNowProduct.value) {
    return [{
      id: `${buyNowProduct.value.id}-${buyNowProduct.value.farmer_id}-buy-now`,
      product: buyNowProduct.value,
      quantity: buyNowQuantity.value
    }];
  }
  return cartItems.value;
});

const groupedItems = computed(() => {
  const grouped = {};
  
  checkoutItems.value.forEach(item => {
    const farmerId = item.product.farmer_id;
    const farmerName = item.product.farmer?.name || 'Unknown Farmer';
    
    if (!grouped[farmerId]) {
      grouped[farmerId] = {
        farmerId: farmerId,
        farmerName: farmerName,
        items: []
      };
    }
    
    grouped[farmerId].items.push(item);
  });
  
  return Object.values(grouped).sort((a, b) => 
    a.farmerName.localeCompare(b.farmerName)
  );
});

const subtotal = computed(() => {
  return checkoutItems.value.reduce((sum, item) => sum + (item.quantity * item.product.price), 0);
});

const totalItems = computed(() => {
  return checkoutItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

const orderTotal = computed(() => {
  return subtotal.value + deliveryFee.value - discount.value;
});

const formatCardNumber = (e) => {
  let value = e.target.value.replace(/\s/g, '');
  let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
  form.value.cardNumber = formattedValue;
};

const formatCardExpiry = (e) => {
  let value = e.target.value.replace(/\D/g, '');
  if (value.length >= 2) {
    value = value.slice(0, 2) + '/' + value.slice(2, 4);
  }
  form.value.cardExpiry = value;
};

const validateForm = () => {
  errors.value = [];

  if (!form.value.fullName.trim()) {
    errors.value.push('Full name is required');
  }

  if (!form.value.email.trim()) {
    errors.value.push('Email address is required');
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.push('Email address is invalid');
  }

  if (!form.value.phone.trim()) {
    errors.value.push('Phone number is required');
  }

  if (!form.value.address.trim()) {
    errors.value.push('Street address is required');
  }

  if (!form.value.city.trim()) {
    errors.value.push('City is required');
  }

  if (!form.value.postalCode.trim()) {
    errors.value.push('Postal code is required');
  }

  if (form.value.paymentMethod === 'card') {
    if (!form.value.cardholderName.trim()) {
      errors.value.push('Cardholder name is required');
    }

    if (!form.value.cardNumber.trim()) {
      errors.value.push('Card number is required');
    } else if (!/^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/.test(form.value.cardNumber)) {
      errors.value.push('Card number must be 16 digits');
    }

    if (!form.value.cardExpiry.trim()) {
      errors.value.push('Card expiry date is required');
    } else if (!/^\d{2}\/\d{2}$/.test(form.value.cardExpiry)) {
      errors.value.push('Card expiry date must be in MM/YY format');
    }

    if (!form.value.cardCvv.trim()) {
      errors.value.push('CVV is required');
    } else if (!/^\d{3}$/.test(form.value.cardCvv)) {
      errors.value.push('CVV must be 3 digits');
    }
  }

  if (!form.value.agreeTerms) {
    errors.value.push('You must agree to the terms and conditions');
  }

  return errors.value.length === 0;
};

// Select a saved address and populate the form
const selectSavedAddress = (address) => {
  form.value.fullName = address.fullName;
  form.value.phone = address.phone;
  form.value.address = address.address;
  form.value.city = address.city;
  form.value.postalCode = address.postalCode;
};

// Start editing an address
const startEditingAddress = (address) => {
  editingAddressId.value = address.id;
  editingAddress.value = { ...address };
};

// Delete address from list
const deleteAddressFromList = (id) => {
  if (confirm('Are you sure you want to delete this address?')) {
    deleteAddress(id);
    if (selectedAddressId.value === id) {
      selectedAddressId.value = null;
    }
  }
};

// Update address from inline edit form
const updateAddressFromList = () => {
  const addressToUpdate = savedAddresses.value.find(addr => addr.id === editingAddressId.value);
  if (addressToUpdate) {
    updateAddress(editingAddressId.value, editingAddress.value);
    editingAddressId.value = null;
  }
};

// Set default address
const setDefaultAddressHandler = (id) => {
  setDefaultAddress(id);
};

// Save address after checkout (when checkbox is checked)
const saveCurrentAddressIfNeeded = () => {
  if (shouldSaveAddress.value) {
    const addressData = {
      fullName: form.value.fullName,
      phone: form.value.phone,
      address: form.value.address,
      city: form.value.city,
      postalCode: form.value.postalCode,
      isDefault: savedAddresses.value.length === 0, // First address is default
    };
    addAddress(addressData);
  }
};

const submitCheckout = async () => {
  if (!validateForm()) {
    window.scrollTo(0, 0);
    return;
  }

  if (checkoutItems.value.length === 0) {
    errors.value = ['Your cart is empty'];
    return;
  }

  isProcessing.value = true;

  try {
    // Save address if user opted to
    saveCurrentAddressIfNeeded();

    // Prepare order data grouped by farmer
    const ordersByFarmer = {};
    
    groupedItems.value.forEach(farmerGroup => {
      ordersByFarmer[farmerGroup.farmerId] = {
        farmer_id: farmerGroup.farmerId,
        items: farmerGroup.items.map(item => ({
          product_id: item.product.id,
          quantity: item.quantity,
          unit_price: item.product.price,
        })),
        payment_method: form.value.paymentMethod,
        delivery_info: {
          fullName: form.value.fullName,
          email: form.value.email,
          phone: form.value.phone,
          address: form.value.address,
          city: form.value.city,
          postalCode: form.value.postalCode,
          notes: form.value.notes,
        }
      };
    });

    // Submit orders to the server
    const response = await fetch('/api/buyer/orders/checkout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({
        orders: Object.values(ordersByFarmer),
      }),
    });

    const data = await response.json();

    if (!response.ok) {
      errors.value = [data.message || 'Failed to process checkout'];
      window.scrollTo(0, 0);
      return;
    }

    // Clear buy-now product from storage
    if (buyNowProduct.value) {
      sessionStorage.removeItem('buyNowProduct');
    }

    // Redirect based on payment method
    if (form.value.paymentMethod === 'gcash' && data.payment_link) {
      window.location.href = data.payment_link;
    } else {
      // Redirect to success page or dashboard
      window.location.href = `/orders?success=true`;
    }
  } catch (error) {
    console.error('Checkout error:', error);
    errors.value = ['An error occurred while processing your checkout. Please try again.'];
    window.scrollTo(0, 0);
  } finally {
    isProcessing.value = false;
  }
};

// Load buy-now product on mount
onMounted(() => {
  initializeSavedAddresses();
  
  const storedProduct = sessionStorage.getItem('buyNowProduct');
  if (storedProduct) {
    try {
      buyNowProduct.value = JSON.parse(storedProduct);
    } catch (e) {
      console.error('Error parsing buy-now product:', e);
    }
  }

  // Load default address if available
  if (savedAddresses.value.length > 0) {
    const defaultAddr = savedAddresses.value.find(addr => addr.isDefault);
    if (defaultAddr) {
      selectSavedAddress(defaultAddr);
      selectedAddressId.value = defaultAddr.id;
    }
  }
});
</script>

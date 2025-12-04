import { ref, computed, watch } from 'vue';

const STORAGE_KEY = 'agrilink_cart';
const DEV_MODE = import.meta.env.DEV;

// Shared cart state across all components
const cartItems = ref([]);
const isLoaded = ref(false);

/**
 * Initialize cart from localStorage on first use
 * In production, only load cart if there's no auth user (guest checkout)
 */
const initializeCart = () => {
  if (isLoaded.value) return;

  try {
    // In production, clear any legacy test data from localStorage on first load
    if (!DEV_MODE && typeof window !== 'undefined') {
      const stored = localStorage.getItem(STORAGE_KEY);
      // If there's data in localStorage, it's from a previous session - clear it
      if (stored) {
        localStorage.removeItem(STORAGE_KEY);
      }
    }
    
    // Only load from localStorage in development mode
    // Production should use server-side cart management
    if (DEV_MODE) {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored) {
        cartItems.value = JSON.parse(stored);
      }
    }
  } catch (error) {
    console.error('Error loading cart from localStorage:', error);
    cartItems.value = [];
  }
  
  isLoaded.value = true;
};

/**
 * Persist cart to localStorage (development only)
 * In production, cart should be managed server-side for authenticated users
 */
const persistCart = () => {
  // Only persist to localStorage in development mode
  if (!DEV_MODE) {
    return; // Production: Don't expose cart data in localStorage
  }
  
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cartItems.value));
  } catch (error) {
    console.error('Error saving cart to localStorage:', error);
  }
};

/**
 * Add item to cart or increase quantity if already exists
 * Same product from same farmer = increase quantity
 * Same product from different farmer = add as separate item
 */
const addToCart = (product, quantity = 1) => {
  initializeCart();
  
  // Check if product already exists in cart from the SAME farmer
  const existingItem = cartItems.value.find(item => 
    item.product.id === product.id && item.product.farmer_id === product.farmer_id
  );
  
  if (existingItem) {
    // Increase quantity, respecting stock limits
    const newQuantity = existingItem.quantity + quantity;
    existingItem.quantity = Math.min(newQuantity, product.stock);
  } else {
    // Add new item to cart
    const quantity_to_add = Math.min(quantity, product.stock);
    cartItems.value.push({
      id: `${product.id}-${product.farmer_id}-${Date.now()}`, // Unique ID includes farmer
      product: product,
      quantity: quantity_to_add
    });
  }
  
  persistCart();
  return existingItem ? 'updated' : 'added';
};

/**
 * Remove item from cart
 */
const removeFromCart = (cartItemId) => {
  initializeCart();
  const index = cartItems.value.findIndex(item => item.id === cartItemId);
  
  if (index > -1) {
    cartItems.value.splice(index, 1);
    persistCart();
    return true;
  }
  
  return false;
};

/**
 * Update quantity of an item
 */
const updateQuantity = (cartItemId, quantity) => {
  initializeCart();
  const item = cartItems.value.find(item => item.id === cartItemId);
  
  if (item) {
    if (quantity <= 0) {
      removeFromCart(cartItemId);
    } else {
      item.quantity = Math.min(quantity, item.product.stock);
      persistCart();
    }
    return true;
  }
  
  return false;
};

/**
 * Clear all items from cart
 */
const clearCart = () => {
  cartItems.value = [];
  persistCart();
};

/**
 * Get total number of items in cart
 */
const getTotalItems = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

/**
 * Get subtotal (sum of all items * quantity)
 */
const getSubtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.quantity * item.product.price), 0);
});

/**
 * Get items grouped by farmer
 */
const getItemsGroupedByFarmer = computed(() => {
  const grouped = {};
  
  cartItems.value.forEach(item => {
    const farmerId = item.product.farmer_id;
    const farmerName = item.product.farmer?.name || 'Unknown Farmer';
    
    if (!grouped[farmerId]) {
      grouped[farmerId] = {
        farmerId: farmerId,
        farmerName: farmerName,
        farmerLocation: item.product.farmer?.farmerProfile?.location || 'N/A',
        items: []
      };
    }
    
    grouped[farmerId].items.push(item);
  });
  
  // Convert to array and sort by farmer name
  return Object.values(grouped).sort((a, b) => 
    a.farmerName.localeCompare(b.farmerName)
  );
});

/**
 * Main composable hook to use cart across components
 */
export const useCart = () => {
  // Initialize on first use
  if (!isLoaded.value) {
    initializeCart();
  }

  return {
    cartItems,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    getTotalItems,
    getSubtotal,
    getItemsGroupedByFarmer
  };
};

// Watch for changes and persist (optional, for extra safety)
watch(cartItems, () => {
  if (isLoaded.value) {
    persistCart();
  }
}, { deep: true });

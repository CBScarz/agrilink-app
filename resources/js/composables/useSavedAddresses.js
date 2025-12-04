import { ref, computed } from 'vue';

export const useSavedAddresses = () => {
  const savedAddresses = ref([]);
  const selectedAddressId = ref(null);

  // Initialize saved addresses from localStorage
  const initializeSavedAddresses = () => {
    try {
      const stored = localStorage.getItem('savedAddresses');
      if (stored) {
        savedAddresses.value = JSON.parse(stored);
      }
    } catch (e) {
      console.error('Error loading saved addresses:', e);
      savedAddresses.value = [];
    }
  };

  // Save addresses to localStorage
  const persistAddresses = () => {
    try {
      localStorage.setItem('savedAddresses', JSON.stringify(savedAddresses.value));
    } catch (e) {
      console.error('Error saving addresses:', e);
    }
  };

  // Add a new address
  const addAddress = (address) => {
    const newAddress = {
      id: Date.now().toString(),
      ...address,
      createdAt: new Date().toISOString(),
    };
    savedAddresses.value.push(newAddress);
    persistAddresses();
    return newAddress;
  };

  // Update an existing address
  const updateAddress = (id, updatedAddress) => {
    const index = savedAddresses.value.findIndex(addr => addr.id === id);
    if (index !== -1) {
      savedAddresses.value[index] = {
        ...savedAddresses.value[index],
        ...updatedAddress,
      };
      persistAddresses();
      return true;
    }
    return false;
  };

  // Delete an address
  const deleteAddress = (id) => {
    const index = savedAddresses.value.findIndex(addr => addr.id === id);
    if (index !== -1) {
      savedAddresses.value.splice(index, 1);
      if (selectedAddressId.value === id) {
        selectedAddressId.value = null;
      }
      persistAddresses();
      return true;
    }
    return false;
  };

  // Set an address as default
  const setDefaultAddress = (id) => {
    savedAddresses.value.forEach(addr => {
      addr.isDefault = addr.id === id;
    });
    selectedAddressId.value = id;
    persistAddresses();
  };

  // Get selected address
  const selectedAddress = computed(() => {
    return savedAddresses.value.find(addr => addr.id === selectedAddressId.value) || null;
  });

  // Get default address
  const defaultAddress = computed(() => {
    return savedAddresses.value.find(addr => addr.isDefault) || null;
  });

  return {
    savedAddresses,
    selectedAddressId,
    selectedAddress,
    defaultAddress,
    initializeSavedAddresses,
    addAddress,
    updateAddress,
    deleteAddress,
    setDefaultAddress,
  };
};

/**
 * API Configuration and Service Client
 * Centralized API calls for all endpoints
 */

const BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

class ApiClient {
  constructor() {
    this.token = localStorage.getItem('auth_token');
    this.headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    if (this.token) {
      this.headers['Authorization'] = `Bearer ${this.token}`;
    }
  }

  setToken(token) {
    this.token = token;
    this.headers['Authorization'] = `Bearer ${token}`;
    localStorage.setItem('auth_token', token);
  }

  clearToken() {
    this.token = null;
    delete this.headers['Authorization'];
    localStorage.removeItem('auth_token');
  }

  async request(method, endpoint, data = null) {
    const url = `${BASE_URL}${endpoint}`;
    const options = {
      method,
      headers: this.headers,
    };

    if (data) {
      options.body = JSON.stringify(data);
    }

    try {
      const response = await fetch(url, options);
      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.message || 'API request failed');
      }

      return result;
    } catch (error) {
      console.error(`API Error [${method} ${endpoint}]:`, error);
      throw error;
    }
  }

  // ===== Authentication =====
  async register(name, email, password, role) {
    const response = await this.request('POST', '/auth/register', {
      name,
      email,
      password,
      role,
    });
    this.setToken(response.token);
    return response;
  }

  async login(email, password) {
    const response = await this.request('POST', '/auth/login', {
      email,
      password,
    });
    this.setToken(response.token);
    return response;
  }

  async logout() {
    await this.request('POST', '/auth/logout');
    this.clearToken();
  }

  async getMe() {
    return this.request('GET', '/auth/me');
  }

  async updateProfile(data) {
    return this.request('PATCH', '/auth/profile', data);
  }

  // ===== Public Products =====
  async getProducts(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    return this.request('GET', `/products?${query}`);
  }

  async getProduct(productId) {
    return this.request('GET', `/products/${productId}`);
  }

  async getProductsByCategory(category) {
    return this.request('GET', `/products/category/${category}`);
  }

  async getProductsByFarmer(farmerId) {
    return this.request('GET', `/products/farmer/${farmerId}`);
  }

  // ===== Farmer Dashboard =====
  async getFarmerDashboard() {
    return this.request('GET', '/farmer/dashboard');
  }

  async updateFarmerProfile(data) {
    return this.request('PATCH', '/farmer/profile', data);
  }

  // ===== Farmer Products =====
  async getFarmerProducts(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    return this.request('GET', `/farmer/products?${query}`);
  }

  async createProduct(data) {
    return this.request('POST', '/farmer/products', data);
  }

  async createProductWithImage(formData) {
    const url = `${BASE_URL}/farmer/products`;
    const options = {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json',
      },
    };
    options.body = formData;

    try {
      const response = await fetch(url, options);
      
      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      let result = {};
      
      if (contentType && contentType.includes('application/json')) {
        result = await response.json();
      } else {
        const text = await response.text();
        console.error('Non-JSON response:', text);
        throw new Error('Server returned invalid response. Check console for details.');
      }

      if (!response.ok) {
        const error = new Error(result.message || result.error || 'Product creation failed');
        error.response = {
          status: response.status,
          data: result
        };
        throw error;
      }

      return result;
    } catch (error) {
      console.error('API Error [POST /farmer/products]:', error);
      if (!error.response) {
        error.response = {
          status: 500,
          data: { message: error.message }
        };
      }
      throw error;
    }
  }

  async updateProduct(productId, data) {
    return this.request('PATCH', `/farmer/products/${productId}`, data);
  }

  async deleteProduct(productId) {
    return this.request('DELETE', `/farmer/products/${productId}`);
  }

  async getFarmerProductStats() {
    return this.request('GET', '/farmer/products/stats');
  }

  // ===== Farmer Orders =====
  async getFarmerOrders(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    return this.request('GET', `/farmer/orders?${query}`);
  }

  async getFarmerOrderStats() {
    return this.request('GET', '/farmer/orders/stats');
  }

  async updateOrderStatus(orderId, status) {
    return this.request('PATCH', `/farmer/orders/${orderId}/status`, { status });
  }

  // ===== Buyer Orders =====
  async createOrder(items, shippingAddress) {
    return this.request('POST', '/buyer/orders', {
      items,
      shipping_address: shippingAddress,
    });
  }

  async getBuyerOrders(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    return this.request('GET', `/buyer/orders?${query}`);
  }

  async getBuyerOrder(orderId) {
    return this.request('GET', `/buyer/orders/${orderId}`);
  }

  async cancelOrder(orderId) {
    return this.request('POST', `/buyer/orders/${orderId}/cancel`);
  }

  // ===== Admin Farmers =====
  async getAdminFarmers(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    return this.request('GET', `/admin/farmers?${query}`);
  }

  async getAdminFarmerStats() {
    return this.request('GET', '/admin/farmers/stats');
  }

  async approveFarmer(farmerId) {
    return this.request('POST', `/admin/farmers/${farmerId}/approve`);
  }

  async rejectFarmer(farmerId) {
    return this.request('POST', `/admin/farmers/${farmerId}/reject`);
  }

  async deleteFarmer(farmerId) {
    return this.request('DELETE', `/admin/farmers/${farmerId}`);
  }

  async downloadFarmerPermit(farmerId) {
    return this.request('GET', `/admin/farmers/${farmerId}/permit`);
  }

  // ===== Admin Products =====
  async getAdminProducts(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    return this.request('GET', `/admin/products?${query}`);
  }

  async getAdminProductStats() {
    return this.request('GET', '/admin/products/stats');
  }

  async updateProductStock(productId, stock) {
    return this.request('PATCH', `/admin/products/${productId}/stock`, { stock });
  }

  async deleteAdminProduct(productId) {
    return this.request('DELETE', `/admin/products/${productId}`);
  }

  // ===== Admin Orders =====
  async getAdminOrders(filters = {}) {
    const query = new URLSearchParams(filters).toString();
    return this.request('GET', `/admin/orders?${query}`);
  }

  async getAdminOrderStats() {
    return this.request('GET', '/admin/orders/stats');
  }

  async updateAdminOrderStatus(orderId, status) {
    return this.request('PATCH', `/admin/orders/${orderId}/status`, { status });
  }

  async deleteAdminOrder(orderId) {
    return this.request('DELETE', `/admin/orders/${orderId}`);
  }
}

export default new ApiClient();

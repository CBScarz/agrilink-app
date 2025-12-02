# REST API Migration Guide

## Overview
AgriLink has been successfully migrated from **Inertia.js** to a **Pure REST API** architecture. This document explains the changes and how to use the new API-driven frontend.

## Key Changes

### Frontend Architecture
- **Before**: Inertia.js (server-rendered Vue with automatic syncing)
- **After**: REST API with client-side state management

### Benefits of REST API
1. **Decoupled**: Frontend and backend are completely independent
2. **Scalable**: Can serve mobile apps, web apps, or any client
3. **Flexible**: Use any frontend framework or technology
4. **Cacheable**: API responses can be cached easily
5. **Stateless**: Each request contains all needed information

### Trade-offs
- **More Code**: Frontend needs to handle API calls, error handling, loading states
- **Manual State Management**: Must manage app state in frontend (use Pinia, Vuex, or Context API)
- **Page Refresh**: Client-side navigation doesn't require full page refresh (better UX with router)

## API Structure

### Base URL
```
http://localhost:8000/api
```

### Authentication
All protected endpoints require Bearer token:
```
Authorization: Bearer {token}
```

### Response Format
```json
{
  "message": "Success message (optional)",
  "user": { /* data */ },
  "data": [],
  "pagination": { /* info */ },
  "token": "auth_token"
}
```

## Frontend Setup

### 1. API Client Service
Use the centralized `resources/js/services/api.js` for all API calls:

```javascript
import api from '@/services/api';

// Auth
await api.login(email, password);
await api.logout();
await api.register(name, email, password, role);

// Products
const products = await api.getProducts({ category: 'Vegetables' });
await api.createProduct(productData);
await api.updateProduct(productId, data);
await api.deleteProduct(productId);

// Orders
await api.createOrder(items, shippingAddress);
const orders = await api.getBuyerOrders();
```

### 2. Vue Component Pattern

```vue
<template>
  <div>
    <div v-if="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <!-- Content -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const loading = ref(true);
const error = ref(null);
const data = ref(null);

onMounted(async () => {
  try {
    data.value = await api.getEndpoint();
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
});
</script>
```

### 3. Environment Variables
Create `.env.local`:
```
VITE_API_URL=http://localhost:8000/api
```

## Migration Steps for Components

### Old Inertia Pattern
```vue
<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const products = computed(() => page.props.products);
</script>
```

### New REST API Pattern
```vue
<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const products = ref([]);

onMounted(async () => {
  products.value = await api.getProducts();
});
</script>
```

## State Management Recommendation

For larger applications, use **Pinia** (recommended for Vue 3):

```bash
npm install pinia
```

Create store: `src/stores/productStore.js`
```javascript
import { defineStore } from 'pinia';
import api from '@/services/api';

export const useProductStore = defineStore('product', () => {
  const products = ref([]);
  const loading = ref(false);

  async function fetchProducts() {
    loading.value = true;
    try {
      products.value = await api.getProducts();
    } finally {
      loading.value = false;
    }
  }

  return { products, loading, fetchProducts };
});
```

Use in component:
```vue
<script setup>
import { useProductStore } from '@/stores/productStore';

const store = useProductStore();

onMounted(() => {
  store.fetchProducts();
});
</script>
```

## Error Handling

### API Client Errors
The API client automatically catches errors and throws them. Handle in components:

```javascript
try {
  await api.createProduct(data);
} catch (error) {
  console.error(error.message); // "Product created successfully" or error description
}
```

### Global Error Handler
Add middleware for common errors:

```javascript
// In api.js or main.js
router.afterEach((to, from, failure) => {
  if (failure?.type === 401) {
    // Redirect to login
    router.push('/login');
  }
});
```

## Routing Setup

### Vue Router Configuration
```javascript
// router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import LoginPage from '@/Pages/Auth/Login.vue';
import FarmerDashboard from '@/Pages/Api/FarmerDashboard.vue';
import AdminDashboard from '@/Pages/Api/AdminDashboard.vue';

const routes = [
  { path: '/login', component: LoginPage },
  { path: '/farmer/dashboard', component: FarmerDashboard, meta: { requiresAuth: true, role: 'farmer' } },
  { path: '/admin/dashboard', component: AdminDashboard, meta: { requiresAuth: true, role: 'admin' } },
  // ... more routes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard
router.beforeEach((to, from) => {
  const token = localStorage.getItem('auth_token');
  
  if (to.meta.requiresAuth && !token) {
    return '/login';
  }
  
  // Role checking
  if (to.meta.role && userRole !== to.meta.role) {
    return '/unauthorized';
  }
});

export default router;
```

## API Documentation

All endpoints are documented in `API_DOCUMENTATION.md`. Key endpoints:

### Auth
- `POST /auth/register` - Create account
- `POST /auth/login` - Sign in
- `POST /auth/logout` - Sign out
- `GET /auth/me` - Current user info

### Products
- `GET /products` - List all products
- `POST /farmer/products` - Create product
- `PATCH /farmer/products/{id}` - Update product
- `DELETE /farmer/products/{id}` - Delete product

### Orders
- `POST /buyer/orders` - Create order
- `GET /buyer/orders` - My orders
- `POST /buyer/orders/{id}/cancel` - Cancel order

### Admin
- `GET /admin/farmers` - Manage farmers
- `POST /admin/farmers/{id}/approve` - Approve farmer
- `GET /admin/products` - Manage products
- `GET /admin/orders` - View all orders

## Example: Complete Product Page

```vue
<template>
  <div class="container">
    <div v-if="loading" class="spinner">Loading...</div>
    <div v-else>
      <!-- Filters -->
      <input v-model="filters.search" placeholder="Search..." @input="applyFilters" />
      <select v-model="filters.category" @change="applyFilters">
        <option value="">All Categories</option>
        <option>Vegetables</option>
        <option>Fruits</option>
      </select>

      <!-- Products Grid -->
      <div class="grid">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
          @add-to-cart="addToCart"
        />
      </div>

      <!-- Pagination -->
      <Pagination
        :current-page="currentPage"
        :last-page="pagination.last_page"
        @change="changePage"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import api from '@/services/api';

const loading = ref(true);
const products = ref([]);
const currentPage = ref(1);
const pagination = ref({});

const filters = reactive({
  search: '',
  category: '',
});

onMounted(async () => {
  await loadProducts();
});

const loadProducts = async () => {
  loading.value = true;
  try {
    const response = await api.getProducts({
      search: filters.search,
      category: filters.category,
      page: currentPage.value,
    });
    products.value = response.data;
    pagination.value = response.pagination;
  } finally {
    loading.value = false;
  }
};

const applyFilters = () => {
  currentPage.value = 1;
  loadProducts();
};

const changePage = (page) => {
  currentPage.value = page;
  loadProducts();
};

const addToCart = (productId) => {
  // Add to cart logic
};
</script>
```

## Testing API Endpoints

### Using cURL
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"farmer@test.com","password":"password123"}'

# Get products
curl http://localhost:8000/api/products

# Create product (requires auth)
curl -X POST http://localhost:8000/api/farmer/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Tomatoes",
    "description": "Fresh tomatoes",
    "price": 250,
    "stock": 100,
    "category": "Vegetables"
  }'
```

### Using Postman
1. Create collection: "AgriLink API"
2. Add variables: `base_url`, `token`
3. Create requests for each endpoint
4. Use pre-request scripts to set token from login response

## Deployment

### Frontend (Vite)
```bash
npm run build
# Deploy dist/ folder to your static host
```

### Backend (Laravel)
```bash
php artisan config:cache
php artisan route:cache
php artisan optimize
php -S localhost:8000 -t public
```

### Enable CORS
Already configured in `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

## Performance Tips

1. **Pagination**: Always paginate large lists
2. **Filtering**: Use server-side filters to reduce data
3. **Caching**: Cache GET responses on client
4. **Debouncing**: Debounce search inputs before API calls
5. **Lazy Loading**: Load data only when needed

## Common Issues

### CORS Error
- Check `.env`: `APP_URL` and allowed origins
- Verify Laravel CORS middleware is configured

### 401 Unauthorized
- Token expired: Redirect to login
- Invalid token: Clear localStorage and login again

### 403 Forbidden
- Check user role and permissions
- Verify authorization middleware

### 422 Validation Error
- Check required fields in API documentation
- Validate data before sending

## Next Steps

1. **Create remaining pages**: Admin dashboard, Buyer shopping
2. **Implement cart**: Use localStorage or Pinia store
3. **Add real-time**: Consider WebSockets for order updates
4. **Mobile app**: Reuse API endpoints for React Native/Flutter
5. **Authentication**: Add JWT refresh token rotation

## Support

For API issues, check:
1. `API_DOCUMENTATION.md` for endpoint details
2. `resources/js/services/api.js` for client methods
3. Laravel logs: `storage/logs/laravel.log`
4. Browser console for frontend errors

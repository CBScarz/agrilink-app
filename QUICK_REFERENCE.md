# REST API Quick Reference

## Authentication

### Register
```javascript
const response = await api.register('John', 'john@example.com', 'password123', 'farmer');
// Returns: { user, token }
```

### Login
```javascript
const response = await api.login('john@example.com', 'password123');
// Token automatically stored and used for future requests
```

### Logout
```javascript
await api.logout();
// Token cleared from localStorage
```

## Products

### Get All Products
```javascript
const products = await api.getProducts({
  category: 'Vegetables',
  search: 'tomato',
  in_stock: true,
  page: 1,
  per_page: 15
});
// Returns: { data: [], pagination: {} }
```

### Get Single Product
```javascript
const product = await api.getProduct(productId);
```

### Create Product (Farmer Only)
```javascript
const product = await api.createProduct({
  name: 'Organic Tomatoes',
  description: 'Fresh and healthy',
  price: 250.00,
  stock: 100,
  category: 'Vegetables'
});
```

### Update Product (Farmer Only)
```javascript
const updated = await api.updateProduct(productId, {
  price: 300.00,
  stock: 80
});
```

### Delete Product (Farmer Only)
```javascript
await api.deleteProduct(productId);
```

## Farmer Operations

### Get Farmer Dashboard
```javascript
const dashboard = await api.getFarmerDashboard();
// Returns: {
//   status: 'active',
//   stats: { total_products, total_orders, total_earnings, average_rating },
//   recent_orders: [],
//   top_products: []
// }
```

### Get Farmer's Products
```javascript
const response = await api.getFarmerProducts({
  category: 'Vegetables',
  stock_status: 'in_stock', // or 'out_of_stock'
  search: 'tomato',
  sort: 'newest', // or 'price_low', 'price_high', 'name'
  page: 1,
  per_page: 12
});
```

### Get Farmer's Orders
```javascript
const response = await api.getFarmerOrders({
  page: 1,
  per_page: 20
});
```

### Update Order Status
```javascript
const order = await api.updateOrderStatus(orderId, 'processing');
// Status: 'processing', 'shipped', 'delivered', 'cancelled'
```

## Buyer Operations

### Create Order
```javascript
const order = await api.createOrder(
  [
    { product_id: 1, quantity: 2 },
    { product_id: 3, quantity: 1 }
  ],
  '123 Main St, City, Province'
);
// Returns: Order with calculated total
```

### Get My Orders
```javascript
const response = await api.getBuyerOrders({
  page: 1,
  per_page: 20
});
```

### Cancel Order
```javascript
await api.cancelOrder(orderId);
// Only pending or processing orders can be cancelled
```

## Admin Operations

### Farmers

#### List Farmers
```javascript
const response = await api.getAdminFarmers({
  status: 'pending', // or 'active', 'rejected', ''
  search: 'john',
  page: 1,
  per_page: 20
});
```

#### Approve Farmer
```javascript
await api.approveFarmer(farmerId);
// Sets status to 'active'
```

#### Reject Farmer
```javascript
await api.rejectFarmer(farmerId);
// Sets status to 'rejected'
```

#### Delete Farmer
```javascript
await api.deleteFarmer(farmerId);
```

#### Get Farmer Stats
```javascript
const stats = await api.getAdminFarmerStats();
// Returns: { total_farmers, pending, active, rejected }
```

### Products

#### List All Products (Admin)
```javascript
const response = await api.getAdminProducts({
  farmer_id: 1,
  category: 'Vegetables',
  stock_status: 'in_stock',
  search: 'tomato',
  page: 1,
  per_page: 20
});
```

#### Update Stock
```javascript
await api.updateProductStock(productId, 150);
```

#### Delete Product (Admin)
```javascript
await api.deleteAdminProduct(productId);
```

#### Get Product Stats
```javascript
const stats = await api.getAdminProductStats();
// Returns: { total, in_stock, out_of_stock }
```

### Orders

#### List All Orders (Admin)
```javascript
const response = await api.getAdminOrders({
  status: 'pending',
  buyer_id: 1,
  farmer_id: 2,
  search: 'ORD123',
  page: 1,
  per_page: 20
});
```

#### Update Order Status (Admin)
```javascript
await api.updateAdminOrderStatus(orderId, 'shipped');
```

#### Delete Order (Admin)
```javascript
await api.deleteAdminOrder(orderId);
```

#### Get Order Stats
```javascript
const stats = await api.getAdminOrderStats();
// Returns: { total, pending, processing, delivered }
```

## Error Handling

### Try-Catch Pattern
```javascript
try {
  const result = await api.createProduct(data);
  console.log('Success:', result);
} catch (error) {
  console.error('Error:', error.message);
  // Display error to user
}
```

### Common Errors
- **401 Unauthorized** - Token missing or invalid, redirect to login
- **403 Forbidden** - User doesn't have permission
- **422 Validation Error** - Invalid data, check fields
- **404 Not Found** - Resource doesn't exist
- **500 Server Error** - Backend issue

## Profile Operations

### Get Current User
```javascript
const user = await api.getMe();
```

### Update Current User Profile
```javascript
await api.updateProfile({
  name: 'New Name',
  email: 'new@example.com',
  password: 'newpassword'
});
```

### Update Farmer Profile
```javascript
await api.updateFarmerProfile({
  business_name: 'My Farm',
  location: 'Quezon City',
  phone: '09123456789',
  description: 'Fresh vegetables'
});
```

## Vue Component Example

```vue
<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const products = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    const response = await api.getProducts({ category: 'Vegetables' });
    products.value = response.data;
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
});

const addProduct = async () => {
  try {
    await api.createProduct({
      name: 'Tomatoes',
      description: 'Fresh',
      price: 250,
      stock: 100,
      category: 'Vegetables'
    });
    // Reload products
    const response = await api.getFarmerProducts();
    products.value = response.data;
  } catch (err) {
    alert(err.message);
  }
};
</script>
```

## Token Management

Token is automatically managed by the API client:

```javascript
// After login, token is stored
api.setToken(token); // Manual set if needed
localStorage.getItem('auth_token'); // Check token
api.clearToken(); // Clear on logout
```

## Pagination

All list endpoints support pagination:

```javascript
const response = await api.getProducts({
  page: 2,
  per_page: 20
});

// Response includes pagination info
console.log(response.pagination);
// { total, per_page, current_page, last_page }
```

## Filters

Common filters across endpoints:

- `search` - Search by name/email/description
- `category` - Filter by product category
- `status` - Filter by status
- `stock_status` - 'in_stock' or 'out_of_stock'
- `sort` - 'newest', 'price_low', 'price_high', 'name'
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: varies)

## Status Values

### User Roles
- `admin` - Administrator
- `farmer` - Farmer seller
- `buyer` - Customer buyer

### Farmer Status
- `pending` - Awaiting admin approval
- `active` - Approved and can sell
- `rejected` - Rejected application

### Order Status
- `pending` - Order placed
- `processing` - Being prepared
- `shipped` - In transit
- `delivered` - Completed
- `cancelled` - Cancelled by buyer

## API Methods Summary

**Auth**: register, login, logout, getMe, updateProfile
**Products**: getProducts, getProduct, getProductsByCategory, getProductsByFarmer
**Farmer**: getFarmerDashboard, updateFarmerProfile, getFarmerProducts, createProduct, updateProduct, deleteProduct, getFarmerProductStats, getFarmerOrders, updateOrderStatus
**Buyer**: createOrder, getBuyerOrders, getBuyerOrder, cancelOrder
**Admin**: getAdminFarmers, approveFarmer, rejectFarmer, deleteFarmer, getAdminProducts, updateProductStock, deleteAdminProduct, getAdminOrders, updateAdminOrderStatus, deleteAdminOrder

## Response Format

All responses follow this format:

```json
{
  "message": "Success message (optional)",
  "data": [],
  "user": {},
  "token": "abc123",
  "pagination": {
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "last_page": 5
  }
}
```

---

For full documentation, see `API_DOCUMENTATION.md`

# AgriLink REST API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
All protected endpoints require Bearer token in Authorization header:
```
Authorization: Bearer {token}
```

---

## Authentication Endpoints

### Register
- **POST** `/auth/register`
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "farmer" or "buyer"
  }
  ```
- **Response:** User object + auth token

### Login
- **POST** `/auth/login`
- **Body:**
  ```json
  {
    "email": "john@example.com",
    "password": "password123"
  }
  ```
- **Response:** User object + auth token

### Logout
- **POST** `/auth/logout`
- **Auth:** Required

### Get Current User
- **GET** `/auth/me`
- **Auth:** Required
- **Response:** Current user object

### Update Profile
- **PATCH** `/auth/profile`
- **Auth:** Required
- **Body:**
  ```json
  {
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "newpassword123"
  }
  ```

---

## Public Product Endpoints

### List All Products
- **GET** `/products`
- **Query Parameters:**
  - `category` - Filter by category
  - `farmer_id` - Filter by farmer
  - `search` - Search by name/description
  - `in_stock` - true/false for stock filter
  - `page` - Page number (default: 1)
  - `per_page` - Results per page (default: 15)
- **Response:** Paginated products with farmer info

### Get Single Product
- **GET** `/products/{productId}`
- **Response:** Product object with farmer and order items

### Get Products by Category
- **GET** `/products/category/{category}`
- **Query Parameters:** `page`, `per_page`

### Get Products by Farmer
- **GET** `/products/farmer/{farmerId}`
- **Query Parameters:** `page`, `per_page`

---

## Farmer Endpoints

### Dashboard
- **GET** `/farmer/dashboard`
- **Auth:** Required (Farmer)
- **Response:**
  ```json
  {
    "status": "active",
    "profile": { farmer profile object },
    "stats": {
      "total_products": 10,
      "total_orders": 25,
      "total_earnings": 5000,
      "average_rating": 4.5
    },
    "recent_orders": [],
    "top_products": []
  }
  ```

### Update Profile
- **PATCH** `/farmer/profile`
- **Auth:** Required (Farmer)
- **Body:**
  ```json
  {
    "business_name": "My Farm",
    "location": "Quezon City",
    "phone": "09123456789",
    "description": "Fresh vegetables..."
  }
  ```

### List Farmer's Products
- **GET** `/farmer/products`
- **Auth:** Required (Farmer)
- **Query Parameters:**
  - `category` - Filter by category
  - `stock_status` - in_stock/out_of_stock
  - `search` - Search by name/description
  - `sort` - newest/price_low/price_high/name
  - `page` - Page number
  - `per_page` - Results per page (default: 12)
- **Response:** Paginated products + stats

### Create Product
- **POST** `/farmer/products`
- **Auth:** Required (Farmer, must be active status)
- **Body:**
  ```json
  {
    "name": "Organic Tomatoes",
    "description": "Fresh organic tomatoes",
    "price": 250.00,
    "stock": 100,
    "category": "Vegetables",
    "image": "file"
  }
  ```

### Update Product
- **PATCH** `/farmer/products/{productId}`
- **Auth:** Required (Farmer)
- **Body:** Same as create (all fields optional)

### Delete Product
- **DELETE** `/farmer/products/{productId}`
- **Auth:** Required (Farmer)

### Get Product Stats
- **GET** `/farmer/products/stats`
- **Auth:** Required (Farmer)
- **Response:**
  ```json
  {
    "total": 10,
    "in_stock": 8,
    "out_of_stock": 2,
    "total_earnings": 5000
  }
  ```

### List Farmer's Orders
- **GET** `/farmer/orders`
- **Auth:** Required (Farmer)
- **Query Parameters:** `page`, `per_page`

### Get Order Stats
- **GET** `/farmer/orders/stats`
- **Auth:** Required (Farmer)
- **Response:**
  ```json
  {
    "total_orders": 25,
    "pending": 5,
    "processing": 10,
    "delivered": 10
  }
  ```

### Update Order Status
- **PATCH** `/farmer/orders/{orderId}/status`
- **Auth:** Required (Farmer)
- **Body:**
  ```json
  {
    "status": "processing" or "shipped" or "delivered" or "cancelled"
  }
  ```

---

## Buyer Endpoints

### Create Order
- **POST** `/buyer/orders`
- **Auth:** Required (Buyer)
- **Body:**
  ```json
  {
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      },
      {
        "product_id": 3,
        "quantity": 1
      }
    ],
    "shipping_address": "123 Main St, City, Province"
  }
  ```
- **Response:** Order object with items and total amount

### List Buyer's Orders
- **GET** `/buyer/orders`
- **Auth:** Required (Buyer)
- **Query Parameters:** `page`, `per_page`

### Get Order Details
- **GET** `/buyer/orders/{orderId}`
- **Auth:** Required (Buyer)

### Cancel Order
- **POST** `/buyer/orders/{orderId}/cancel`
- **Auth:** Required (Buyer)
- **Note:** Can only cancel pending or processing orders

---

## Admin Endpoints

### Manage Farmers

#### List All Farmers
- **GET** `/admin/farmers`
- **Auth:** Required (Admin)
- **Query Parameters:**
  - `status` - pending/active/rejected
  - `search` - Search by name/email/farm name
  - `page`, `per_page`

#### Get Farmer Stats
- **GET** `/admin/farmers/stats`
- **Auth:** Required (Admin)
- **Response:**
  ```json
  {
    "total_farmers": 50,
    "pending": 10,
    "active": 35,
    "rejected": 5
  }
  ```

#### Approve Farmer
- **POST** `/admin/farmers/{farmerId}/approve`
- **Auth:** Required (Admin)

#### Reject Farmer
- **POST** `/admin/farmers/{farmerId}/reject`
- **Auth:** Required (Admin)

#### Delete Farmer
- **DELETE** `/admin/farmers/{farmerId}`
- **Auth:** Required (Admin)

#### Download Farmer Permit
- **GET** `/admin/farmers/{farmerId}/permit`
- **Auth:** Required (Admin)

### Manage Products

#### List All Products
- **GET** `/admin/products`
- **Auth:** Required (Admin)
- **Query Parameters:**
  - `farmer_id` - Filter by farmer
  - `category` - Filter by category
  - `stock_status` - in_stock/out_of_stock
  - `search` - Search by name/description
  - `page`, `per_page`

#### Get Product Stats
- **GET** `/admin/products/stats`
- **Auth:** Required (Admin)
- **Response:**
  ```json
  {
    "total": 150,
    "in_stock": 120,
    "out_of_stock": 30
  }
  ```

#### Update Stock
- **PATCH** `/admin/products/{productId}/stock`
- **Auth:** Required (Admin)
- **Body:**
  ```json
  {
    "stock": 100
  }
  ```

#### Delete Product
- **DELETE** `/admin/products/{productId}`
- **Auth:** Required (Admin)

### Manage Orders

#### List All Orders
- **GET** `/admin/orders`
- **Auth:** Required (Admin)
- **Query Parameters:**
  - `status` - pending/processing/shipped/delivered/cancelled
  - `buyer_id` - Filter by buyer
  - `farmer_id` - Filter by farmer
  - `search` - Search by order ID
  - `page`, `per_page`

#### Get Order Stats
- **GET** `/admin/orders/stats`
- **Auth:** Required (Admin)
- **Response:**
  ```json
  {
    "total": 100,
    "pending": 10,
    "processing": 20,
    "delivered": 70
  }
  ```

#### Update Order Status
- **PATCH** `/admin/orders/{orderId}/status`
- **Auth:** Required (Admin)
- **Body:**
  ```json
  {
    "status": "processing" or "shipped" or "delivered" or "cancelled"
  }
  ```

#### Delete Order
- **DELETE** `/admin/orders/{orderId}`
- **Auth:** Required (Admin)

---

## Error Responses

All errors return appropriate HTTP status codes and a message:

```json
{
  "message": "Error description"
}
```

### Common Status Codes
- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Rate Limiting
- No rate limiting currently implemented
- Recommended: Add rate limiting in production

## Environment Variables
Add to `.env`:
```
API_URL=http://localhost:8000/api
```

And in `.env.development.local`:
```
VITE_API_URL=http://localhost:8000/api
```

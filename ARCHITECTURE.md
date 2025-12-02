# AgriLink REST API Architecture

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Web App    │  │  Mobile App  │  │   Desktop    │          │
│  │  (Vue 3)     │  │  (React Native)│ │   App        │          │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘          │
│         │                  │                 │                 │
│         └──────────────────┼─────────────────┘                 │
│                            │                                   │
│                    All consume same API                         │
└────────────────────────────┼───────────────────────────────────┘
                             │
                      HTTP/HTTPS (REST)
                             │
┌────────────────────────────┼───────────────────────────────────┐
│                   API GATEWAY LAYER                           │
├────────────────────────────┼───────────────────────────────────┤
│                            │                                   │
│              ┌─────────────▼─────────────┐                    │
│              │   CORS Middleware         │                    │
│              │   (Allow Cross-Origin)    │                    │
│              └─────────────┬─────────────┘                    │
│                            │                                   │
│              ┌─────────────▼─────────────┐                    │
│              │  Authentication Layer     │                    │
│              │  (Sanctum Tokens)         │                    │
│              └─────────────┬─────────────┘                    │
│                            │                                   │
└────────────────────────────┼───────────────────────────────────┘
                             │
┌────────────────────────────┼───────────────────────────────────┐
│                    ROUTING LAYER (routes/api.php)             │
├────────────────────────────┼───────────────────────────────────┤
│                            │                                   │
│   ┌────────────────────────┼────────────────────────┐         │
│   │                        │                        │         │
│   ▼                        ▼                        ▼         │
│ PUBLIC ROUTES      PROTECTED ROUTES          ADMIN ROUTES    │
│ (No Auth)          (User Auth + Role)        (Admin Only)     │
│                                                               │
│ • /auth/login      • /farmer/products        • /admin/farmers │
│ • /auth/register   • /farmer/orders          • /admin/products│
│ • /products        • /buyer/orders           • /admin/orders  │
│ • /products/{id}   • /auth/me                                 │
│                                                               │
└────────────────────────────┼───────────────────────────────────┘
                             │
┌────────────────────────────┼───────────────────────────────────┐
│                  CONTROLLER LAYER                              │
├────────────────────────────┼───────────────────────────────────┤
│                            │                                   │
│   ┌────────────────────────┼────────────────────────┐         │
│   │                        │                        │         │
│   ▼                        ▼                        ▼         │
│ AuthController     Role-Based                   AdminControllers
│                    Controllers                                │
│ • register         • Farmer\                    • FarmerController
│ • login              ProductController          • ProductController
│ • logout           • Farmer\                    • OrderController
│ • getMe              OrderController                          │
│ • updateProfile    • Buyer\                                  │
│                      OrderController                          │
│                                                               │
└────────────────────────────┼───────────────────────────────────┘
                             │
┌────────────────────────────┼───────────────────────────────────┐
│                      MODEL LAYER                               │
├────────────────────────────┼───────────────────────────────────┤
│                            │                                   │
│   ┌────────────────────────┼────────────────────────┐         │
│   │                        │                        │         │
│   ▼                        ▼                        ▼         │
│ User Model        Product Model            Order Model       │
│ • id              • id                      • id              │
│ • name            • name                    • buyer_id        │
│ • email           • description             • status          │
│ • role            • price                   • total_amount    │
│ • status          • stock                   • items[]         │
│ • password        • farmer_id               • created_at      │
│                   • category                                 │
│                                                               │
└────────────────────────────┼───────────────────────────────────┘
                             │
┌────────────────────────────┼───────────────────────────────────┐
│                   DATABASE LAYER                               │
├────────────────────────────┼───────────────────────────────────┤
│                            │                                   │
│   ┌────────────────────────▼────────────────────────┐         │
│   │         SQLite Database (SQLite)                │         │
│   │                                                 │         │
│   │  Tables:                                        │         │
│   │  • user (id, name, email, role, status)        │         │
│   │  • farmer_profiles (farmer_id, business_name)  │         │
│   │  • products (id, name, price, stock, farmer_id)│         │
│   │  • orders (id, buyer_id, status, total_amount) │         │
│   │  • order_items (id, order_id, product_id)      │         │
│   └─────────────────────────────────────────────────┘         │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

## Data Flow Example: Creating a Product

```
FRONTEND (Vue Component)
    │
    │ const product = await api.createProduct({
    │   name: 'Tomatoes',
    │   price: 250,
    │   stock: 100
    │ })
    │
    ▼
HTTP POST Request
    │
    POST /api/farmer/products
    Body: { name, price, stock, ... }
    Headers: { Authorization: Bearer {token} }
    │
    ▼
Laravel Route (routes/api.php)
    │
    Route::post('/farmer/products', [FarmerProductController::class, 'store'])
    │
    ▼
Middleware Chain
    │
    ├─ auth:sanctum (verify token)
    ├─ farmer (verify role = 'farmer')
    └─ Verify farmer status = 'active'
    │
    ▼
Controller Method (ProductController@store)
    │
    ├─ Validate request
    ├─ Check farmer status (must be 'active')
    ├─ Create product with farmer_id
    └─ Return JSON response
    │
    ▼
Database
    │
    INSERT INTO products (name, price, stock, farmer_id)
    VALUES ('Tomatoes', 250, 100, {auth()->id()})
    │
    ▼
JSON Response
    │
    {
      "message": "Product created successfully",
      "product": {
        "id": 1,
        "name": "Tomatoes",
        "price": 250,
        "stock": 100,
        "farmer_id": 2,
        "created_at": "2024-12-02..."
      }
    }
    │
    ▼
FRONTEND (Vue Component)
    │
    product.value = response.product
    // Display success message
    // Redirect or update list
```

## Authentication Flow

```
┌──────────────────────────────────────────────────────────┐
│                   USER LOGIN                             │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  Step 1: Frontend sends credentials                    │
│  ┌─────────────────────────────────────────────────┐  │
│  │ POST /api/auth/login                            │  │
│  │ { email: "user@example.com", password: "..." }  │  │
│  └────────────────────┬────────────────────────────┘  │
│                       │                                │
│  Step 2: Backend validates credentials                │
│  ┌────────────────────▼────────────────────────────┐  │
│  │ - Check user exists                             │  │
│  │ - Verify password hash                          │  │
│  │ - If valid: generate Sanctum token              │  │
│  └────────────────────┬────────────────────────────┘  │
│                       │                                │
│  Step 3: Return token and user info                   │
│  ┌────────────────────▼────────────────────────────┐  │
│  │ {                                               │  │
│  │   user: { id, name, email, role, status },     │  │
│  │   token: "abc123xyz789..."                      │  │
│  │ }                                               │  │
│  └────────────────────┬────────────────────────────┘  │
│                       │                                │
│  Step 4: Frontend stores token                        │
│  ┌────────────────────▼────────────────────────────┐  │
│  │ localStorage.setItem('auth_token', token)       │  │
│  └────────────────────┬────────────────────────────┘  │
│                       │                                │
│  Step 5: Include token in subsequent requests        │
│  ┌────────────────────▼────────────────────────────┐  │
│  │ GET /api/farmer/products                        │  │
│  │ Headers: {                                      │  │
│  │   Authorization: Bearer abc123xyz789...         │  │
│  │ }                                               │  │
│  └────────────────────┬────────────────────────────┘  │
│                       │                                │
│  Step 6: Backend validates token                      │
│  ┌────────────────────▼────────────────────────────┐  │
│  │ - Extract token from header                     │  │
│  │ - Look up user from token                       │  │
│  │ - Verify user has 'farmer' role                 │  │
│  │ - Execute request                               │  │
│  └──────────────────────────────────────────────────┘  │
│                                                        │
└──────────────────────────────────────────────────────────┘
```

## Role-Based Access Control

```
┌────────────────────────────────────────────────────────────┐
│                      USER ROLES                            │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  ADMIN                    FARMER                  BUYER     │
│  ├─ Approve farmers       ├─ Create products     ├─ View    │
│  ├─ Reject farmers        ├─ Edit products       │  products│
│  ├─ Manage products       ├─ Delete products     ├─ Add to  │
│  ├─ View all orders       ├─ View orders         │  cart    │
│  ├─ Manage orders         ├─ Update order status ├─ Create  │
│  ├─ View all users        └─ View dashboard      │  orders  │
│  └─ Generate reports                             ├─ Track   │
│                                                   │  orders  │
│                                                   └─ Cancel  │
│                                                      orders  │
│                                                            │
│  STATUS SYSTEM (Farmers only):                           │
│  ├─ pending  → Waiting admin approval                    │
│  ├─ active   → Approved, can sell                        │
│  └─ rejected → Application rejected                      │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

## API Response Format

```json
{
  "success": true,
  "message": "Operation description",
  "data": {
    "id": 1,
    "name": "Resource name",
    "description": "Resource details",
    "timestamps": "2024-12-02..."
  },
  "pagination": {
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "last_page": 5
  },
  "meta": {
    "timestamp": "2024-12-02T...",
    "request_id": "req_..."
  }
}
```

## Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  },
  "status_code": 422
}
```

## Frontend Integration Pattern

```
┌──────────────────────────────────────────┐
│         Vue Component                    │
├──────────────────────────────────────────┤
│                                          │
│  <template>                              │
│    <div>                                 │
│      <div v-if="loading">Loading...</div>
│      <div v-if="error">{{ error }}</div> │
│      <div v-else>                        │
│        <!-- Display data -->             │
│      </div>                              │
│    </div>                                │
│  </template>                             │
│                                          │
│  <script setup>                          │
│    import { ref, onMounted } from 'vue' │
│    import api from '@/services/api'     │
│                                          │
│    const data = ref(null)                │
│    const loading = ref(true)             │
│    const error = ref(null)               │
│                                          │
│    onMounted(async () => {               │
│      try {                               │
│        data.value =                      │
│          await api.getEndpoint()         │
│      } catch (err) {                     │
│        error.value = err.message         │
│      } finally {                         │
│        loading.value = false             │
│      }                                   │
│    })                                    │
│                                          │
│    const handleAction = async () => {    │
│      try {                               │
│        await api.postEndpoint(payload)   │
│        // Refresh data                   │
│        data.value =                      │
│          await api.getEndpoint()         │
│      } catch (err) {                     │
│        alert(err.message)                │
│      }                                   │
│    }                                     │
│  </script>                               │
│                                          │
└──────────────────────────────────────────┘
```

## Deployment Architecture (Recommended)

```
┌─────────────────────────────────────────────────────────────┐
│                        PRODUCTION                           │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌────────────────────────────────────────────────────┐   │
│  │         CDN / Web Server                           │   │
│  │  (Serve Vue frontend - build/dist)                 │   │
│  │  - index.html                                      │   │
│  │  - app.js (bundled)                                │   │
│  │  - app.css (bundled)                               │   │
│  └─────────────────┬──────────────────────────────────┘   │
│                    │                                       │
│                    │ API calls to                          │
│                    │                                       │
│  ┌─────────────────▼──────────────────────────────────┐   │
│  │  API Server (Laravel)                              │   │
│  │  - Port 443 (HTTPS)                                │   │
│  │  - Environment: production                         │   │
│  │  - Cache enabled                                   │   │
│  │  - Queue workers running                           │   │
│  └─────────────────┬──────────────────────────────────┘   │
│                    │                                       │
│  ┌─────────────────▼──────────────────────────────────┐   │
│  │  Database (PostgreSQL preferred for production)   │   │
│  │  - Backup enabled                                  │   │
│  │  - Replication enabled                             │   │
│  │  - Monitoring enabled                              │   │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  Additional Services:                                      │
│  - Redis (cache, queue)                                    │
│  - S3 (file storage)                                       │
│  - SendGrid (emails)                                       │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

**Notes:**
- All APIs follow REST principles
- Token-based authentication (Sanctum)
- Role-based access control
- Pagination for list endpoints
- Consistent error handling
- CORS enabled for cross-origin requests
- Production-ready error logging

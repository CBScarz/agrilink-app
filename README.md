# AgriLink - Agricultural Marketplace Platform

A modern web application connecting farmers directly with buyers, enabling seamless agricultural commerce.

## 🌾 Overview

AgriLink is a full-stack agricultural marketplace built with Laravel 12 and Vue 3. It empowers farmers to list their products with rich details and images, while providing buyers with access to fresh, local produce.

## 🎯 Key Features

### For Farmers
- **Product Management**: Create, edit, and manage product listings
- **Rich Product Details**: Add harvest dates, certifications, origin, and product features
- **Image Upload**: Upload high-quality product images with drag-and-drop support
- **Dashboard**: View sales, revenue, and product performance
- **Order Management**: Track and manage customer orders

### For Buyers
- **Product Discovery**: Browse products by category, availability, and price
- **Advanced Search**: Find products with powerful filtering options
- **Shopping Cart**: Add products to cart with cart deduplication by product + farmer
- **Buy Now**: Direct checkout for single product purchases
- **Saved Addresses**: Save and reuse delivery addresses with edit/delete options
- **Multiple Payment Methods**: Credit/Debit Card, GCash, Cash on Delivery
- **Order Management**: Track orders and view order history
- **Farmer Profiles**: View farmer information and ratings

### For Admin
- **Farmer Verification**: Approve or reject farmer applications
- **Product Management**: Monitor all platform products
- **Order Oversight**: Track all orders and their status
- **Performance Analytics**: View platform statistics and metrics

## 🏗️ Technology Stack

### Backend
- **Framework**: Laravel 12.40.2
- **Database**: SQLite
- **Authentication**: Laravel Sanctum + Session Auth
- **API**: RESTful API with role-based authorization

### Frontend
- **Framework**: Vue 3
- **Server-Side Rendering**: Inertia.js
- **Styling**: Tailwind CSS
- **Build Tool**: Vite

### Infrastructure
- **File Storage**: Local disk with public symlink
- **Image Handling**: Drag-and-drop upload with preview

## 🗂️ Project Structure

```
agrilink-app/
├── app/
│   ├── Http/Controllers/         # Request handlers
│   ├── Models/                   # Database models
│   ├── Policies/                 # Authorization policies
│   └── Middleware/               # Custom middleware
├── database/
│   ├── migrations/               # Schema changes
│   ├── factories/                # Test data factories
│   └── seeders/                  # Database seeders
├── resources/
│   ├── js/
│   │   ├── Pages/                # Vue page components
│   │   ├── Layouts/              # Layout components
│   │   ├── Components/           # Reusable components
│   │   └── services/             # API service layer
│   └── css/                      # Tailwind CSS
├── routes/
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   └── auth.php                  # Auth routes
└── storage/                      # File uploads
```

## 🚀 Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/CBScarz/agrilink-app.git
   cd agrilink-app
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install --legacy-peer-deps
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run migrations**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Create storage link**
   ```bash
   php artisan storage:link
   ```

6. **Start development servers**
   
   Terminal 1 - Laravel Server:
   ```bash
   php artisan serve
   ```
   
   Terminal 2 - Vite Dev Server:
   ```bash
   npm run dev
   ```

7. **Access the application**
   - URL: `http://localhost:8000`

## 📊 Database Schema

### Core Tables
- **users**: All platform users (farmers, buyers, admins)
- **farmer_profiles**: Additional farmer information and verification
- **products**: Product listings with details and images
- **orders**: Customer orders
- **order_items**: Individual items in orders

### Authentication Tables
- **personal_access_tokens**: API tokens for Sanctum
- **sessions**: Session data
- **cache**: Cache storage

## 🔐 User Roles

### Admin
- Full platform access
- Manage farmers and their verification
- Monitor products and orders
- View platform analytics

### Farmer
- Create and manage products
- View product performance
- Manage customer orders
- Access dashboard with sales metrics

### Buyer
- Browse and search products
- Add products to cart
- Place orders
- Manage shopping cart

## 📝 Product Creation Flow

1. **Farmer** navigates to `/farmer/products/create`
2. **Form** collects all product details:
   - Basic info (name, description, category)
   - Pricing & stock details
   - Optional fields (origin, certifications, harvest dates)
   - Product features (checkboxes)
   - Product image (drag-and-drop upload)
3. **Validation** on both frontend and backend
4. **Image** uploaded to `storage/app/public/products/`
5. **Product** stored in database with all metadata
6. **Display** on marketplace with image

## 🖼️ Image Storage & Display

- Images stored in: `storage/app/public/products/`
- Public access via: `/storage/products/filename`
- Supported formats: PNG, JPG, GIF
- Max file size: 2MB
- Symlink created via `php artisan storage:link`

## 🧪 Testing

Run migrations and seeders to populate test data:
```bash
php artisan migrate:fresh --seed
```

Test accounts created:
- Admin user
- Sample farmers
- Sample buyers

## 📚 API Documentation

API endpoints are documented in `API_DOCUMENTATION.md`

Key endpoints:
- `POST /api/farmer/products` - Create product
- `GET /api/products` - List products
- `GET /api/farmer/products` - Farmer's products
- `GET /api/orders` - User orders

## 🔧 Configuration

### Environment Variables
```env
APP_NAME=AgriLink
APP_ENV=local
APP_DEBUG=true
DB_DATABASE=sqlite
VITE_API_URL=http://localhost:8000/api
```

### Middleware
- `auth` - Requires authentication
- `verified` - Requires email verification
- `admin` - Admin role only
- `farmer` - Farmer role only
- `buyer` - Buyer role only

## 🐛 Known Issues & Future Improvements

### Completed ✅
- ✅ Product creation with image upload
- ✅ Product display with filtering and search
- ✅ Role-based access control
- ✅ Shopping cart functionality with deduplication
- ✅ Buy Now feature for direct checkout
- ✅ Saved addresses for delivery information
- ✅ Multiple payment method options (Card, GCash, COD)
- ✅ Multi-farmer order support
- ✅ Stock validation and inventory management
- ✅ Form validation with error messages

### Planned 🚀
- Product ratings and reviews
- Farmer ratings and profiles
- Email notifications for orders
- Real-time order status updates
- Product recommendations
- Wishlist feature
- Order cancellation
- Refund management
- Advanced analytics for farmers

## 📄 License

This project is open source and available under the MIT license.

## 👥 Contributing

Contributions are welcome! Please feel free to submit issues and pull requests.

## 📞 Support

For support, please open an issue on the GitHub repository.

---

**Repository**: https://github.com/CBScarz/agrilink-app

**Last Updated**: December 4, 2025

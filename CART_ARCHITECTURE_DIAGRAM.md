# Shopping Cart System Architecture

## Component Hierarchy & Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                       useCart Composable                        │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │ Shared State (Vue 3 Composition API)                    │ │
│  ├──────────────────────────────────────────────────────────┤ │
│  │ • cartItems: ref([])              → Array of cart items │ │
│  │ • isLoaded: ref(false)            → Init flag           │ │
│  │ • localStorage['agrilink_cart']   → Persistent storage  │ │
│  └──────────────────────────────────────────────────────────┘ │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │ Methods (Public API)                                     │ │
│  ├──────────────────────────────────────────────────────────┤ │
│  │ addToCart(product, qty)         → 'added' | 'updated'   │ │
│  │ removeFromCart(id)              → true | false          │ │
│  │ updateQuantity(id, qty)         → true | false          │ │
│  │ clearCart()                     → void                  │ │
│  │ getTotalItems (computed)        → number                │ │
│  │ getSubtotal (computed)          → number                │ │
│  └──────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
  ▲                           ▲                      ▲
  │                           │                      │
  └─────────────┬─────────────┴──────────┬───────────┘
                │                        │
                │                        │
   ┌────────────▼──────────┐  ┌──────────▼──────────┐
   │   ProductDetail.vue   │  │   Cart.vue          │
   ├───────────────────────┤  ├─────────────────────┤
   │ Imports: useCart      │  │ Imports: useCart    │
   │                       │  │                     │
   │ Uses:                 │  │ Uses:               │
   │ • addToCart()         │  │ • cartItems         │
   │ • quantity ref        │  │ • removeFromCart()  │
   │ • notification ref    │  │ • updateQuantity()  │
   │                       │  │ • computed totals   │
   │ Displays:             │  │                     │
   │ • Product details     │  │ Displays:           │
   │ • Qty selector        │  │ • All cart items    │
   │ • Add to Cart button  │  │ • Qty adjusters     │
   │ • Success notify      │  │ • Remove buttons    │
   │ • Buy Now button      │  │ • Order summary     │
   │                       │  │ • Checkout button   │
   └───────────────────────┘  └─────────────────────┘
                │                       │
                │                       │
                └───────────┬───────────┘
                            │
         ┌──────────────────▼──────────────────┐
         │     AppLayout.vue (Header)          │
         ├─────────────────────────────────────┤
         │ Imports: useCart                    │
         │                                     │
         │ Uses:                               │
         │ • getTotalItems (computed)          │
         │ • cartCount (computed)              │
         │                                     │
         │ Displays:                           │
         │ • Shopping cart icon                │
         │ • Item count badge (1-9999)         │
         │ • Link to /cart                     │
         └─────────────────────────────────────┘
```

## Data Flow Diagram

```
ADDING TO CART:
───────────────────────────────────────────────────────────

ProductDetail.vue                useCart Composable        Browser
(User Action)                    (Business Logic)          (Storage)
      │                                 │                        │
      │ Click "Add to Cart"             │                        │
      ├────────►addToCart(product, qty) │                        │
      │                                 │                        │
      │                         ┌─────────────────────┐          │
      │                         │ Check if exists     │          │
      │                         └─────────────────────┘          │
      │                                 │                        │
      │                         ┌─────────────────────┐          │
      │                         │ Validate stock      │          │
      │                         └─────────────────────┘          │
      │                                 │                        │
      │                         ┌─────────────────────┐          │
      │                         │ Update/Add item     │          │
      │                         │ to cartItems ref    │          │
      │                         └─────────────────────┘          │
      │                                 │                        │
      │                                 │      persistCart()     │
      │                                 ├───────────────────────►│
      │                                 │                        │
      │                                 │       Save to          │
      │                                 │       localStorage    │
      │                                 │       'agrilink_cart'  │
      │                                 │                        │
      │ ◄─────────return status─────────┤                        │
      │                                 │                        │
      ├─ Show notification              │                        │
      │                                 │                        │
      │ Auto-dismiss (3 sec)            │                        │
      │                                 │                        │
      └─ Reset quantity to 1            │                        │
```

## Cart Item Structure

```
localStorage['agrilink_cart'] = [
  {
    "id": "12-1702345678901",
    ↑
    └─ Unique ID: productId-timestamp
    
    "product": {
      "id": 12,
      "name": "Organic Tomatoes",
      "price": 45.50,
      "unit": "kg",
      "category": "Vegetables",
      "stock": 100,
      "image_url": "path/to/image.jpg",
      "average_rating": 4.5,
      "rating_count": 245,
      "farmer": {
        "id": 5,
        "name": "Juan Dela Cruz",
        "farmerProfile": {
          "location": "Tagaytay, Cavite"
        }
      },
      ...
    },
    
    "quantity": 2
    ↑
    └─ User selected amount
  },
  ...
]
```

## State Synchronization Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    COMPONENT ISOLATION                          │
│                                                                 │
│  ProductDetail.vue          Cart.vue           AppLayout.vue   │
│  (Different instances)      (Different)        (Different)      │
│                                                                 │
│  └─────────────────────┬────────────────────┬──────────────────┘
│                        │                    │
│                        ▼                    ▼
│     ┌──────────────────────────────────────────────┐
│     │  Shared useCart Composable                   │
│     │  (Single instance across all components)     │
│     │                                              │
│     │  • cartItems (reactive ref)                  │
│     │  • All operations update same state          │
│     └──────────────────────────────────────────────┘
│                        ▲                    ▲
│                        │                    │
│  ┌─────────────────────┴────────────────────┴──────────────────┐
│  │                                                              │
│  │  ALL COMPONENTS SEE SAME DATA                              │
│  │  Updates in any component instantly visible in others       │
│  │                                                              │
│  └──────────────────────────────────────────────────────────────┘
│
│  PERSISTENCE LAYER:
│  ┌──────────────────────────────────────────────────────────────┐
│  │ Watcher watches cartItems changes                            │
│  │ Auto-saves to localStorage whenever items change            │
│  │ On page refresh: Auto-loads from localStorage               │
│  └──────────────────────────────────────────────────────────────┘
```

## Reactive Updates Timeline

```
TIME    EVENT                              STATE
────    ─────────────────────────────────  ──────────────────────
 0ms    User clicks "Add to Cart"          cartItems: []
        
 1ms    addToCart() executes               cartItems: []
        
 2ms    Item added to cartItems ref        cartItems: [{ product, qty }]
                                           (Vue detects change)
        
 3ms    Watcher triggered → persistCart()  localStorage updated
        
 4ms    AppLayout cartCount recomputes     cartCount = 1
        
 5ms    Template updates → badge shows "1" ✓ Visual update
        
100ms   Success notification appears       "Added 2 kg..."
        
3000ms  Notification auto-dismisses        Clean UI
        
User browsing...
        
5000ms  User navigates to /products        cartItems preserved
        
6000ms  navigates to /cart                 Page reloads
        
7ms     Cart.vue mounts                    useCart initializes
        
8ms     initializeCart() runs              localStorage.getItem()
        
9ms     cartItems loaded from storage      Data restored
        
10ms    Component renders with items       ✓ Cart populated
        
11ms    AppLayout updates badge            ✓ Count reflects cart
```

## Quantity Validation Flow

```
User input → Validation → Storage → Display
(Quantity)    (Stock check) (Save)   (UI)

    │
    ▼
  ┌─────────────────┐
  │ Input: qty = 5  │
  └─────────────────┘
    │
    ▼
  ┌──────────────────────────────────┐
  │ Check: qty <= product.stock?     │
  │ (e.g., 5 <= 100)                 │
  └──────────────────────────────────┘
    │
    ├─ YES ─► Math.min(5, 100) = 5
    │         │
    │         ▼
    │     ┌──────────────────┐
    │     │ Update cartItems │ ← Watch triggers
    │     └──────────────────┘
    │         │
    │         ▼
    │     ┌──────────────────┐
    │     │ Save to storage  │
    │     └──────────────────┘
    │         │
    │         ▼
    │     ┌──────────────────┐
    │     │ Recompute totals │
    │     │ (subtotal, total)│
    │     └──────────────────┘
    │         │
    │         ▼
    │     ┌──────────────────┐
    │     │ Update UI        │
    │     │ ✓ qty shows 5    │
    │     │ ✓ price updates  │
    │     └──────────────────┘
    │
    └─ NO ──► Keep current value
              (Silently fail or
               show error)
```

## Browser Storage Visualization

```
┌─────────────────────────────────────────┐
│     Browser LocalStorage (5-10MB)       │
├─────────────────────────────────────────┤
│                                         │
│  Key: 'agrilink_cart'                  │
│  ┌─────────────────────────────────┐  │
│  │ [                               │  │
│  │   {                             │  │
│  │     "id": "12-1702345678901",  │  │
│  │     "product": { ... },         │  │
│  │     "quantity": 2               │  │
│  │   },                            │  │
│  │   {                             │  │
│  │     "id": "15-1702345678902",  │  │
│  │     "product": { ... },         │  │
│  │     "quantity": 1               │  │
│  │   }                             │  │
│  │ ]                               │  │
│  └─────────────────────────────────┘  │
│  Size: ~1.2 KB (3 items)                │
│                                         │
│  Other keys...                          │
│  (Reserved by browser)                  │
│                                         │
└─────────────────────────────────────────┘

┌─ Access via Browser DevTools ─┐
│ F12 → Application              │
│     → Local Storage             │
│     → http://localhost:8000     │
│     → agrilink_cart (value)    │
└────────────────────────────────┘
```

## Error Handling Flow

```
Operation
  │
  ▼
Try Block
  │
  ├─ Success ──► Execute operation
  │             Update cartItems
  │             Return success
  │
  └─ Error ────► Catch block
                 Log error
                 Return false/null
                 Don't crash app
                 Graceful fallback
```

---

This architecture ensures:
✅ **Consistency**: Single source of truth  
✅ **Reactivity**: All components stay in sync  
✅ **Persistence**: Data survives page reloads  
✅ **Validation**: Stock limits respected  
✅ **Reliability**: Error handling built-in  
✅ **Performance**: Optimized localStorage access  

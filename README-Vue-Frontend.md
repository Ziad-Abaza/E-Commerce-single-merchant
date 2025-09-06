# Vue.js SPA Frontend for Laravel E-Commerce

This is a comprehensive Vue.js Single Page Application (SPA) frontend built for the Laravel e-commerce backend.

## 🚀 Features

### 🛍️ E-commerce Features
- **Product Browsing**: Browse products with advanced filtering and search
- **Shopping Cart**: Add/remove items with local storage support for guests
- **Wishlist**: Save favorite products for later
- **Product Categories**: Organized product navigation
- **Product Details**: Detailed product views with image galleries
- **Search**: Advanced search with suggestions and recent searches

### 👤 User Features
- **Authentication**: Login/register with social login support
- **Profile Management**: Update user profile and preferences
- **Order History**: Track and view past orders
- **Wishlist Management**: Organize saved products

### 🎨 UI/UX Features
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Modern UI**: Clean, modern interface with smooth animations
- **Loading States**: Proper loading indicators and skeleton screens
- **Toast Notifications**: User feedback for all actions
- **Accessibility**: WCAG compliant components

## 🛠️ Technology Stack

- **Vue 3** - Progressive JavaScript framework
- **Pinia** - State management
- **Vue Router** - Client-side routing
- **Tailwind CSS** - Utility-first CSS framework
- **Axios** - HTTP client for API communication
- **Vite** - Fast build tool and dev server
- **Laravel Vite Plugin** - Integration with Laravel

## 📦 Dependencies

### Core Dependencies
- `vue` - Vue.js framework
- `vue-router` - Vue routing
- `pinia` - State management
- `axios` - HTTP client
- `@vueuse/core` - Vue composition utilities

### UI Dependencies
- `@headlessui/vue` - Unstyled UI components
- `@heroicons/vue` - Icon library
- `vue-toastification` - Toast notifications
- `vue-loading-overlay` - Loading states
- `vue-star-rating` - Star rating component

### Chart Dependencies
- `vue-chart-3` - Vue 3 chart wrapper
- `chart.js` - Chart library

### Utility Dependencies
- `date-fns` - Date manipulation
- `lodash-es` - Utility functions
- `js-cookie` - Cookie management
- `nprogress` - Progress bar

## 🚀 Getting Started

### Prerequisites
- Node.js (v16 or higher)
- npm or yarn
- Laravel backend running

### Installation

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Start development server:**
   ```bash
   npm run dev
   ```

3. **Build for production:**
   ```bash
   npm run build
   ```

4. **Preview production build:**
   ```bash
   npm run preview
   ```

## 📁 Project Structure

```
resources/
├── js/
│   ├── app.js                 # Main application entry point
│   ├── bootstrap.js           # API configuration
│   ├── style.css              # Global styles
│   ├── App.vue                # Root component
│   ├── stores/                # Pinia stores
│   │   ├── auth.js           # Authentication store
│   │   ├── products.js       # Products store
│   │   └── cart.js           # Cart store
│   ├── layouts/               # Layout components
│   │   ├── AppLayout.vue     # Main layout
│   │   └── AuthLayout.vue    # Auth layout
│   ├── components/            # Reusable components
│   │   ├── common/           # Common components
│   │   └── layout/           # Layout components
│   └── pages/                # Page components
│       ├── Home.vue          # Homepage
│       ├── Products.vue      # Products listing
│       ├── ProductDetail.vue # Product details
│       ├── Cart.vue          # Shopping cart
│       └── auth/             # Authentication pages
└── css/
    └── app.css               # Tailwind CSS imports
```

## 🔧 Configuration

### Environment Variables
Create a `.env` file in your Laravel root directory:

```env
VITE_API_URL=/api
```

### API Configuration
The frontend is configured to communicate with the Laravel backend API. Update the API base URL in `resources/js/bootstrap.js` if needed.

## 🎨 Styling

The application uses Tailwind CSS with custom components and utilities. Key styling features:

- **Custom Color Palette**: Primary and secondary color schemes
- **Responsive Design**: Mobile-first approach
- **Component Classes**: Reusable utility classes
- **Animations**: Smooth transitions and hover effects
- **Dark Mode Ready**: Prepared for dark mode implementation

## 🔄 State Management

The application uses Pinia for state management with three main stores:

### Auth Store
- User authentication state
- Login/logout functionality
- Profile management

### Products Store
- Product data management
- Search and filtering
- Category management

### Cart Store
- Shopping cart state
- Local storage for guests
- Cart operations (add, remove, update)

## 🚦 Routing

The application uses Vue Router with the following route structure:

- `/` - Homepage
- `/products` - Products listing
- `/products/:id` - Product details
- `/category/:id` - Category products
- `/search` - Search results
- `/cart` - Shopping cart
- `/checkout` - Checkout process
- `/auth/login` - Login page
- `/auth/register` - Registration page
- `/profile` - User profile
- `/orders` - Order history
- `/wishlist` - User wishlist

## 🔒 Authentication

The frontend supports both guest and authenticated users:

- **Guest Users**: Can browse products and use local cart storage
- **Authenticated Users**: Full access to all features including orders and wishlist
- **Social Login**: Ready for Google and Facebook integration

## 📱 Responsive Design

The application is fully responsive with breakpoints:

- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

## 🎯 Performance

- **Code Splitting**: Automatic code splitting with Vite
- **Lazy Loading**: Images and components loaded on demand
- **Caching**: API response caching
- **Optimized Builds**: Production builds are optimized for performance

## 🧪 Testing

The application is ready for testing with:

- **Unit Tests**: Component testing setup
- **E2E Tests**: End-to-end testing ready
- **API Mocking**: Mock API responses for testing

## 🚀 Deployment

### Production Build
```bash
npm run build
```

### Laravel Integration
The built assets are automatically integrated with Laravel using the Vite plugin.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

For support and questions:
- Check the documentation
- Review the code comments
- Open an issue on GitHub

---

**Happy Coding! 🎉**
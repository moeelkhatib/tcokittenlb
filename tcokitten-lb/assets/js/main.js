// Main JavaScript file for Take Care of Kitten Lebanon

// Cart management
class CartManager {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('cart')) || [];
        this.init();
    }

    init() {
        this.updateCartCount();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Save cart before page unload
        window.addEventListener('beforeunload', () => {
            localStorage.setItem('cart', JSON.stringify(this.cart));
        });
    }

    addToCart(product, quantity = 1) {
        const existingItem = this.cart.find(item => item.id === product.id);
        
        if (existingItem) {
            if (existingItem.quantity + quantity <= product.stock) {
                existingItem.quantity += quantity;
            } else {
                return { success: false, message: 'Stock limit reached' };
            }
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: quantity,
                stock: product.stock
            });
        }
        
        this.updateCartCount();
        return { success: true };
    }

    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.updateCartCount();
    }

    updateQuantity(productId, quantity) {
        const item = this.cart.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
        }
        this.updateCartCount();
    }

    getCartTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    getCartCount() {
        return this.cart.reduce((total, item) => total + item.quantity, 0);
    }

    updateCartCount() {
        const countElement = document.getElementById('cart-count');
        if (countElement) {
            countElement.textContent = this.getCartCount();
        }
    }

    clearCart() {
        this.cart = [];
        this.updateCartCount();
        localStorage.removeItem('cart');
    }
}

// Notification system
class NotificationManager {
    static show(message, type = 'success', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up ${this.getTypeClass(type)}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('hiding');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, duration);
    }

    static getTypeClass(type) {
        switch(type) {
            case 'success':
                return 'bg-green-500 text-white';
            case 'error':
                return 'bg-red-500 text-white';
            case 'warning':
                return 'bg-yellow-500 text-white';
            case 'info':
                return 'bg-blue-500 text-white';
            default:
                return 'bg-gray-800 text-white';
        }
    }
}

// Form validation
class FormValidator {
    static validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    static validatePhone(phone) {
        const re = /^[\+]?[0-9\s\-\(\)]+$/;
        return re.test(phone);
    }

    static validateRequired(field) {
        return field && field.trim().length > 0;
    }

    static validateForm(formData, rules) {
        const errors = {};
        
        for (const [field, rule] of Object.entries(rules)) {
            const value = formData.get(field);
            
            if (rule.required && !this.validateRequired(value)) {
                errors[field] = `${field} is required`;
                continue;
            }
            
            if (rule.email && !this.validateEmail(value)) {
                errors[field] = 'Please enter a valid email address';
                continue;
            }
            
            if (rule.phone && !this.validatePhone(value)) {
                errors[field] = 'Please enter a valid phone number';
                continue;
            }
            
            if (rule.minLength && value.length < rule.minLength) {
                errors[field] = `Minimum ${rule.minLength} characters required`;
                continue;
            }
            
            if (rule.maxLength && value.length > rule.maxLength) {
                errors[field] = `Maximum ${rule.maxLength} characters allowed`;
                continue;
            }
        }
        
        return errors;
    }
}

// Search functionality
class SearchManager {
    static init() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch, 300));
        }
    }

    static handleSearch(event) {
        const searchTerm = event.target.value.trim();
        if (searchTerm.length >= 2) {
            // In a real implementation, this would make an API call
            console.log('Searching for:', searchTerm);
        }
    }

    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Product filter
class ProductFilter {
    static filterByCategory(category) {
        const url = new URL(window.location);
        url.searchParams.set('category', category);
        window.location.href = url.toString();
    }

    static filterByPrice(min, max) {
        const url = new URL(window.location);
        url.searchParams.set('min_price', min);
        url.searchParams.set('max_price', max);
        window.location.href = url.toString();
    }

    static sortProducts(sortBy) {
        const url = new URL(window.location);
        url.searchParams.set('sort', sortBy);
        window.location.href = url.toString();
    }
}

// Global functions
function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    if (cartSidebar) {
        cartSidebar.classList.toggle('translate-x-full');
        if (!cartSidebar.classList.contains('translate-x-full')) {
            updateCartDisplay();
        }
    }
}

function updateCartDisplay() {
    if (window.cartManager) {
        window.cartManager.updateCartCount();
    }
}

function showNotification(message, type = 'success') {
    NotificationManager.show(message, type);
}

// Add to cart function (global)
window.addToCart = function(productId) {
    // In a real implementation, this would fetch product details from an API
    const product = {
        id: productId,
        name: 'Product Name',
        price: 0,
        image: '',
        stock: 10
    };
    
    const result = window.cartManager.addToCart(product);
    if (result.success) {
        showNotification('Product added to cart!');
    } else {
        showNotification(result.message, 'error');
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart manager
    window.cartManager = new CartManager();
    
    // Initialize search
    SearchManager.init();
    
    // Cart sidebar toggle
    const cartButton = document.querySelector('button[onclick*="toggleCart"]');
    if (cartButton) {
        cartButton.addEventListener('click', toggleCart);
    }
    
    // Quantity input controls
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('quantity-increase')) {
            const input = event.target.parentElement.querySelector('.quantity-input');
            input.value = parseInt(input.value) + 1;
        }
        
        if (event.target.classList.contains('quantity-decrease')) {
            const input = event.target.parentElement.querySelector('.quantity-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img.lazy').forEach(img => imageObserver.observe(img));
    }
});

<?php
require_once 'includes/config.php';
require_once 'includes/cart-functions.php';
$current_page = 'cart';

// Load cart from database if user is logged in
if (isLoggedIn()) {
    loadCartFromDatabase();
}

$cartItems = getCartItems();
$cartTotal = getCartTotal();
$cartCount = getCartCount();
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Cart Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Shopping Cart</h1>
            <p class="text-xl opacity-90">Review your items and proceed to checkout</p>
        </div>
    </section>

    <!-- Cart Content -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <?php if ($cartCount == 0): ?>
                <div class="text-center py-16">
                    <div class="w-32 h-32 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-shopping-cart text-5xl text-gray-400"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h2>
                    <p class="text-gray-600 mb-8">Looks like you haven't added any products to your cart yet.</p>
                    <a href="products.php" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-shopping-bag mr-2"></i>Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <div class="p-6 border-b">
                                <h2 class="text-xl font-bold text-dark">Cart Items (<?php echo $cartCount; ?>)</h2>
                            </div>
                            
                            <div id="cart-items-container">
                                <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item p-6 border-b" data-product-id="<?php echo $item['id']; ?>">
                                    <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0">
                                        <!-- Product Image -->
                                        <div class="md:w-32 md:mr-6">
                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" 
                                                 class="w-full h-32 object-cover rounded-lg">
                                        </div>
                                        
                                        <!-- Product Info -->
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-dark mb-2"><?php echo $item['name']; ?></h3>
                                            <div class="flex items-center justify-between">
                                                <div class="text-2xl font-bold text-primary">
                                                    $<?php echo number_format($item['price'], 2); ?>
                                                </div>
                                                <div class="flex items-center space-x-4">
                                                    <!-- Quantity Controls -->
                                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                                        <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                                                class="px-4 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50"
                                                                <?php if ($item['quantity'] <= 1): ?>disabled<?php endif; ?>>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <span class="px-4 py-2 w-16 text-center quantity-display"><?php echo $item['quantity']; ?></span>
                                                        <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)" 
                                                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Remove Button -->
                                                    <button onclick="removeFromCart(<?php echo $item['id']; ?>)" 
                                                            class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-trash text-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Item Total -->
                                    <div class="mt-4 text-right">
                                        <span class="text-gray-600">Item Total:</span>
                                        <span class="ml-2 text-xl font-bold text-dark item-total">
                                            $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Continue Shopping -->
                            <div class="p-6">
                                <a href="products.php" class="inline-flex items-center text-primary hover:text-indigo-700">
                                    <i class="fas fa-arrow-left mr-2"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                            <h2 class="text-xl font-bold text-dark mb-6">Order Summary</h2>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span id="cart-subtotal" class="font-semibold">$<?php echo number_format($cartTotal, 2); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="font-semibold">$5.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="font-semibold">$0.00</span>
                                </div>
                            </div>
                            
                            <div class="border-t pt-6 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-dark">Total</span>
                                    <span id="cart-grand-total" class="text-2xl font-bold text-primary">
                                        $<?php echo number_format($cartTotal + 5.00, 2); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Checkout Button -->
                            <a href="checkout.php" 
                               class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors mb-4 text-center">
                                <i class="fas fa-shopping-bag mr-2"></i>Proceed to Checkout
                            </a>
                            
                            <!-- Secure Checkout Message -->
                            <div class="text-center text-sm text-gray-500">
                                <i class="fas fa-lock mr-1"></i> Secure checkout
                            </div>
                            
                            <!-- Payment Methods -->
                            <div class="mt-6 pt-6 border-t">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">We Accept</h3>
                                <div class="flex justify-center space-x-4">
                                    <i class="fab fa-cc-visa text-2xl text-gray-400"></i>
                                    <i class="fab fa-cc-mastercard text-2xl text-gray-400"></i>
                                    <i class="fab fa-cc-amex text-2xl text-gray-400"></i>
                                    <i class="fas fa-money-bill-wave text-2xl text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script>
// Global cart functions
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch('add-to-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in header
            document.getElementById('cart-count').textContent = data.count;
            
            // Show success message
            showNotification('Product added to cart!', 'success');
            
            // Update sidebar cart if open
            if (document.getElementById('cart-sidebar').classList.contains('translate-x-0')) {
                updateCartSidebar();
            }
            
            // If on cart page, refresh it
            if (window.location.pathname.includes('cart.php')) {
                window.location.reload();
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('remove-from-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in header
            document.getElementById('cart-count').textContent = data.count;
            
            // Remove item from cart page if on cart page
            if (window.location.pathname.includes('cart.php')) {
                const itemElement = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                if (itemElement) {
                    itemElement.remove();
                }
                
                // Update totals
                document.getElementById('cart-subtotal').textContent = '$' + data.total.toFixed(2);
                document.getElementById('cart-grand-total').textContent = '$' + (data.total + 5.00).toFixed(2);
                
                // If cart is empty, show empty message
                if (data.count === 0) {
                    window.location.reload();
                }
            }
            
            // Update sidebar cart
            updateCartSidebar();
            
            showNotification('Item removed from cart', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function updateCartQuantity(productId, newQuantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', newQuantity);
    
    fetch('update-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in header
            document.getElementById('cart-count').textContent = data.count;
            
            // If on cart page, update the specific item
            if (window.location.pathname.includes('cart.php')) {
                const itemElement = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                if (itemElement) {
                    const quantityDisplay = itemElement.querySelector('.quantity-display');
                    const itemTotal = itemElement.querySelector('.item-total');
                    const price = parseFloat(itemElement.querySelector('.text-primary').textContent.replace('$', ''));
                    
                    if (newQuantity <= 0) {
                        itemElement.remove();
                    } else {
                        quantityDisplay.textContent = newQuantity;
                        itemTotal.textContent = '$' + (price * newQuantity).toFixed(2);
                    }
                    
                    // Update totals
                    document.getElementById('cart-subtotal').textContent = '$' + data.total.toFixed(2);
                    document.getElementById('cart-grand-total').textContent = '$' + (data.total + 5.00).toFixed(2);
                }
            }
            
            // Update sidebar cart
            updateCartSidebar();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function updateCartSidebar() {
    // Fetch updated cart sidebar content
    fetch('cart-sidebar-content.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById('cart-items').innerHTML = html;
            document.getElementById('cart-total').textContent = '$' + getCartTotal().toFixed(2);
        });
}

function showNotification(message, type = 'success') {
    // Remove existing notification
    const existingNotification = document.querySelector('.cart-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `cart-notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg animate-fade-in ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Toggle cart sidebar (from header.php)
function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    cartSidebar.classList.toggle('translate-x-full');
    cartSidebar.classList.toggle('translate-x-0');
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    document.getElementById('cart-count').textContent = <?php echo getCartCount(); ?>;
});
</script>

<style>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
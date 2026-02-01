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
            if (document.getElementById('cart-sidebar') && 
                document.getElementById('cart-sidebar').classList.contains('translate-x-0')) {
                updateCartSidebar();
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
    const cartSidebar = document.getElementById('cart-sidebar');
    if (cartSidebar && cartSidebar.classList.contains('translate-x-0')) {
        // Fetch updated cart sidebar content
        fetch('cart-sidebar-content.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('cart-items').innerHTML = html;
                // Update total in sidebar
                fetch('get-cart-total.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('cart-total').textContent = '$' + data.total.toFixed(2);
                        }
                    });
            });
    }
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

// Toggle cart sidebar
function toggleCart() {
    const cartSidebar = document.getElementById('cart-sidebar');
    if (cartSidebar) {
        cartSidebar.classList.toggle('translate-x-full');
        cartSidebar.classList.toggle('translate-x-0');
        
        // Update cart sidebar content when opening
        if (cartSidebar.classList.contains('translate-x-0')) {
            updateCartSidebar();
        }
    }
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    if (document.getElementById('cart-count')) {
        // You might want to fetch the current cart count via AJAX
        // For now, it's set by PHP on page load
    }
    
    // Add notification styles
    const style = document.createElement('style');
    style.textContent = `
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
});
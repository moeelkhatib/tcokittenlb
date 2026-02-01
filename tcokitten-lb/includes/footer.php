    <!-- Footer -->
    <footer class="bg-dark text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                            <i class="fas fa-paw text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Take Care of Kitten</h3>
                            <p class="text-sm text-gray-400">Lebanon</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6">Providing premium cat products and expert care advice.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="products.php" class="text-gray-400 hover:text-white transition-colors">All Products</a></li>
                        <li><a href="categories.php" class="text-gray-400 hover:text-white transition-colors">Categories</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="text-lg font-semibold mb-6">Categories</h4>
                    <ul class="space-y-3">
                        <li><a href="products.php?category=food" class="text-gray-400 hover:text-white transition-colors">Dry Food</a></li>
                        <li><a href="products.php?category=food" class="text-gray-400 hover:text-white transition-colors">Wet Food</a></li>
                        <li><a href="products.php?category=toys" class="text-gray-400 hover:text-white transition-colors">Interactive Toys</a></li>
                        <li><a href="products.php?category=accessories" class="text-gray-400 hover:text-white transition-colors">Beds & Furniture</a></li>
                        <li><a href="products.php?category=health" class="text-gray-400 hover:text-white transition-colors">Health & Wellness</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-6">Contact Us</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                            <span class="text-gray-400">Online Shop ONLY</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone text-primary"></i>
                            <span class="text-gray-400">+961 76 605 040</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-primary"></i>
                            <span class="text-gray-400">infoattcokittenlb@gmail.com</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-clock text-primary"></i>
                            <span class="text-gray-400">Mon-Sat: 9AM - 8PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Take Care of Kitten Lebanon. All rights reserved.</p>
                <p class="mt-2 text-sm">Designed with <i class="fas fa-heart text-red-500"></i>  for cat lovers</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
        // Cart management functions
        function addToCart(productId) {
            fetch('api/cart_add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-count').textContent = data.count;
                    updateCartDisplay();
                    showNotification('Product added to cart!');
                } else {
                    alert(data.message);
                }
            });
        }

        function removeFromCart(productId) {
            fetch('api/cart_remove.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('cart-count').textContent = data.count;
                updateCartDisplay();
            });
        }

        function updateCartQuantity(productId, quantity) {
            if (quantity < 1) return;
            fetch('api/cart_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('cart-count').textContent = data.count;
                updateCartDisplay();
            });
        }

        function updateCartDisplay() {
            fetch('api/cart_get.php')
                .then(response => response.json())
                .then(data => {
                    const cartItems = document.getElementById('cart-items');
                    const cartTotal = document.getElementById('cart-total');
                    
                    if (data.cart.length === 0) {
                        cartItems.innerHTML = `
                            <div class="text-center py-12">
                                <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Your cart is empty</p>
                                <a href="products.php" class="mt-4 text-primary hover:underline inline-block">
                                    Start Shopping
                                </a>
                            </div>
                        `;
                        cartTotal.textContent = '$0.00';
                    } else {
                        let cartHTML = '';
                        let total = 0;
                        
                        data.cart.forEach(item => {
                            const itemTotal = item.price * item.quantity;
                            total += itemTotal;
                            
                            cartHTML += `
                                <div class="flex items-center space-x-4 mb-6 pb-6 border-b">
                                    <img src="${item.image}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-dark">${item.name}</h4>
                                        <p class="text-gray-600 text-sm">$${item.price.toFixed(2)} each</p>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <button onclick="updateCartQuantity(${item.id}, ${item.quantity - 1})" 
                                                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <span class="w-10 text-center">${item.quantity}</span>
                                            <button onclick="updateCartQuantity(${item.id}, ${item.quantity + 1})" 
                                                    class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">$${itemTotal.toFixed(2)}</p>
                                        <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 text-sm mt-2">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        
                        cartItems.innerHTML = cartHTML;
                        cartTotal.textContent = `$${total.toFixed(2)}`;
                    }
                });
        }

        // Cart sidebar toggle
        function toggleCart() {
            const cartSidebar = document.getElementById('cart-sidebar');
            cartSidebar.classList.toggle('translate-x-full');
            if (!cartSidebar.classList.contains('translate-x-full')) {
                updateCartDisplay();
            }
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        }

        // Show notification
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Close cart when clicking outside
        document.addEventListener('click', (event) => {
            const cartSidebar = document.getElementById('cart-sidebar');
            if (!cartSidebar.contains(event.target) && 
                !event.target.closest('button[onclick*="toggleCart"]') &&
                !event.target.closest('.fa-shopping-cart')) {
                cartSidebar.classList.add('translate-x-full');
            }
        });
    </script>
</body>
</html>
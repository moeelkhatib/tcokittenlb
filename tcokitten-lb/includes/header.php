<?php
require_once 'config.php';
$current_page = getCurrentPage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo ucfirst($current_page); ?></title>
    <meta name="description" content="Take Care of Kitten Lebanon offers premium cat food, toys, and accessories. Shop high-quality products for your feline friends with easy checkout and delivery across Lebanon.">
    <meta name="keywords" content="cat food, cat toys, cat accessories, Lebanon, pet supplies, kitten care">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#4F46E5",
                        secondary: "#F59E0B",
                        accent: "#10B981",
                        dark: "#1F2937",
                        light: "#F9FAFB"
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                        'quicksand': ['Quicksand', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'slide-down': 'slideDown 0.3s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gray-50 font-poppins">
    <!-- Header & Navigation -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <a href="index.php" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                            <i class="fas fa-paw text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-dark">Take Care of Kitten</h1>
                            <p class="text-xs text-gray-500">Lebanon</p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-dark hover:text-primary font-medium transition-colors <?php echo ($current_page == 'index' || $current_page == 'home') ? 'text-primary' : ''; ?>">Home</a>
                    <a href="products.php" class="text-dark hover:text-primary font-medium transition-colors <?php echo $current_page == 'products' ? 'text-primary' : ''; ?>">Products</a>
                    <a href="categories.php" class="text-dark hover:text-primary font-medium transition-colors <?php echo $current_page == 'categories' ? 'text-primary' : ''; ?>">Categories</a>
                    <a href="about.php" class="text-dark hover:text-primary font-medium transition-colors <?php echo $current_page == 'about' ? 'text-primary' : ''; ?>">About Us</a>
                    <a href="contact.php" class="text-dark hover:text-primary font-medium transition-colors <?php echo $current_page == 'contact' ? 'text-primary' : ''; ?>">Contact</a>
                </nav>

                <!-- Cart & User Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block relative">
                        <form action="products.php" method="GET" class="relative">
                            <input type="text" name="search" placeholder="Search products..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent w-64"
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="absolute left-3 top-3 text-gray-400">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Cart Icon -->
                    <div class="relative">
                        <a href="cart.php">
                            <button onclick="toggleCart()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <i class="fas fa-shopping-cart text-xl text-dark"></i>
                                <span id="cart-count" class="cart-badge"><?php echo getCartCount(); ?></span>
                            </button>
                        </a>
                    </div>
                    
                    <!-- User Account -->
                    <div class="hidden md:block">
                        <?php if (isLoggedIn()): ?>
                            <a href="account.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <i class="fas fa-user-circle text-xl text-dark"></i>
                                <span class="text-dark font-medium">My Account</span>
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <i class="fas fa-user-circle text-xl text-dark"></i>
                                <span class="text-dark font-medium">Login</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2">
                        <i class="fas fa-bars text-xl text-dark"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t shadow-lg absolute left-0 right-0 top-full z-50">
            <div class="container mx-auto px-4 py-4">
                <div class="space-y-4">
                    <a href="index.php" class="block py-3 px-4 text-dark hover:text-primary hover:bg-gray-50 rounded-lg <?php echo ($current_page == 'index' || $current_page == 'home') ? 'text-primary bg-indigo-50' : ''; ?>">
                        <i class="fas fa-home mr-3"></i>Home
                    </a>
                    <a href="products.php" class="block py-3 px-4 text-dark hover:text-primary hover:bg-gray-50 rounded-lg <?php echo $current_page == 'products' ? 'text-primary bg-indigo-50' : ''; ?>">
                        <i class="fas fa-box mr-3"></i>Products
                    </a>
                    <a href="categories.php" class="block py-3 px-4 text-dark hover:text-primary hover:bg-gray-50 rounded-lg <?php echo $current_page == 'categories' ? 'text-primary bg-indigo-50' : ''; ?>">
                        <i class="fas fa-list mr-3"></i>Categories
                    </a>
                    <a href="about.php" class="block py-3 px-4 text-dark hover:text-primary hover:bg-gray-50 rounded-lg <?php echo $current_page == 'about' ? 'text-primary bg-indigo-50' : ''; ?>">
                        <i class="fas fa-info-circle mr-3"></i>About Us
                    </a>
                    <a href="contact.php" class="block py-3 px-4 text-dark hover:text-primary hover:bg-gray-50 rounded-lg <?php echo $current_page == 'contact' ? 'text-primary bg-indigo-50' : ''; ?>">
                        <i class="fas fa-envelope mr-3"></i>Contact
                    </a>
                    
                    <div class="pt-4 border-t">
                        <form action="products.php" method="GET" class="mb-4">
                            <div class="relative">
                                <input type="text" name="search" placeholder="Search products..." 
                                       class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <button type="submit" class="absolute left-4 top-3 text-gray-400">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="flex items-center justify-between">
                            <?php if (isLoggedIn()): ?>
                                <a href="account.php" class="flex items-center py-2 px-4 text-dark hover:text-primary">
                                    <i class="fas fa-user-circle mr-2"></i>My Account
                                </a>
                                <a href="logout.php" class="flex items-center py-2 px-4 text-dark hover:text-primary">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="flex items-center py-2 px-4 text-dark hover:text-primary">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                                </a>
                                <a href="register.php" class="flex items-center py-2 px-4 bg-primary text-white rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-user-plus mr-2"></i>Register
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Shopping Cart Sidebar -->
    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-full md:w-96 bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50">
        <div class="h-full flex flex-col">
            <!-- Cart Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-xl font-bold text-dark">Shopping Cart</h2>
                <button onclick="toggleCart()" class="p-2 hover:bg-gray-100 rounded-full">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Cart Items -->
            <div id="cart-items" class="flex-1 overflow-y-auto p-6">
                <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Your cart is empty</p>
                        <a href="products.php" class="mt-4 text-primary hover:underline inline-block">
                            Start Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="flex items-center space-x-4 mb-6 pb-6 border-b">
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-semibold text-dark"><?php echo $item['name']; ?></h4>
                                <p class="text-gray-600 text-sm">$<?php echo number_format($item['price'], 2); ?> each</p>
                                <div class="flex items-center space-x-2 mt-2">
                                    <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="w-10 text-center"><?php echo $item['quantity']; ?></span>
                                    <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)" 
                                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="text-red-500 hover:text-red-700 text-sm mt-2">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Cart Footer -->
            <div class="border-t p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold">Total:</span>
                    <span id="cart-total" class="text-2xl font-bold text-primary">$<?php echo number_format(getCartTotal(), 2); ?></span>
                </div>
                <a href="checkout.php" class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors mb-3 text-center">
                    <i class="fas fa-shopping-bag mr-2"></i>Proceed to Checkout
                </a>
                <button onclick="toggleCart()" class="w-full border border-gray-300 text-dark py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                    Continue Shopping
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script>
    // Mobile menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuBtn && mobileMenu) {
            // Toggle mobile menu
            mobileMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle('hidden');
                
                // Change icon
                const icon = mobileMenuBtn.querySelector('i');
                if (mobileMenu.classList.contains('hidden')) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                } else {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                    const icon = mobileMenuBtn.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
            
            // Prevent menu from closing when clicking inside it
            mobileMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    </script>
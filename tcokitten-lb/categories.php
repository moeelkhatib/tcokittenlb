
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'categories';

// Get products by category
$categories = [
    'food' => getProductsByCategory('food', 4, 1),
    'toys' => getProductsByCategory('toys', 4, 1),
    'accessories' => getProductsByCategory('accessories', 4, 1),
    'health' => getProductsByCategory('health', 4, 1)
];
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Categories Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Product Categories</h1>
            <p class="text-xl opacity-90">Browse our wide range of cat products by category</p>
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Food Category -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-blue-100 p-6 text-center">
                        <i class="fas fa-utensils text-blue-600 text-4xl mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Cat Food</h2>
                        <p class="text-gray-600">Premium nutrition for your cat</p>
                        <a href="products.php?category=food" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700">
                            View All
                        </a>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Popular Items</h3>
                        <?php if (!empty($categories['food'])): ?>
                            <?php foreach (array_slice($categories['food'], 0, 3) as $product): ?>
                            <div class="flex items-center mb-4 pb-4 border-b last:border-0">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-16 h-16 object-cover rounded-lg">
                                <div class="ml-4 flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm"><?php echo $product['name']; ?></h4>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-primary font-bold">$<?php echo number_format($product['price'], 2); ?></span>
                                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Toys Category -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-green-100 p-6 text-center">
                        <i class="fas fa-basketball-ball text-green-600 text-4xl mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Toys</h2>
                        <p class="text-gray-600">Fun and interactive toys</p>
                        <a href="products.php?category=toys" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700">
                            View All
                        </a>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Popular Items</h3>
                        <?php if (!empty($categories['toys'])): ?>
                            <?php foreach (array_slice($categories['toys'], 0, 3) as $product): ?>
                            <div class="flex items-center mb-4 pb-4 border-b last:border-0">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-16 h-16 object-cover rounded-lg">
                                <div class="ml-4 flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm"><?php echo $product['name']; ?></h4>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-primary font-bold">$<?php echo number_format($product['price'], 2); ?></span>
                                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Accessories Category -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-purple-100 p-6 text-center">
                        <i class="fas fa-cat text-purple-600 text-4xl mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Accessories</h2>
                        <p class="text-gray-600">Comfort and style for your cat</p>
                        <a href="products.php?category=accessories" class="inline-block mt-4 bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700">
                            View All
                        </a>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Popular Items</h3>
                        <?php if (!empty($categories['accessories'])): ?>
                            <?php foreach (array_slice($categories['accessories'], 0, 3) as $product): ?>
                            <div class="flex items-center mb-4 pb-4 border-b last:border-0">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-16 h-16 object-cover rounded-lg">
                                <div class="ml-4 flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm"><?php echo $product['name']; ?></h4>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-primary font-bold">$<?php echo number_format($product['price'], 2); ?></span>
                                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="text-purple-600 hover:text-purple-800">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Health Category -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-red-100 p-6 text-center">
                        <i class="fas fa-heart text-red-600 text-4xl mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Health Care</h2>
                        <p class="text-gray-600">Keep your cat healthy and happy</p>
                        <a href="products.php?category=health" class="inline-block mt-4 bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700">
                            View All
                        </a>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Popular Items</h3>
                        <?php if (!empty($categories['health'])): ?>
                            <?php foreach (array_slice($categories['health'], 0, 3) as $product): ?>
                            <div class="flex items-center mb-4 pb-4 border-b last:border-0">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-16 h-16 object-cover rounded-lg">
                                <div class="ml-4 flex-1">
                                    <h4 class="font-medium text-gray-800 text-sm"><?php echo $product['name']; ?></h4>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-primary font-bold">$<?php echo number_format($product['price'], 2); ?></span>
                                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Brands -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Trusted Brands</h2>
                <p class="text-gray-600">We carry only the best brands for your feline friends</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
                <div class="bg-white rounded-lg p-6 flex items-center justify-center">
                    <i class="fas fa-paw text-3xl text-gray-400"></i>
                    <span class="ml-2 font-semibold text-gray-700">Whiskas</span>
                </div>
                <div class="bg-white rounded-lg p-6 flex items-center justify-center">
                    <i class="fas fa-fish text-3xl text-gray-400"></i>
                    <span class="ml-2 font-semibold text-gray-700">Royal Canin</span>
                </div>
                <div class="bg-white rounded-lg p-6 flex items-center justify-center">
                    <i class="fas fa-bone text-3xl text-gray-400"></i>
                    <span class="ml-2 font-semibold text-gray-700">Purina</span>
                </div>
                <div class="bg-white rounded-lg p-6 flex items-center justify-center">
                    <i class="fas fa-heart text-3xl text-gray-400"></i>
                    <span class="ml-2 font-semibold text-gray-700">Hill's</span>
                </div>
                <div class="bg-white rounded-lg p-6 flex items-center justify-center">
                    <i class="fas fa-star text-3xl text-gray-400"></i>
                    <span class="ml-2 font-semibold text-gray-700">IAMS</span>
                </div>
                <div class="bg-white rounded-lg p-6 flex items-center justify-center">
                    <i class="fas fa-cat text-3xl text-gray-400"></i>
                    <span class="ml-2 font-semibold text-gray-700">Friskies</span>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
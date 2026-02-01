<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'index';
$featured_products = getFeaturedProducts(8);
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white">
        <div class="container mx-auto px-4 py-16 md:py-24">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">Premium Care for Your Feline Friends</h2>
                    <p class="text-xl mb-8 opacity-90">Discover Lebanon's finest selection of cat food, toys, and accessories. Quality products for happy, healthy kittens.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="products.php" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            Shop Now <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="categories.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors">
                            View Categories
                        </a>
                    </div>
                </div>
                <div class="animate-fade-in">
                    <img src="https://picsum.photos/600/400?random=1" alt="Happy cat with toys" class="rounded-2xl shadow-2xl" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-dark mb-12">Shop by Category</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <a href="products.php?category=food" class="category-card bg-light rounded-xl p-6 text-center hover:shadow-lg transition-shadow cursor-pointer block">
                    <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Cat Food</h3>
                    <p class="text-gray-600">Premium nutrition for all life stages</p>
                </a>
                <a href="products.php?category=toys" class="category-card bg-light rounded-xl p-6 text-center hover:shadow-lg transition-shadow cursor-pointer block">
                    <div class="w-20 h-20 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-baseball-ball text-secondary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Toys</h3>
                    <p class="text-gray-600">Interactive and engaging playthings</p>
                </a>
                <a href="products.php?category=accessories" class="category-card bg-light rounded-xl p-6 text-center hover:shadow-lg transition-shadow cursor-pointer block">
                    <div class="w-20 h-20 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tshirt text-accent text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Accessories</h3>
                    <p class="text-gray-600">Collars, beds, and grooming tools</p>
                </a>
                <a href="products.php?category=health" class="category-card bg-light rounded-xl p-6 text-center hover:shadow-lg transition-shadow cursor-pointer block">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-red-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">Health Care</h3>
                    <p class="text-gray-600">Vitamins and wellness products</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-12">
                <h2 class="text-3xl font-bold text-dark">Featured Products</h2>
                <a href="products.php" class="text-primary hover:text-indigo-700 font-semibold">
                    View All Products <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card bg-white rounded-xl shadow-md overflow-hidden animate-slide-up">
                    <div class="relative">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-48 object-cover">
                        <?php if (strpos($product['tags'], 'best-seller') !== false): ?>
                            <span class="absolute top-2 left-2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full">BEST SELLER</span>
                        <?php endif; ?>
                        <?php if ($product['original_price'] > $product['price']): ?>
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                SAVE <?php echo round((1 - $product['price']/$product['original_price']) * 100); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-dark"><?php echo $product['name']; ?></h3>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="font-medium"><?php echo $product['rating']; ?></span>
                                <span class="text-gray-500 text-sm ml-1">(<?php echo $product['reviews']; ?>)</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4"><?php echo $product['description']; ?></p>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-2xl font-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php if ($product['original_price'] > $product['price']): ?>
                                    <span class="text-gray-400 line-through ml-2">$<?php echo number_format($product['original_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-sm <?php echo $product['stock'] > 10 ? 'text-green-600' : 'text-orange-600'; ?>">
                                <i class="fas fa-box mr-1"></i><?php echo $product['stock']; ?> in stock
                            </span>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                            </button>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="w-12 border border-gray-300 text-dark rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-dark mb-12">Why Choose Take Care of Kitten?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shipping-fast text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-4">Fast Delivery</h3>
                    <p class="text-gray-600">Quick and reliable delivery across all Lebanon regions</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-award text-accent text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-4">Quality Guaranteed</h3>
                    <p class="text-gray-600">All products are vet-approved and premium quality</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-headset text-secondary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-4">Expert Support</h3>
                    <p class="text-gray-600">Our team of cat experts is here to help you 24/7</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-16 bg-gradient-to-r from-primary to-indigo-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Stay Updated with Cat Care Tips</h2>
            <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto">Subscribe to our newsletter for exclusive offers, new arrivals, and expert advice on kitten care.</p>
            <div class="max-w-md mx-auto">
                <form action="subscribe.php" method="POST" class="flex flex-col sm:flex-row gap-4">
                    <input type="email" name="email" placeholder="Your email address" required
                           class="flex-1 px-6 py-3 rounded-lg text-dark focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Subscribe <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </form>
                <p class="text-sm opacity-75 mt-4">We respect your privacy. Unsubscribe at any time.</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
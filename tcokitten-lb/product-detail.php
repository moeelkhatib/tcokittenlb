<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 1;
$product = getProductById($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}

$current_page = 'product-detail';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Take Care of Kitten Lebanon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 font-poppins">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div id="product-detail" class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Images -->
                <div>
                    <div class="bg-white rounded-xl shadow-md p-6 mb-4">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" 
                             class="w-full h-96 object-cover rounded-lg">
                    </div>
                    <div class="flex space-x-4">
                        <div class="w-24 h-24 bg-gray-100 rounded-lg cursor-pointer border-2 border-indigo-600">
                            <img src="<?php echo $product['image']; ?>" alt="Thumbnail" class="w-full h-full object-cover rounded">
                        </div>
                        <!-- Additional images would go here -->
                    </div>
                </div>
                
                <!-- Product Info -->
                <div>
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <?php if (strpos($product['tags'], 'best-seller') !== false): ?>
                            <span class="inline-block bg-indigo-600 text-white text-sm font-bold px-3 py-1 rounded-full mb-4">BEST SELLER</span>
                        <?php endif; ?>
                        
                        <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo $product['name']; ?></h1>
                        
                        <div class="flex items-center mb-6">
                            <div class="flex items-center mr-4">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= floor($product['rating']) ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                <?php endfor; ?>
                                <span class="ml-2 font-medium"><?php echo $product['rating']; ?></span>
                                <span class="text-gray-500 ml-1">(<?php echo $product['reviews']; ?> reviews)</span>
                            </div>
                            <span class="text-sm <?php echo $product['stock'] > 10 ? 'text-green-600' : 'text-orange-600'; ?>">
                                <i class="fas fa-box mr-1"></i><?php echo $product['stock']; ?> in stock
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-lg mb-8"><?php echo $product['description']; ?></p>
                        
                        <div class="mb-8">
                            <div class="text-4xl font-bold text-indigo-600 mb-2">
                                $<?php echo number_format($product['price'], 2); ?>
                                <?php if ($product['original_price'] > $product['price']): ?>
                                    <span class="text-lg text-gray-400 line-through ml-2">$<?php echo number_format($product['original_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($product['original_price'] > $product['price']): ?>
                                <div class="text-green-600 font-semibold">
                                    Save $<?php echo number_format($product['original_price'] - $product['price'], 2); ?> 
                                    (<?php echo round((1 - $product['price']/$product['original_price']) * 100); ?>%)
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Quantity Selector -->
                        <div class="mb-8">
                            <label class="block text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button onclick="updateQuantity(-1)" class="px-4 py-3 text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
                                           class="w-20 text-center border-0 focus:ring-0">
                                    <button onclick="updateQuantity(1)" class="px-4 py-3 text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <span class="text-gray-500">Max: <?php echo $product['stock']; ?> items</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex space-x-4 mb-8">
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                    class="flex-1 bg-indigo-600 text-white py-4 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                            </button>
                            <button onclick="buyNow(<?php echo $product['id']; ?>)" 
                                    class="flex-1 bg-green-600 text-white py-4 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                                <i class="fas fa-bolt mr-2"></i>Buy Now
                            </button>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="border-t pt-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Product Details</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600"><strong>Category:</strong> <?php echo ucfirst($product['category']); ?></p>
                                    <p class="text-gray-600"><strong>Tags:</strong> <?php echo $product['tags'] ?: 'N/A'; ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600"><strong>SKU:</strong> TCK-<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                    <p class="text-gray-600"><strong>Availability:</strong> <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Info -->
                    <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Shipping & Returns</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-shipping-fast text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold">Free Shipping</h4>
                                    <p class="text-sm text-gray-600">On orders over $50</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-sync-alt text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold">30-Day Returns</h4>
                                    <p class="text-sm text-gray-600">Money back guarantee</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-headset text-purple-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold">Support 24/7</h4>
                                    <p class="text-sm text-gray-600">We're here to help</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php
        $related_products = getProductsByCategory($product['category'], 4, 1);
        if (!empty($related_products)): 
        ?>
        <section class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($related_products as $related): ?>
                    <?php if ($related['id'] != $product['id']): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <img src="<?php echo $related['image']; ?>" alt="<?php echo $related['name']; ?>" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2"><?php echo $related['name']; ?></h3>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-indigo-600">$<?php echo number_format($related['price'], 2); ?></span>
                                <a href="product-detail.php?id=<?php echo $related['id']; ?>" 
                                        class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        function updateQuantity(change) {
            const input = document.getElementById('quantity');
            let quantity = parseInt(input.value) + change;
            const max = <?php echo $product['stock']; ?>;
            quantity = Math.max(1, Math.min(quantity, max));
            input.value = quantity;
        }

        function addToCart(productId) {
            const quantity = parseInt(document.getElementById('quantity').value);
            // Use the global addToCart function from main.js
            window.addToCart(productId, quantity);
        }

        function buyNow(productId) {
            const quantity = parseInt(document.getElementById('quantity').value);
            // Redirect to checkout with this product
            window.location.href = `checkout.php?product=${productId}&quantity=${quantity}`;
        }
    </script>
</body>
</html>
[file content end]
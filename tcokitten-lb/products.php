<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'products';

// Get query parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;

// Get products
if (!empty($search)) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE (name LIKE ? OR description LIKE ? OR tags LIKE ?) 
        AND stock > 0 
        LIMIT ? OFFSET ?
    ");
    $search_term = "%$search%";
    $offset = ($page - 1) * $limit;
    $stmt->execute([$search_term, $search_term, $search_term, $limit, $offset]);
    $products = $stmt->fetchAll();
    
    // Get total count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM products 
        WHERE (name LIKE ? OR description LIKE ? OR tags LIKE ?) 
        AND stock > 0
    ");
    $stmt->execute([$search_term, $search_term, $search_term]);
    $total_products = $stmt->fetch()['total'];
} else {
    $products = getProductsByCategory($category, $limit, $page);
    $pdo = getDBConnection();
    if ($category === 'all') {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE stock > 0");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE category = ? AND stock > 0");
        $stmt->execute([$category]);
    }
    $total_products = $stmt->fetch()['total'];
}

$total_pages = ceil($total_products / $limit);

// Get categories for filter
$categories = getCategories();
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Products Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">Our Products</h1>
            <p class="text-xl opacity-90">Discover our premium selection of cat products</p>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <!-- Filters and Search -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div class="w-full md:w-auto mb-4 md:mb-0">
                    <form action="products.php" method="GET" class="flex items-center space-x-4">
                        <select name="category" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="all" <?php echo $category == 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <option value="food" <?php echo $category == 'food' ? 'selected' : ''; ?>>Cat Food</option>
                            <option value="toys" <?php echo $category == 'toys' ? 'selected' : ''; ?>>Toys</option>
                            <option value="accessories" <?php echo $category == 'accessories' ? 'selected' : ''; ?>>Accessories</option>
                            <option value="health" <?php echo $category == 'health' ? 'selected' : ''; ?>>Health Care</option>
                        </select>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Search products..." 
                                   value="<?php echo $search; ?>"
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent w-64">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700">
                            Search
                        </button>
                    </form>
                </div>
                
                <div class="text-gray-600">
                    Showing <?php echo ($page - 1) * $limit + 1; ?>-<?php echo min($page * $limit, $total_products); ?> of <?php echo $total_products; ?> products
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php if (empty($products)): ?>
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search or filter criteria</p>
                        <a href="products.php" class="text-primary hover:text-indigo-700 font-semibold">
                            View All Products
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
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
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center mt-12">
                <nav class="flex items-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="products.php?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" 
                           class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="products.php?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" 
                               class="px-4 py-2 border rounded-lg <?php echo $i == $page ? 'bg-primary text-white border-primary' : 'border-gray-300 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span class="px-4 py-2">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="products.php?category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" 
                           class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
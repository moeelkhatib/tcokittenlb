<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$current_page = 'admin-products';
$pdo = getDBConnection();

// Get filter parameters
$category = $_GET['category'] ?? '';
$stock_status = $_GET['stock_status'] ?? '';
$featured = $_GET['featured'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($stock_status) {
    if ($stock_status == 'in_stock') {
        $query .= " AND stock > 0";
    } elseif ($stock_status == 'out_of_stock') {
        $query .= " AND stock = 0";
    } elseif ($stock_status == 'low_stock') {
        $query .= " AND stock > 0 AND stock <= 10";
    }
}

if ($featured !== '') {
    $query .= " AND featured = ?";
    $params[] = $featured;
}

if ($search) {
    $query .= " AND (name LIKE ? OR description LIKE ? OR tags LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Get products
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get total count for pagination
$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", explode("ORDER BY", $query)[0]);
$count_stmt = $pdo->prepare($count_query);
$count_params = array_slice($params, 0, -2);
$count_stmt->execute($count_params);
$total_products = $count_stmt->fetch()['total'];
$total_pages = ceil($total_products / $limit);

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$product_id])) {
            $_SESSION['success_message'] = "Product deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete product.";
        }
        header("Location: admin-products.php");
        exit;
    }
    
    if (isset($_POST['toggle_featured'])) {
        $product_id = $_POST['product_id'];
        $featured = $_POST['featured'];
        
        $stmt = $pdo->prepare("UPDATE products SET featured = ? WHERE id = ?");
        if ($stmt->execute([$featured, $product_id])) {
            $_SESSION['success_message'] = "Product updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update product.";
        }
        header("Location: admin-products.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'admin-sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Product Management</h1>
                        <p class="text-gray-600">Manage your product catalog</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Updated link to admin-add-product.php -->
                        <a href="admin-add-product.php" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>Add New Product
                        </a>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <span class="font-medium"><?php echo $_SESSION['user_name'] ?? 'Administrator'; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 m-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="p-6">
                <div class="bg-white rounded-xl shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Products</h3>
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Categories</option>
                                <option value="food" <?php echo $category == 'food' ? 'selected' : ''; ?>>Food</option>
                                <option value="toys" <?php echo $category == 'toys' ? 'selected' : ''; ?>>Toys</option>
                                <option value="accessories" <?php echo $category == 'accessories' ? 'selected' : ''; ?>>Accessories</option>
                                <option value="health" <?php echo $category == 'health' ? 'selected' : ''; ?>>Health</option>
                                <option value="litter" <?php echo $category == 'litter' ? 'selected' : ''; ?>>Litter</option>
                                <option value="travel" <?php echo $category == 'travel' ? 'selected' : ''; ?>>Travel</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                            <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">All</option>
                                <option value="in_stock" <?php echo $stock_status == 'in_stock' ? 'selected' : ''; ?>>In Stock</option>
                                <option value="low_stock" <?php echo $stock_status == 'low_stock' ? 'selected' : ''; ?>>Low Stock</option>
                                <option value="out_of_stock" <?php echo $stock_status == 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Featured</label>
                            <select name="featured" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">All</option>
                                <option value="1" <?php echo $featured === '1' ? 'selected' : ''; ?>>Featured Only</option>
                                <option value="0" <?php echo $featured === '0' ? 'selected' : ''; ?>>Non-Featured Only</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Product name, description..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-5 flex justify-end space-x-3">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                <i class="fas fa-filter mr-2"></i>Apply Filters
                            </button>
                            <a href="admin-products.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Products (<?php echo $total_products; ?>)</h3>
                        <div class="flex space-x-3">
                            <button onclick="printProducts()" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                            <button onclick="exportProducts()" class="text-sm bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-md">
                                <i class="fas fa-download mr-2"></i>Export CSV
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-box-open text-3xl mb-3 text-gray-300"></i>
                                            <p>No products found</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0">
                                                        <img class="h-10 w-10 rounded-md object-cover" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                                        <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    <?php echo ucfirst($product['category']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">$<?php echo number_format($product['price'], 2); ?></div>
                                                <?php if ($product['original_price'] > $product['price']): ?>
                                                    <div class="text-xs text-gray-500 line-through">$<?php echo number_format($product['original_price'], 2); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo $product['stock']; ?></div>
                                                <div class="text-xs <?php echo $product['stock'] > 10 ? 'text-green-600' : 'text-red-600'; ?>">
                                                    <?php echo $product['stock'] > 10 ? 'In Stock' : ($product['stock'] > 0 ? 'Low Stock' : 'Out of Stock'); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                                    <span class="text-sm font-medium"><?php echo $product['rating']; ?></span>
                                                    <span class="text-xs text-gray-500 ml-1">(<?php echo $product['reviews']; ?>)</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" name="toggle_featured" value="1">
                                                    <input type="hidden" name="featured" value="<?php echo $product['featured'] ? '0' : '1'; ?>">
                                                    <button type="submit" class="text-sm <?php echo $product['featured'] ? 'text-yellow-600 hover:text-yellow-800' : 'text-gray-400 hover:text-gray-600'; ?>">
                                                        <i class="fas fa-star <?php echo $product['featured'] ? 'text-yellow-400' : ''; ?>"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="admin-product-edit.php?id=<?php echo $product['id']; ?>" 
                                                       class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" 
                                                       class="text-blue-600 hover:text-blue-900" title="View" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button onclick="confirmDelete('<?php echo $product['id']; ?>', '<?php echo htmlspecialchars(addslashes($product['name'])); ?>')" 
                                                            class="text-red-600 hover:text-red-900" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="px-6 py-4 border-t flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $limit, $total_products); ?> of <?php echo $total_products; ?> results
                            </div>
                            <nav class="flex space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                           class="px-3 py-2 border rounded-md text-sm font-medium <?php echo $i == $page ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                        <span class="px-3 py-2 text-gray-500">...</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                       class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Product</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="productName" class="font-semibold"></span>? This action cannot be undone.</p>
                <form id="deleteForm" method="POST">
                    <input type="hidden" name="product_id" id="delete_product_id">
                    <input type="hidden" name="delete_product" value="1">
                    
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Delete modal functions
        function confirmDelete(productId, productName) {
            document.getElementById('delete_product_id').value = productId;
            document.getElementById('productName').textContent = productName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Export function
        function exportProducts() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = 'api/export_products.php?' + params.toString();
        }

        // Print function
        function printProducts() {
            window.print();
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const deleteModal = document.getElementById('deleteModal');
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>
[file content end]
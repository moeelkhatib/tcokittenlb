<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$current_page = 'account';

// Get user data
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_email = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user['email']]);
$orders = $stmt->fetchAll();

// Handle profile update
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Update basic info
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $address, $_SESSION['user_id']]);
    
    // Update password if provided
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                $success = 'Profile and password updated successfully!';
            } else {
                $error = 'Current password is incorrect.';
            }
        } else {
            $error = 'New passwords do not match.';
        }
    } else {
        $success = 'Profile updated successfully!';
    }
    
    // Update session
    $_SESSION['user_name'] = $name;
}

// Check if user is admin - using function from config.php
// No need to redeclare isAdmin() function here
?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- Account Header -->
    <section class="bg-gradient-to-r from-primary to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4">My Account</h1>
            <p class="text-xl opacity-90">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
        </div>
    </section>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-circle text-indigo-600 text-4xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <p class="text-gray-600 text-sm">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                        <?php if (isAdmin()): ?>
                            <span class="inline-block mt-2 px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                <i class="fas fa-crown mr-1"></i> Administrator
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="#profile" onclick="showSection('profile')" class="block p-3 rounded-lg bg-indigo-50 text-indigo-600 font-semibold">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <a href="#orders" onclick="showSection('orders')" class="block p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                            <i class="fas fa-shopping-bag mr-2"></i>My Orders
                        </a>
                        <a href="#wishlist" onclick="showSection('wishlist')" class="block p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                            <i class="fas fa-heart mr-2"></i>Wishlist
                        </a>
                        <a href="#addresses" onclick="showSection('addresses')" class="block p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                            <i class="fas fa-map-marker-alt mr-2"></i>Addresses
                        </a>
                        
                        <!-- Admin Panel Button (only shown for admins) -->
                        <?php if (isAdmin()): ?>
                        <div class="border-t border-gray-200 pt-4 mt-2">
                            <a href="admin.php" class="block p-3 rounded-lg hover:bg-purple-50 text-purple-700 border-l-4 border-purple-500">
                                <i class="fas fa-cog mr-2"></i>Admin Panel
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <a href="logout.php" class="block p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Profile Section -->
                <div id="profile-section" class="section active">
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Profile Information</h2>
                        
                        <?php if ($success): ?>
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <p class="text-green-700"><?php echo $success; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                    <p class="text-red-700"><?php echo $error; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form action="account.php" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 mb-2">Full Name</label>
                                    <input type="text" name="name" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 mb-2">Email Address</label>
                                    <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    <p class="text-gray-500 text-sm mt-1">Email cannot be changed</p>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" name="phone"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 mb-2">Address</label>
                                    <textarea name="address" rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Password Change -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-gray-700 mb-2">Current Password</label>
                                        <input type="password" name="current_password"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 mb-2">New Password</label>
                                        <input type="password" name="new_password"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 mb-2">Confirm New Password</label>
                                        <input type="password" name="confirm_password"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                                    </div>
                                </div>
                                <p class="text-gray-500 text-sm mt-2">Leave blank to keep current password</p>
                            </div>
                            
                            <button type="submit" name="update_profile" 
                                    class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                                Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Orders Section -->
                <div id="orders-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Orders</h2>
                        
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-12">
                                <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-600 mb-4">You haven't placed any orders yet.</p>
                                <a href="products.php" class="text-primary hover:text-indigo-700 font-semibold">
                                    Start Shopping <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($orders as $order): ?>
                                    <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50">
                                        <div class="flex justify-between items-center mb-4">
                                            <div>
                                                <h3 class="font-semibold text-gray-800">Order #<?php echo $order['order_id']; ?></h3>
                                                <p class="text-gray-600 text-sm"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-lg">$<?php echo number_format($order['total'], 2); ?></p>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?php 
                                                    echo $order['status'] == 'delivered' ? 'bg-green-100 text-green-800' : 
                                                           ($order['status'] == 'shipped' ? 'bg-blue-100 text-blue-800' : 
                                                           ($order['status'] == 'processing' ? 'bg-yellow-100 text-yellow-800' : 
                                                           'bg-gray-100 text-gray-800')); 
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-600"><strong>Payment:</strong> <?php echo $order['payment_method'] == 'cash' ? 'Cash on Delivery' : 'Credit Card'; ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600"><strong>City:</strong> <?php echo ucfirst($order['city']); ?></p>
                                            </div>
                                            <div>
                                                <a href="order-tracker.php?order_id=<?php echo $order['order_id']; ?>" 
                                                   class="text-sm text-primary hover:text-indigo-700">
                                                    Track Order <i class="fas fa-truck ml-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end">
                                            <button onclick="viewOrderDetails('<?php echo $order['order_id']; ?>')" 
                                                    class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-8 text-center">
                                <a href="orders.php" class="text-primary hover:text-indigo-700 font-semibold">
                                    View All Orders <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Wishlist Section -->
                <div id="wishlist-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Wishlist</h2>
                        <div id="wishlist-content">
                            <!-- Wishlist items will be loaded via JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Addresses Section -->
                <div id="addresses-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Saved Addresses</h2>
                        <div id="addresses-content">
                            <!-- Addresses will be loaded via JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Admin Panel Section (only shown for admins) -->
                <?php if (isAdmin()): ?>
                <div id="admin-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Admin Panel</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <a href="admin.php" class="block bg-indigo-50 hover:bg-indigo-100 rounded-xl p-6 text-center transition">
                                <i class="fas fa-tachometer-alt text-indigo-600 text-3xl mb-4"></i>
                                <h3 class="font-semibold text-gray-800 mb-2">Dashboard</h3>
                                <p class="text-gray-600 text-sm">View site statistics</p>
                            </a>
                            <a href="admin-products.php" class="block bg-green-50 hover:bg-green-100 rounded-xl p-6 text-center transition">
                                <i class="fas fa-box text-green-600 text-3xl mb-4"></i>
                                <h3 class="font-semibold text-gray-800 mb-2">Manage Products</h3>
                                <p class="text-gray-600 text-sm">Add/edit products</p>
                            </a>
                            <a href="admin-orders.php" class="block bg-purple-50 hover:bg-purple-100 rounded-xl p-6 text-center transition">
                                <i class="fas fa-shopping-cart text-purple-600 text-3xl mb-4"></i>
                                <h3 class="font-semibold text-gray-800 mb-2">Manage Orders</h3>
                                <p class="text-gray-600 text-sm">Process & track orders</p>
                            </a>
                            <a href="admin-users.php" class="block bg-blue-50 hover:bg-blue-100 rounded-xl p-6 text-center transition">
                                <i class="fas fa-users text-blue-600 text-3xl mb-4"></i>
                                <h3 class="font-semibold text-gray-800 mb-2">Manage Users</h3>
                                <p class="text-gray-600 text-sm">View user accounts</p>
                            </a>
                            <a href="admin-settings.php" class="block bg-yellow-50 hover:bg-yellow-100 rounded-xl p-6 text-center transition">
                                <i class="fas fa-cogs text-yellow-600 text-3xl mb-4"></i>
                                <h3 class="font-semibold text-gray-800 mb-2">Site Settings</h3>
                                <p class="text-gray-600 text-sm">Configure site options</p>
                            </a>
                            <a href="admin-reports.php" class="block bg-red-50 hover:bg-red-100 rounded-xl p-6 text-center transition">
                                <i class="fas fa-chart-line text-red-600 text-3xl mb-4"></i>
                                <h3 class="font-semibold text-gray-800 mb-2">Reports</h3>
                                <p class="text-gray-600 text-sm">View sales reports</p>
                            </a>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2">Quick Actions</h4>
                                <ul class="space-y-2">
                                    <li><a href="admin.php?action=users" class="text-primary hover:text-indigo-700 text-sm">Manage Users</a></li>
                                    <li><a href="admin.php?action=reports" class="text-primary hover:text-indigo-700 text-sm">View Reports</a></li>
                                    <li><a href="admin.php?action=settings" class="text-primary hover:text-indigo-700 text-sm">Site Settings</a></li>
                                </ul>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2">Recent Activity</h4>
                                <p class="text-gray-600 text-sm">No recent activity</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2">System Status</h4>
                                <p class="text-green-600 text-sm">All systems operational</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
    // Show/hide sections
    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.section').forEach(section => {
            section.classList.remove('active');
            section.classList.add('hidden');
        });
        
        // Show selected section
        const selectedSection = document.getElementById(sectionId + '-section');
        if (selectedSection) {
            selectedSection.classList.remove('hidden');
            selectedSection.classList.add('active');
        }
        
        // Update active nav item
        document.querySelectorAll('nav a').forEach(link => {
            link.classList.remove('bg-indigo-50', 'text-indigo-600');
            link.classList.add('text-gray-700', 'hover:bg-gray-50');
            
            // Remove admin specific styles
            link.classList.remove('border-l-4', 'border-purple-500', 'text-purple-700', 'hover:bg-purple-50');
        });
        
        const activeLink = document.querySelector(`a[href="#${sectionId}"]`);
        if (activeLink) {
            activeLink.classList.remove('text-gray-700', 'hover:bg-gray-50');
            activeLink.classList.add('bg-indigo-50', 'text-indigo-600');
        }
    }

    // View order details
    function viewOrderDetails(orderId) {
        // This would typically fetch order details via AJAX
        alert('Order details for ' + orderId + ' would be shown here.');
    }

    // Load wishlist
    async function loadWishlist() {
        try {
            const response = await fetch('api/get_wishlist.php');
            const wishlist = await response.json();
            
            const container = document.getElementById('wishlist-content');
            if (wishlist.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-heart text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600 mb-4">Your wishlist is empty.</p>
                        <a href="products.php" class="text-primary hover:text-indigo-700 font-semibold">
                            Browse Products <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                `;
            } else {
                // Render wishlist items
                // Implementation depends on your API
            }
        } catch (error) {
            console.error('Error loading wishlist:', error);
        }
    }

    // Load saved addresses
    async function loadAddresses() {
        try {
            const response = await fetch('api/get_addresses.php');
            const addresses = await response.json();
            
            const container = document.getElementById('addresses-content');
            // Render addresses
            // Implementation depends on your API
        } catch (error) {
            console.error('Error loading addresses:', error);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Load initial data if needed
        if (document.getElementById('wishlist-section').classList.contains('active')) {
            loadWishlist();
        }
        if (document.getElementById('addresses-section').classList.contains('active')) {
            loadAddresses();
        }
    });
</script>

<style>
    .section {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .section.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
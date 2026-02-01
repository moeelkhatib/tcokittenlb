<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$current_page = 'admin-customers';
$pdo = getDBConnection();

// Get filter parameters
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT * FROM users WHERE is_admin = 0";
$params = [];

if ($search) {
    $query .= " AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Get customers
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll();

// Get total count for pagination
$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", explode("ORDER BY", $query)[0]);
$count_stmt = $pdo->prepare($count_query);
$count_params = array_slice($params, 0, -2);
$count_stmt->execute($count_params);
$total_customers = $count_stmt->fetch()['total'];
$total_pages = ceil($total_customers / $limit);

// Handle customer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_customer'])) {
        $customer_id = $_POST['customer_id'];
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
        if ($stmt->execute([$customer_id])) {
            $_SESSION['success_message'] = "Customer deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete customer.";
        }
        header("Location: admin-customers.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - Admin Dashboard</title>
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
                        <h1 class="text-2xl font-bold text-gray-800">Customer Management</h1>
                        <p class="text-gray-600">Manage customer accounts and information</p>
                    </div>
                    <div class="flex items-center space-x-4">
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
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Search Customers</h3>
                    <form method="GET" class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by name, email, or phone..." 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <a href="admin-customers.php" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-300">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                    </form>
                </div>

                <!-- Customer Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-indigo-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Customers</p>
                                <h3 class="text-2xl font-bold text-gray-800"><?php echo $total_customers; ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-green-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <?php
                                $stmt = $pdo->prepare("SELECT COUNT(DISTINCT customer_email) as active FROM orders");
                                $stmt->execute();
                                $active_customers = $stmt->fetch()['active'];
                                ?>
                                <p class="text-sm text-gray-500">Active Customers</p>
                                <h3 class="text-2xl font-bold text-gray-800"><?php echo $active_customers; ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <?php
                                $stmt = $pdo->prepare("SELECT COUNT(*) as new_today FROM users WHERE DATE(created_at) = CURDATE() AND is_admin = 0");
                                $stmt->execute();
                                $new_today = $stmt->fetch()['new_today'];
                                ?>
                                <p class="text-sm text-gray-500">New Today</p>
                                <h3 class="text-2xl font-bold text-gray-800"><?php echo $new_today; ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-line text-yellow-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <?php
                                $stmt = $pdo->prepare("SELECT COUNT(*) as new_week FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND is_admin = 0");
                                $stmt->execute();
                                $new_week = $stmt->fetch()['new_week'];
                                ?>
                                <p class="text-sm text-gray-500">New This Week</p>
                                <h3 class="text-2xl font-bold text-gray-800"><?php echo $new_week; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Customers List</h3>
                        <div class="flex space-x-3">
                            <button onclick="printCustomers()" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                            <button onclick="exportCustomers()" class="text-sm bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-md">
                                <i class="fas fa-download mr-2"></i>Export CSV
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Since</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($customers)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-user-friends text-3xl mb-3 text-gray-300"></i>
                                            <p>No customers found</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($customers as $customer): ?>
                                        <?php
                                        // Get customer order stats
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as order_count, SUM(total) as total_spent FROM orders WHERE customer_email = ?");
                                        $stmt->execute([$customer['email']]);
                                        $stats = $stmt->fetch();
                                        ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0">
                                                        <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user text-indigo-600"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($customer['full_name']); ?></div>
                                                        <div class="text-sm text-gray-500">ID: <?php echo $customer['id']; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($customer['email']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo date('M d, Y', strtotime($customer['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $stats['order_count'] > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                                    <?php echo $stats['order_count'] ?? 0; ?> orders
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                $<?php echo number_format($stats['total_spent'] ?? 0, 2); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button onclick="viewCustomerDetails(<?php echo $customer['id']; ?>)" 
                                                            class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button onclick="sendEmail('<?php echo htmlspecialchars($customer['email']); ?>')" 
                                                            class="text-blue-600 hover:text-blue-900" title="Send Email">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                    <button onclick="confirmDelete(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars(addslashes($customer['full_name'])); ?>')" 
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
                                Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $limit, $total_customers); ?> of <?php echo $total_customers; ?> results
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

    <!-- Customer Details Modal -->
    <div id="customerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Customer Details</h3>
                    <button onclick="closeCustomerModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="customerDetails">
                    <!-- Customer details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Send Email to Customer</h3>
                <form id="emailForm">
                    <input type="hidden" id="emailTo" name="email_to">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <input type="text" name="subject" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea name="message" rows="6" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEmailModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Send Email
                        </button>
                    </div>
                </form>
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
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Customer</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="customerName" class="font-semibold"></span>? This action cannot be undone.</p>
                <form id="deleteForm" method="POST">
                    <input type="hidden" name="customer_id" id="delete_customer_id">
                    <input type="hidden" name="delete_customer" value="1">
                    
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Customer details modal
        async function viewCustomerDetails(customerId) {
            try {
                const response = await fetch(`api/get_customer_details.php?id=${customerId}`);
                const customer = await response.json();
                
                const modalContent = `
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Personal Information</h4>
                            <div class="space-y-2">
                                <p><strong>Name:</strong> ${customer.full_name}</p>
                                <p><strong>Email:</strong> ${customer.email}</p>
                                <p><strong>Phone:</strong> ${customer.phone || 'N/A'}</p>
                                <p><strong>Member Since:</strong> ${new Date(customer.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Address</h4>
                            <p>${customer.address || 'No address provided'}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-700 mb-2">Order Statistics</h4>
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-sm text-gray-500">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-800">${customer.order_count || 0}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-sm text-gray-500">Total Spent</p>
                                <p class="text-2xl font-bold text-gray-800">$${(customer.total_spent || 0).toFixed(2)}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-sm text-gray-500">Average Order</p>
                                <p class="text-2xl font-bold text-gray-800">$${customer.order_count ? (customer.total_spent / customer.order_count).toFixed(2) : '0.00'}</p>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('customerDetails').innerHTML = modalContent;
                document.getElementById('customerModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error loading customer details:', error);
            }
        }

        function closeCustomerModal() {
            document.getElementById('customerModal').classList.add('hidden');
        }

        // Email modal
        function sendEmail(email) {
            document.getElementById('emailTo').value = email;
            document.getElementById('emailModal').classList.remove('hidden');
        }

        function closeEmailModal() {
            document.getElementById('emailModal').classList.add('hidden');
        }

        // Delete modal functions
        function confirmDelete(customerId, customerName) {
            document.getElementById('delete_customer_id').value = customerId;
            document.getElementById('customerName').textContent = customerName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Export function
        function exportCustomers() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = 'api/export_customers.php?' + params.toString();
        }

        // Print function
        function printCustomers() {
            window.print();
        }

        // Email form submission
        document.getElementById('emailForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/send_customer_email.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Email sent successfully!');
                    closeEmailModal();
                } else {
                    alert('Failed to send email: ' + result.message);
                }
            } catch (error) {
                console.error('Error sending email:', error);
                alert('An error occurred while sending the email.');
            }
        });

        // Close modals on outside click
        window.onclick = function(event) {
            const customerModal = document.getElementById('customerModal');
            const emailModal = document.getElementById('emailModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === customerModal) {
                closeCustomerModal();
            }
            if (event.target === emailModal) {
                closeEmailModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>
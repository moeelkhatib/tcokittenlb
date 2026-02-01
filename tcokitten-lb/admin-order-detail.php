<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$current_page = 'admin-orders';
$pdo = getDBConnection();

// Get order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header('Location: admin-orders.php');
    exit;
}

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.id) as item_count,
           GROUP_CONCAT(DISTINCT oi.product_name) as product_names
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ?
    GROUP BY o.id
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: admin-orders.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, notes = CONCAT(notes, '\n', ?), updated_at = NOW() WHERE id = ?");
    if ($stmt->execute([$new_status, "[Status changed to $new_status] " . $notes, $order_id])) {
        $_SESSION['success_message'] = "Order status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update order status.";
    }
    header("Location: admin-order-detail.php?id=" . $order_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin Dashboard</title>
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
                        <h1 class="text-2xl font-bold text-gray-800">Order Details</h1>
                        <p class="text-gray-600">Order #<?php echo $order['order_id']; ?></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="admin-orders.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
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

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Information -->
                    <div class="lg:col-span-2">
                        <!-- Order Summary -->
                        <div class="bg-white rounded-xl shadow p-6 mb-6">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800">Order Summary</h2>
                                    <p class="text-gray-600">Placed on <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full <?php 
                                    echo $order['status'] == 'delivered' ? 'bg-green-100 text-green-800' : 
                                           ($order['status'] == 'shipped' ? 'bg-blue-100 text-blue-800' : 
                                           ($order['status'] == 'processing' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($order['status'] == 'cancelled' ? 'bg-red-100 text-red-800' : 
                                           'bg-gray-100 text-gray-800'))); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="mb-6">
                                <h3 class="font-semibold text-gray-700 mb-4">Order Items (<?php echo $order['item_count']; ?>)</h3>
                                <div class="space-y-4">
                                    <?php foreach ($order_items as $item): ?>
                                        <div class="flex items-center border border-gray-200 rounded-lg p-4">
                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['product_name']; ?>" 
                                                 class="w-16 h-16 object-cover rounded-md mr-4">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-800"><?php echo $item['product_name']; ?></h4>
                                                <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                                                <p class="text-sm text-gray-600">Price: $<?php echo number_format($item['price'], 2); ?> each</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-gray-800">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Order Totals -->
                            <div class="border-t pt-6">
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="font-semibold">$<?php echo number_format($order['subtotal'], 2); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Shipping Fee</span>
                                        <span class="font-semibold">$<?php echo number_format($order['shipping_fee'], 2); ?></span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-gray-800">Total</span>
                                        <span class="text-indigo-600">$<?php echo number_format($order['total'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Customer Information -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Customer Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-700 mb-3">Contact Details</h4>
                                    <div class="space-y-2">
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-700 mb-3">Shipping Address</h4>
                                    <div class="space-y-2">
                                        <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                        <p><strong>City:</strong> <?php echo ucfirst($order['city']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Actions & Payment -->
                    <div class="space-y-6">
                        <!-- Status Update -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="font-semibold text-gray-800 mb-4">Update Status</h3>
                            <form method="POST">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                    <textarea name="notes" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                              placeholder="Add update notes..."></textarea>
                                </div>
                                
                                <button type="submit" name="update_status" 
                                        class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700">
                                    Update Status
                                </button>
                            </form>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="font-semibold text-gray-800 mb-4">Payment Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Method</span>
                                    <span class="font-semibold"><?php echo ucfirst($order['payment_method']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $order['payment_status'] == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Actions -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="font-semibold text-gray-800 mb-4">Order Actions</h3>
                            <div class="space-y-3">
                                <button onclick="printInvoice()" 
                                        class="w-full text-left p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-print mr-2 text-gray-600"></i>Print Invoice
                                </button>
                                <button onclick="resendConfirmation()" 
                                        class="w-full text-left p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-envelope mr-2 text-gray-600"></i>Resend Confirmation
                                </button>
                                <button onclick="trackShipment()" 
                                        class="w-full text-left p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-truck mr-2 text-gray-600"></i>Track Shipment
                                </button>
                                <a href="mailto:<?php echo htmlspecialchars($order['customer_email']); ?>" 
                                   class="block w-full text-left p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-comment mr-2 text-gray-600"></i>Contact Customer
                                </a>
                            </div>
                        </div>
                        
                        <!-- Order Notes -->
                        <?php if ($order['notes']): ?>
                            <div class="bg-white rounded-xl shadow p-6">
                                <h3 class="font-semibold text-gray-800 mb-4">Order Notes</h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($order['notes']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printInvoice() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Invoice - Order #<?php echo $order['order_id']; ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 40px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .info { margin-bottom: 20px; }
                        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .table th { background-color: #f2f2f2; }
                        .total { text-align: right; margin-top: 20px; }
                        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Take Care of Kitten</h1>
                        <p>Invoice for Order #<?php echo $order['order_id']; ?></p>
                        <p>Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                    </div>
                    
                    <div class="info">
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?>, <?php echo ucfirst($order['city']); ?></p>
                    </div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="total">
                        <p>Subtotal: $<?php echo number_format($order['subtotal'], 2); ?></p>
                        <p>Shipping: $<?php echo number_format($order['shipping_fee'], 2); ?></p>
                        <p><strong>Total: $<?php echo number_format($order['total'], 2); ?></strong></p>
                    </div>
                    
                    <div class="footer">
                        <p>Thank you for your business!</p>
                        <p>Take Care of Kitten Lebanon | <?php echo PHONE_NUMBER; ?> | <?php echo ADMIN_EMAIL; ?></p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        function resendConfirmation() {
            if (confirm('Resend order confirmation email to customer?')) {
                // In a real application, this would call an API endpoint
                alert('Confirmation email resent to ' + '<?php echo htmlspecialchars($order['customer_email']); ?>');
            }
        }

        function trackShipment() {
            // In a real application, this would open the shipping carrier's tracking page
            alert('This would open the shipping carrier tracking page.');
        }
    </script>
</body>
</html>
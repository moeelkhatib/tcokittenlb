<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$current_page = 'admin-analytics';
$pdo = getDBConnection();

// Get date range parameters
$period = $_GET['period'] ?? 'month';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get analytics data
$analytics = [];

// Total Revenue
$stmt = $pdo->prepare("SELECT SUM(total) as total_revenue FROM orders WHERE created_at BETWEEN ? AND ?");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$analytics['total_revenue'] = $stmt->fetch()['total_revenue'] ?? 0;

// Total Orders
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE created_at BETWEEN ? AND ?");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$analytics['total_orders'] = $stmt->fetch()['total_orders'] ?? 0;

// Average Order Value
$analytics['avg_order_value'] = $analytics['total_orders'] > 0 ? $analytics['total_revenue'] / $analytics['total_orders'] : 0;

// New Customers
$stmt = $pdo->prepare("SELECT COUNT(*) as new_customers FROM users WHERE created_at BETWEEN ? AND ? AND is_admin = 0");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$analytics['new_customers'] = $stmt->fetch()['new_customers'] ?? 0;

// Top Products
$stmt = $pdo->prepare("
    SELECT p.name, p.category, SUM(oi.quantity) as total_quantity, SUM(oi.quantity * oi.price) as total_revenue
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    JOIN products p ON p.id = oi.product_id
    WHERE o.created_at BETWEEN ? AND ?
    GROUP BY oi.product_id
    ORDER BY total_quantity DESC
    LIMIT 10
");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$analytics['top_products'] = $stmt->fetchAll();

// Sales by Category
$stmt = $pdo->prepare("
    SELECT p.category, SUM(oi.quantity * oi.price) as revenue, SUM(oi.quantity) as quantity
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    JOIN products p ON p.id = oi.product_id
    WHERE o.created_at BETWEEN ? AND ?
    GROUP BY p.category
    ORDER BY revenue DESC
");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$analytics['sales_by_category'] = $stmt->fetchAll();

// Monthly Sales Data (for chart)
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
           COUNT(*) as order_count, 
           SUM(total) as revenue
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month
");
$stmt->execute();
$monthly_sales = $stmt->fetchAll();

// Popular Payment Methods
$stmt = $pdo->prepare("
    SELECT payment_method, COUNT(*) as count, SUM(total) as revenue
    FROM orders 
    WHERE created_at BETWEEN ? AND ?
    GROUP BY payment_method
");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$analytics['payment_methods'] = $stmt->fetchAll();

// Order Status Distribution
$stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count
    FROM orders 
    WHERE created_at BETWEEN ? AND ?
    GROUP BY status
");
$stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
$analytics['order_status'] = $stmt->fetchAll();

// Customer Acquisition Source (mock data - in real app, this would come from tracking)
$acquisition_data = [
    ['source' => 'Direct', 'customers' => 45, 'percentage' => 30],
    ['source' => 'Social Media', 'customers' => 35, 'percentage' => 23],
    ['source' => 'Search Engines', 'customers' => 40, 'percentage' => 27],
    ['source' => 'Email Marketing', 'customers' => 30, 'percentage' => 20]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <h1 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h1>
                        <p class="text-gray-600">Business insights and performance metrics</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <form method="GET" class="flex items-center space-x-3">
                            <select name="period" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md">
                                <option value="today" <?php echo $period == 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="week" <?php echo $period == 'week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="month" <?php echo $period == 'month' ? 'selected' : ''; ?>>This Month</option>
                                <option value="quarter" <?php echo $period == 'quarter' ? 'selected' : ''; ?>>This Quarter</option>
                                <option value="year" <?php echo $period == 'year' ? 'selected' : ''; ?>>This Year</option>
                                <option value="custom" <?php echo $period == 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                            </select>
                            
                            <?php if ($period == 'custom'): ?>
                                <input type="date" name="start_date" value="<?php echo $start_date; ?>" 
                                       class="px-3 py-2 border border-gray-300 rounded-md">
                                <input type="date" name="end_date" value="<?php echo $end_date; ?>" 
                                       class="px-3 py-2 border border-gray-300 rounded-md">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                                    Apply
                                </button>
                            <?php endif; ?>
                        </form>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <span class="font-medium"><?php echo $_SESSION['user_name'] ?? 'Administrator'; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Range Info -->
            <div class="p-6">
                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-indigo-800">Analytics Period</h3>
                            <p class="text-indigo-600">
                                <?php 
                                if ($period == 'today') {
                                    echo date('F j, Y');
                                } elseif ($period == 'week') {
                                    echo 'Week of ' . date('F j', strtotime('monday this week'));
                                } elseif ($period == 'month') {
                                    echo date('F Y');
                                } elseif ($period == 'quarter') {
                                    echo 'Q' . ceil(date('n')/3) . ' ' . date('Y');
                                } elseif ($period == 'year') {
                                    echo date('Y');
                                } else {
                                    echo date('F j, Y', strtotime($start_date)) . ' - ' . date('F j, Y', strtotime($end_date));
                                }
                                ?>
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="exportAnalytics()" class="text-sm bg-white text-indigo-600 border border-indigo-300 px-4 py-2 rounded-md hover:bg-indigo-50">
                                <i class="fas fa-download mr-2"></i>Export Report
                            </button>
                            <button onclick="printAnalytics()" class="text-sm bg-white text-indigo-600 border border-indigo-300 px-4 py-2 rounded-md hover:bg-indigo-50">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500">Total Revenue</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2">$<?php echo number_format($analytics['total_revenue'], 2); ?></h3>
                                <p class="text-sm <?php echo $analytics['total_revenue'] > 0 ? 'text-green-600' : 'text-gray-500'; ?> mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <?php echo $analytics['total_revenue'] > 0 ? 'Growth' : 'No data'; ?>
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500">Total Orders</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2"><?php echo $analytics['total_orders']; ?></h3>
                                <p class="text-sm <?php echo $analytics['total_orders'] > 0 ? 'text-blue-600' : 'text-gray-500'; ?> mt-1">
                                    <i class="fas fa-shopping-bag mr-1"></i>
                                    <?php echo $analytics['total_orders']; ?> completed
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500">Avg. Order Value</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2">$<?php echo number_format($analytics['avg_order_value'], 2); ?></h3>
                                <p class="text-sm <?php echo $analytics['avg_order_value'] > 0 ? 'text-purple-600' : 'text-gray-500'; ?> mt-1">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    <?php echo $analytics['avg_order_value'] > 0 ? 'Per order' : 'No data'; ?>
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-chart-bar text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500">New Customers</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2"><?php echo $analytics['new_customers']; ?></h3>
                                <p class="text-sm <?php echo $analytics['new_customers'] > 0 ? 'text-yellow-600' : 'text-gray-500'; ?> mt-1">
                                    <i class="fas fa-users mr-1"></i>
                                    <?php echo $analytics['new_customers'] > 0 ? 'Acquired' : 'No new'; ?>
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Sales Chart -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Sales Trend</h3>
                        <canvas id="salesChart" class="h-64"></canvas>
                    </div>
                    
                    <!-- Category Distribution -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Sales by Category</h3>
                        <canvas id="categoryChart" class="h-64"></canvas>
                    </div>
                </div>

                <!-- Top Products & Order Status -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Top Products -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Top Selling Products</h3>
                            <span class="text-sm text-gray-500">Top 10</span>
                        </div>
                        <div class="space-y-4">
                            <?php if (empty($analytics['top_products'])): ?>
                                <p class="text-gray-500 text-center py-4">No sales data available</p>
                            <?php else: ?>
                                <?php foreach ($analytics['top_products'] as $index => $product): ?>
                                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <span class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3 text-sm font-semibold">
                                                <?php echo $index + 1; ?>
                                            </span>
                                            <div>
                                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($product['name']); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo ucfirst($product['category']); ?></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800"><?php echo $product['total_quantity']; ?> sold</p>
                                            <p class="text-sm text-green-600">$<?php echo number_format($product['total_revenue'], 2); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Order Status Distribution -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Status Distribution</h3>
                        <div class="space-y-4">
                            <?php if (empty($analytics['order_status'])): ?>
                                <p class="text-gray-500 text-center py-4">No order data available</p>
                            <?php else: ?>
                                <?php foreach ($analytics['order_status'] as $status): ?>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 rounded-full mr-3 <?php 
                                                echo $status['status'] == 'delivered' ? 'bg-green-500' : 
                                                       ($status['status'] == 'shipped' ? 'bg-blue-500' : 
                                                       ($status['status'] == 'processing' ? 'bg-yellow-500' : 
                                                       ($status['status'] == 'cancelled' ? 'bg-red-500' : 
                                                       'bg-gray-500'))); 
                                            ?>"></span>
                                            <span class="font-medium text-gray-700"><?php echo ucfirst($status['status']); ?></span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="font-semibold text-gray-800 mr-2"><?php echo $status['count']; ?></span>
                                            <span class="text-sm text-gray-500">orders</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="mt-8">
                            <h4 class="font-semibold text-gray-700 mb-4">Payment Methods</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <?php foreach ($analytics['payment_methods'] as $method): ?>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="font-medium text-gray-800"><?php echo ucfirst($method['payment_method']); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo $method['count']; ?> orders</p>
                                        <p class="text-lg font-bold text-green-600">$<?php echo number_format($method['revenue'], 2); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Acquisition & Additional Metrics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Acquisition -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Acquisition</h3>
                        <div class="space-y-4">
                            <?php foreach ($acquisition_data as $source): ?>
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="font-medium text-gray-700"><?php echo $source['source']; ?></span>
                                        <span class="text-sm text-gray-600"><?php echo $source['customers']; ?> customers</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo $source['percentage']; ?>%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo $source['percentage']; ?>% of total</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Additional Metrics -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Metrics</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-undo-alt text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Conversion Rate</p>
                                        <p class="text-2xl font-bold text-gray-800">4.2%</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Visitors to buyers</p>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-shopping-cart text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Cart Abandonment</p>
                                        <p class="text-2xl font-bold text-gray-800">32%</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Of initiated checkouts</p>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user-check text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Customer Retention</p>
                                        <p class="text-2xl font-bold text-gray-800">68%</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Repeat customers</p>
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-star text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Avg. Rating</p>
                                        <p class="text-2xl font-bold text-gray-800">4.7</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Product reviews</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_sales, 'month')); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_column($monthly_sales, 'revenue')); ?>,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Orders',
                    data: <?php echo json_encode(array_column($monthly_sales, 'order_count')); ?>,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return this.chart.data.datasets[0].label === 'Revenue' ? '$' + value : value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($analytics['sales_by_category'], 'category')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($analytics['sales_by_category'], 'revenue')); ?>,
                    backgroundColor: [
                        '#4F46E5',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#06B6D4'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Export function
        function exportAnalytics() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = 'api/export_analytics.php?' + params.toString();
        }

        // Print function
        function printAnalytics() {
            window.print();
        }
    </script>
</body>
</html>
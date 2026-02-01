<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$current_page = 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Take Care of Kitten</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Include Sidebar Component -->
        <?php include 'admin-sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                        <p class="text-gray-600">Welcome back, Admin</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <i class="fas fa-bell text-gray-600 text-xl"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <span class="font-medium">Administrator</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500">Total Revenue</p>
                                <h3 id="total-revenue" class="text-3xl font-bold mt-2">$0.00</h3>
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
                                <h3 id="total-orders" class="text-3xl font-bold mt-2">0</h3>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500">Products</p>
                                <h3 id="total-products" class="text-3xl font-bold mt-2">0</h3>
                            </div>
                            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-box text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-500">Pending Orders</p>
                                <h3 id="pending-orders" class="text-3xl font-bold mt-2">0</h3>
                            </div>
                            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Recent Orders</h2>
                        <a href="admin-orders.php" class="text-indigo-600 hover:text-indigo-800">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div id="recent-orders" class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-3 px-4">Order ID</th>
                                    <th class="text-left py-3 px-4">Customer</th>
                                    <th class="text-left py-3 px-4">Date</th>
                                    <th class="text-left py-3 px-4">Total</th>
                                    <th class="text-left py-3 px-4">Status</th>
                                    <th class="text-left py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table-body">
                                <!-- Orders will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button onclick="window.location.href='admin-products.php?action=add'" 
                                    class="w-full text-left p-4 border border-dashed border-gray-300 rounded-lg hover:border-indigo-600 hover:bg-indigo-50 transition-colors">
                                <i class="fas fa-plus text-indigo-600 mr-3"></i>
                                Add New Product
                            </button>
                            <button onclick="window.location.href='admin-orders.php'" 
                                    class="w-full text-left p-4 border border-dashed border-gray-300 rounded-lg hover:border-indigo-600 hover:bg-indigo-50 transition-colors">
                                <i class="fas fa-eye text-indigo-600 mr-3"></i>
                                View All Orders
                            </button>
                            <button onclick="generateReport()" 
                                    class="w-full text-left p-4 border border-dashed border-gray-300 rounded-lg hover:border-indigo-600 hover:bg-indigo-50 transition-colors">
                                <i class="fas fa-download text-indigo-600 mr-3"></i>
                                Generate Sales Report
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-bold mb-4">Sales Overview</h3>
                        <div id="sales-chart" class="h-64">
                            <!-- Chart will be loaded here -->
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function loadDashboard() {
            try {
                const response = await fetch('api/admin_stats.php');
                const stats = await response.json();
                
                // Update stats
                document.getElementById('total-revenue').textContent = `$${stats.totalRevenue.toFixed(2)}`;
                document.getElementById('total-orders').textContent = stats.totalOrders;
                document.getElementById('total-products').textContent = stats.totalProducts;
                document.getElementById('pending-orders').textContent = stats.pendingOrders;
                
                // Load recent orders
                loadRecentOrders(stats.recentOrders);
                
                // Load chart
                loadSalesChart(stats.monthlyRevenue);
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }

        function loadRecentOrders(orders) {
            const tbody = document.getElementById('orders-table-body');
            tbody.innerHTML = orders.map(order => `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <a href="admin-orders.php?order=${order.orderId}" class="text-indigo-600 hover:underline">
                            ${order.orderId}
                        </a>
                    </td>
                    <td class="py-3 px-4">${order.customerName}</td>
                    <td class="py-3 px-4">${new Date(order.createdAt).toLocaleDateString()}</td>
                    <td class="py-3 px-4 font-semibold">$${order.total.toFixed(2)}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${getStatusClass(order.status)}">
                            ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <button onclick="updateOrderStatus('${order.orderId}')" 
                                class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded mr-2">
                            Update
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function getStatusClass(status) {
            switch(status) {
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                case 'processing': return 'bg-blue-100 text-blue-800';
                case 'shipped': return 'bg-purple-100 text-purple-800';
                case 'delivered': return 'bg-green-100 text-green-800';
                case 'cancelled': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        let salesChart = null;
        function loadSalesChart(monthlyRevenue) {
            const ctx = document.getElementById('monthlySalesChart').getContext('2d');
            
            if (salesChart) {
                salesChart.destroy();
            }
            
            const labels = monthlyRevenue.map(item => 
                `${new Date(2000, item._id.month - 1).toLocaleString('default', { month: 'short' })} ${item._id.year}`
            );
            const data = monthlyRevenue.map(item => item.total);
            
            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: data,
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
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
                                    return '$' + value;
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
        }

        async function updateOrderStatus(orderId) {
            const status = prompt('Enter new status (pending, processing, shipped, delivered, cancelled):');
            if (!status) return;
            
            try {
                await fetch('api/update_order_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orderId, status })
                });
                alert('Order status updated!');
                loadDashboard();
            } catch (error) {
                alert('Error updating order status');
            }
        }

        function generateReport() {
            alert('Sales report generation would be implemented here.');
        }

        // Load dashboard on page load
        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
</body>
</html>
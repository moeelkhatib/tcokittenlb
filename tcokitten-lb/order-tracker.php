
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'tracker';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Take Care of Kitten</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'includes/header.php'; ?>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Track Your Order</h1>
                <p class="text-gray-600">Enter your order ID and email to track your package</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 mb-2">Order ID</label>
                        <input type="text" id="order-id" placeholder="e.g., ORD123456789"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="order-email" placeholder="your@email.com"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600">
                    </div>
                </div>
                <button onclick="trackOrder()" 
                        class="w-full md:w-auto bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                    <i class="fas fa-search mr-2"></i>Track Order
                </button>
            </div>
            
            <div id="tracking-result" class="hidden">
                <!-- Tracking results will be shown here -->
            </div>
            
            <div id="order-history" class="hidden">
                <!-- Order history will be shown here -->
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        async function trackOrder() {
            const orderId = document.getElementById('order-id').value.trim();
            const email = document.getElementById('order-email').value.trim();
            
            if (!orderId || !email) {
                alert('Please enter both order ID and email');
                return;
            }
            
            try {
                // Fetch order details
                const orderResponse = await fetch(`api/get_order.php?order_id=${orderId}`);
                const order = await orderResponse.json();
                
                // Verify email matches
                if (order.customerEmail.toLowerCase() !== email.toLowerCase()) {
                    alert('Order not found with this email');
                    return;
                }
                
                displayOrderTracking(order);
                
                // Load order history
                const historyResponse = await fetch(`api/get_orders_by_email.php?email=${email}`);
                const history = await historyResponse.json();
                displayOrderHistory(history);
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tracking-result').innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-red-800 mb-2">Order Not Found</h3>
                        <p class="text-red-600">Please check your order ID and email address</p>
                    </div>
                `;
                document.getElementById('tracking-result').classList.remove('hidden');
            }
        }

        function displayOrderTracking(order) {
            const statusSteps = [
                { id: 'pending', label: 'Order Placed', icon: 'fa-shopping-cart' },
                { id: 'processing', label: 'Processing', icon: 'fa-cog' },
                { id: 'shipped', label: 'Shipped', icon: 'fa-shipping-fast' },
                { id: 'delivered', label: 'Delivered', icon: 'fa-check-circle' }
            ];
            
            const currentStatusIndex = statusSteps.findIndex(step => step.id === order.status);
            
            const html = `
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Order #${order.orderId}</h2>
                            <p class="text-gray-600">Placed on ${new Date(order.createdAt).toLocaleDateString()}</p>
                        </div>
                        <span class="px-4 py-2 rounded-full text-lg font-semibold ${getStatusClass(order.status)}">
                            ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                        </span>
                    </div>
                    
                    <!-- Order Timeline -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-6">Order Timeline</h3>
                        <div class="flex justify-between items-center relative">
                            <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-200 -translate-y-1/2"></div>
                            <div class="absolute top-1/2 left-0 h-1 bg-indigo-600 -translate-y-1/2" 
                                 style="width: ${(currentStatusIndex / (statusSteps.length - 1)) * 100}%"></div>
                            
                            ${statusSteps.map((step, index) => `
                                <div class="relative z-10 text-center">
                                    <div class="w-12 h-12 rounded-full ${index <= currentStatusIndex ? 'bg-indigo-600' : 'bg-gray-200'} 
                                         flex items-center justify-center mb-2 mx-auto">
                                        <i class="fas ${step.icon} text-white"></i>
                                    </div>
                                    <p class="text-sm font-medium ${index <= currentStatusIndex ? 'text-indigo-600' : 'text-gray-500'}">
                                        ${step.label}
                                    </p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <!-- Order Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-4">Shipping Information</h4>
                            <div class="space-y-2">
                                <p><strong>Name:</strong> ${order.customerName}</p>
                                <p><strong>Email:</strong> ${order.customerEmail}</p>
                                <p><strong>Phone:</strong> ${order.customerPhone}</p>
                                <p><strong>Address:</strong> ${order.shippingAddress}, ${order.city}</p>
                                ${order.notes ? `<p><strong>Notes:</strong> ${order.notes}</p>` : ''}
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-4">Order Summary</h4>
                            <div class="space-y-2">
                                <p><strong>Payment Method:</strong> ${order.paymentMethod === 'cash' ? 'Cash on Delivery' : 'Credit Card'}</p>
                                <p><strong>Payment Status:</strong> ${order.paymentStatus}</p>
                                <p><strong>Shipping Fee:</strong> $${order.shippingFee.toFixed(2)}</p>
                                <p><strong>Subtotal:</strong> $${order.subtotal.toFixed(2)}</p>
                                <p class="text-lg font-bold"><strong>Total:</strong> $${order.total.toFixed(2)}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="mt-8">
                        <h4 class="font-semibold text-gray-800 mb-4">Order Items</h4>
                        <div class="space-y-4">
                            ${order.items.map(item => `
                                <div class="flex items-center border border-gray-200 rounded-lg p-4">
                                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded mr-4">
                                    <div class="flex-1">
                                        <h5 class="font-medium">${item.name}</h5>
                                        <p class="text-gray-600 text-sm">Quantity: ${item.quantity}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">$${item.price.toFixed(2)} each</p>
                                        <p class="text-gray-600">$${(item.price * item.quantity).toFixed(2)} total</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <!-- Support Info -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2">Need Help?</h4>
                        <p class="text-gray-600 mb-2">If you have any questions about your order, contact our support team:</p>
                        <p class="text-gray-600"><i class="fas fa-phone mr-2"></i> <?php echo PHONE_NUMBER; ?></p>
                        <p class="text-gray-600"><i class="fas fa-envelope mr-2"></i> <?php echo ADMIN_EMAIL; ?></p>
                    </div>
                </div>
            `;
            
            document.getElementById('tracking-result').innerHTML = html;
            document.getElementById('tracking-result').classList.remove('hidden');
        }

        function displayOrderHistory(orders) {
            if (orders.length === 0) return;
            
            const html = `
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Your Order History</h3>
                    <div class="space-y-4">
                        ${orders.map(order => `
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-semibold">Order #${order.orderId}</h4>
                                        <p class="text-gray-600 text-sm">${new Date(order.createdAt).toLocaleDateString()}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg">$${order.total.toFixed(2)}</p>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${getStatusClass(order.status)}">
                                            ${order.status}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button onclick="viewOrder('${order.orderId}')" 
                                            class="text-sm text-indigo-600 hover:text-indigo-800">
                                        View Details <i class="fas fa-arrow-right ml-1"></i>
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            
            document.getElementById('order-history').innerHTML = html;
            document.getElementById('order-history').classList.remove('hidden');
        }

        function viewOrder(orderId) {
            // This would open the order in a modal or new page
            alert(`Viewing order ${orderId}. This would show detailed view.`);
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
    </script>
</body>
</html>
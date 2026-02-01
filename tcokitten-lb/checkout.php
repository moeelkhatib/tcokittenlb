<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$current_page = 'checkout';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Take Care of Kitten Lebanon</title>
    <meta name="description" content="Secure checkout for your cat products">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }
        .step-indicator.active {
            background-color: #4F46E5;
            color: white;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-poppins">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Progress Steps -->
            <div class="mb-12">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="step-indicator w-10 h-10 rounded-full border-2 border-indigo-600 flex items-center justify-center font-semibold text-indigo-600 active">
                            1
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Step 1</p>
                            <p class="font-semibold">Shipping Details</p>
                        </div>
                    </div>
                    <div class="h-1 flex-1 mx-4 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="step-indicator w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center font-semibold text-gray-500">
                            2
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Step 2</p>
                            <p class="font-semibold">Payment</p>
                        </div>
                    </div>
                    <div class="h-1 flex-1 mx-4 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="step-indicator w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center font-semibold text-gray-500">
                            3
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Step 3</p>
                            <p class="font-semibold">Confirmation</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Summary -->
                <div class="lg:col-span-2">
                    <div id="step1" class="form-step active">
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Shipping Information</h2>
                            <form id="shipping-form">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-gray-700 mb-2">Full Name *</label>
                                        <input type="text" name="fullName" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 mb-2">Email Address *</label>
                                        <input type="email" name="email" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 mb-2">Phone Number *</label>
                                        <input type="tel" name="phone" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 mb-2">City *</label>
                                        <select name="city" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                                            <option value="">Select City</option>
                                            <option value="beirut">Beirut</option>
                                            <option value="tripoli">Tripoli</option>
                                            <option value="saidon">Saidon</option>
                                            <option value="tyre">Tyre</option>
                                            <option value="byblos">Byblos</option>
                                            <option value="zahlé">Zahlé</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-gray-700 mb-2">Shipping Address *</label>
                                        <textarea name="address" required rows="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent"></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-gray-700 mb-2">Delivery Notes (Optional)</label>
                                        <textarea name="notes" rows="2"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                                            placeholder="Any special instructions for delivery..."></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="step2" class="form-step">
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Payment Method</h2>
                            <div class="space-y-4">
                                <div class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-600 payment-method">
                                    <input type="radio" name="payment" value="cash" class="mr-4" checked>
                                    <div>
                                        <h3 class="font-semibold">Cash on Delivery</h3>
                                        <p class="text-gray-600 text-sm">Pay when you receive your order</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-600 payment-method">
                                    <input type="radio" name="payment" value="card" class="mr-4">
                                    <div>
                                        <h3 class="font-semibold">Credit/Debit Card</h3>
                                        <p class="text-gray-600 text-sm">Pay securely with your card</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="step3" class="form-step">
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6 text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-check text-green-600 text-3xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Order Confirmed!</h2>
                            <p class="text-gray-600 mb-6">Thank you for your purchase. Your order has been received.</p>
                            <div id="order-details" class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <!-- Order details will be inserted here -->
                            </div>
                            <div class="space-x-4">
                                <a href="index.php" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                                    Continue Shopping
                                </a>
                                <button onclick="printOrder()" class="inline-block border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50">
                                    <i class="fas fa-print mr-2"></i>Print Receipt
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button id="prev-btn" class="hidden bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300">
                            <i class="fas fa-arrow-left mr-2"></i>Previous
                        </button>
                        <button id="next-btn" class="ml-auto bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                            Continue to Payment <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>
                        
                        <div id="order-items" class="mb-6">
                            <!-- Cart items will be dynamically inserted -->
                        </div>
                        
                        <div class="space-y-3 border-t border-b py-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span id="subtotal" class="font-semibold">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping Fee</span>
                                <span id="shipping-fee" class="font-semibold">$5.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span id="tax" class="font-semibold">$0.00</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mt-6">
                            <span class="text-lg font-bold text-gray-800">Total</span>
                            <span id="order-total" class="text-2xl font-bold text-indigo-600">$0.00</span>
                        </div>
                        
                        <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                            <div class="flex items-center text-yellow-800 mb-2">
                                <i class="fas fa-shipping-fast mr-2"></i>
                                <span class="font-semibold">Delivery Estimate</span>
                            </div>
                            <p class="text-sm text-yellow-700">2-4 business days within Beirut</p>
                            <p class="text-sm text-yellow-700">5-7 business days outside Beirut</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Load cart from session (via PHP)
        let cart = <?php echo isset($_SESSION['cart']) ? json_encode($_SESSION['cart']) : '[]'; ?>;
        let currentStep = 1;
        let orderData = {};

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateOrderSummary();
            setupStepNavigation();
        });

        function updateOrderSummary() {
            const orderItems = document.getElementById('order-items');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('order-total');
            
            if (cart.length === 0) {
                orderItems.innerHTML = `
                    <div class="text-center py-6">
                        <p class="text-gray-500">Your cart is empty</p>
                        <a href="index.php" class="text-indigo-600 hover:underline mt-2 inline-block">
                            Continue Shopping
                        </a>
                    </div>
                `;
                subtotalElement.textContent = '$0.00';
                totalElement.textContent = '$0.00';
                return;
            }
            
            let itemsHTML = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                itemsHTML += `
                    <div class="flex items-center mb-4 pb-4 border-b">
                        <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg">
                        <div class="ml-4 flex-1">
                            <h4 class="font-medium text-gray-800">${item.name}</h4>
                            <p class="text-sm text-gray-600">Qty: ${item.quantity}</p>
                        </div>
                        <span class="font-semibold">$${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            });
            
            orderItems.innerHTML = itemsHTML;
            const shippingFee = 5.00;
            const total = subtotal + shippingFee;
            
            subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
            totalElement.textContent = `$${total.toFixed(2)}`;
            
            // Update order data
            orderData.items = cart.map(item => ({
                productId: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity,
                image: item.image
            }));
            orderData.subtotal = subtotal;
            orderData.shippingFee = shippingFee;
            orderData.total = total;
        }

        function setupStepNavigation() {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const steps = document.querySelectorAll('.form-step');
            const indicators = document.querySelectorAll('.step-indicator');
            
            function updateStep() {
                steps.forEach((step, index) => {
                    step.classList.toggle('active', index + 1 === currentStep);
                });
                
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('active', index + 1 <= currentStep);
                    if (index + 1 < currentStep) {
                        indicator.classList.remove('border-gray-300', 'text-gray-500');
                        indicator.classList.add('border-indigo-600', 'text-indigo-600');
                    }
                });
                
                prevBtn.classList.toggle('hidden', currentStep === 1);
                nextBtn.classList.toggle('hidden', currentStep === 3);
                
                if (currentStep === 2) {
                    nextBtn.innerHTML = 'Place Order <i class="fas fa-check ml-2"></i>';
                } else if (currentStep === 1) {
                    nextBtn.innerHTML = 'Continue to Payment <i class="fas fa-arrow-right ml-2"></i>';
                } else {
                    nextBtn.classList.add('hidden');
                }
            }
            
            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    updateStep();
                }
            });
            
            nextBtn.addEventListener('click', async () => {
                if (currentStep === 1) {
                    // Validate shipping form
                    const form = document.getElementById('shipping-form');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    
                    // Collect shipping data
                    const formData = new FormData(form);
                    orderData.customerName = formData.get('fullName');
                    orderData.customerEmail = formData.get('email');
                    orderData.customerPhone = formData.get('phone');
                    orderData.shippingAddress = formData.get('address');
                    orderData.city = formData.get('city');
                    orderData.notes = formData.get('notes');
                    
                    currentStep = 2;
                } else if (currentStep === 2) {
                    // Get payment method
                    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
                    orderData.paymentMethod = paymentMethod;
                    
                    // Submit order
                    try {
                        const response = await fetch('api/orders.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(orderData)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Clear cart
                            fetch('api/cart_clear.php', { method: 'POST' });
                            cart = [];
                            
                            // Show confirmation
                            currentStep = 3;
                            showOrderConfirmation(result.order);
                        } else {
                            alert('Failed to place order. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    }
                }
                
                updateStep();
            });
            
            updateStep();
        }

        function showOrderConfirmation(order) {
            const orderDetails = document.getElementById('order-details');
            orderDetails.innerHTML = `
                <div class="text-left">
                    <p class="mb-2"><strong>Order ID:</strong> ${order.orderId}</p>
                    <p class="mb-2"><strong>Date:</strong> ${new Date(order.createdAt).toLocaleDateString()}</p>
                    <p class="mb-2"><strong>Total:</strong> $${order.total.toFixed(2)}</p>
                    <p class="mb-2"><strong>Payment:</strong> ${order.paymentMethod === 'cash' ? 'Cash on Delivery' : 'Credit Card'}</p>
                    <p class="mb-2"><strong>Status:</strong> <span class="text-yellow-600 font-semibold">${order.status}</span></p>
                </div>
            `;
        }

        function printOrder() {
            window.print();
        }
    </script>
</body>
</html>
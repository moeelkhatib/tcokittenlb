
<?php
require_once 'includes/config.php';

$cartItems = getCartItems();
?>

<?php if (empty($cartItems)): ?>
    <div class="text-center py-12">
        <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
        <p class="text-gray-500">Your cart is empty</p>
        <a href="products.php" class="mt-4 text-primary hover:underline inline-block">
            Start Shopping
        </a>
    </div>
<?php else: ?>
    <?php foreach ($cartItems as $item): ?>
        <div class="flex items-center space-x-4 mb-6 pb-6 border-b">
            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-20 h-20 object-cover rounded-lg">
            <div class="flex-1">
                <h4 class="font-semibold text-dark"><?php echo $item['name']; ?></h4>
                <p class="text-gray-600 text-sm">$<?php echo number_format($item['price'], 2); ?> each</p>
                <div class="flex items-center space-x-2 mt-2">
                    <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50"
                            <?php if ($item['quantity'] <= 1): ?>disabled<?php endif; ?>>
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="w-10 text-center"><?php echo $item['quantity']; ?></span>
                    <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)" 
                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>
            <div class="text-right">
                <p class="font-semibold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="text-red-500 hover:text-red-700 text-sm mt-2">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php
require_once 'includes/config.php';
require_once 'includes/cart-functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if ($productId > 0) {
        if (removeFromCart($productId)) {
            // Sync with database if user is logged in
            if (isLoggedIn()) {
                syncCartWithDatabase();
            }
            
            echo json_encode([
                'success' => true,
                'count' => getCartCount(),
                'total' => getCartTotal(),
                'message' => 'Product removed from cart successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found in cart.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
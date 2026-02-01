
<?php
require_once 'includes/config.php';
require_once 'includes/cart-functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($productId > 0 && $quantity > 0) {
        if (addToCart($productId, $quantity)) {
            // Sync with database if user is logged in
            if (isLoggedIn()) {
                syncCartWithDatabase();
            }
            
            echo json_encode([
                'success' => true,
                'count' => getCartCount(),
                'message' => 'Product added to cart successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add product to cart. Product may be out of stock.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product or quantity.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
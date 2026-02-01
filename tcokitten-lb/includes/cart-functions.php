
<?php
// ========== CART FUNCTIONS ==========

// Initialize cart in session if not exists
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

// Get cart count from session
function getCartCount() {
    initCart();
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

// Get cart total
function getCartTotal() {
    initCart();
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Get cart items
function getCartItems() {
    initCart();
    return $_SESSION['cart'];
}

// Add item to cart
function addToCart($productId, $quantity = 1) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, name, price, image, stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        return false;
    }
    
    // Check stock
    if ($product['stock'] < $quantity) {
        return false;
    }
    
    initCart();
    
    // Check if product already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            $newQuantity = $item['quantity'] + $quantity;
            if ($product['stock'] >= $newQuantity) {
                $item['quantity'] = $newQuantity;
                $found = true;
                break;
            }
        }
    }
    
    // If not found, add new item
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity
        ];
    }
    
    return true;
}

// Remove item from cart
function removeFromCart($productId) {
    initCart();
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $productId) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            return true;
        }
    }
    return false;
}

// Update cart quantity
function updateCartQuantity($productId, $quantity) {
    if ($quantity <= 0) {
        return removeFromCart($productId);
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product || $product['stock'] < $quantity) {
        return false;
    }
    
    initCart();
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            $item['quantity'] = $quantity;
            return true;
        }
    }
    return false;
}

// Clear cart
function clearCart() {
    $_SESSION['cart'] = [];
}

// Sync cart with database for logged in users
function syncCartWithDatabase() {
    if (isLoggedIn()) {
        $pdo = getDBConnection();
        
        // Clear existing cart in database
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Insert current cart items
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $item['id'], $item['quantity']]);
        }
    }
}

// Load cart from database for logged in users
function loadCartFromDatabase() {
    if (isLoggedIn()) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT c.product_id as id, c.quantity, p.name, p.price, p.image 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cartItems = $stmt->fetchAll();
        
        $_SESSION['cart'] = [];
        foreach ($cartItems as $item) {
            $_SESSION['cart'][] = $item;
        }
    }
}
?>
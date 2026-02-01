
<?php
// Include cart functions
require_once 'cart-functions.php';

// Product-related functions
function getProductsByCategory($category, $limit = 12, $page = 1) {
    $pdo = getDBConnection();
    $offset = ($page - 1) * $limit;
    
    if ($category === 'all') {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE stock > 0 LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND stock > 0 LIMIT ? OFFSET ?");
        $stmt->execute([$category, $limit, $offset]);
    }
    
    return $stmt->fetchAll();
}

function getProductById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCategories() {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT DISTINCT category FROM products WHERE stock > 0");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getFeaturedProducts($limit = 6) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE featured = 1 AND stock > 0 LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Helper functions
function getProductStock($productId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $result = $stmt->fetch();
    return $result ? $result['stock'] : 0;
}
?>
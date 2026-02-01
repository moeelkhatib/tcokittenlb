
<?php
require_once 'includes/config.php';
echo json_encode([
    'success' => true,
    'total' => getCartTotal()
]);
?>
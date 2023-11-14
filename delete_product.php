<!-- delete_product.php-->
<?php
require('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'] ?? null;
    
    if ($product_id) {
        $query = "DELETE FROM Products WHERE product_id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header('Location: admin.php');
exit;

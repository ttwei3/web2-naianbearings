<?php
require('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $categoryId = $_POST['category_id'];

    $updateQuery = "UPDATE Products SET category_id = :category_id WHERE product_id = :product_id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $updateStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    
    if ($updateStmt->execute()) {
        header('Location: admin.php');
        exit();
    } else {
        echo "Error updating category for the product.";
    }
} else {
    echo "Invalid request method.";
}
?>

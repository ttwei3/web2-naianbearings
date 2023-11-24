<?php
// 连接数据库
require('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取提交的数据
    $productId = $_POST['product_id'];
    $categoryId = $_POST['category_id'];

    // 更新商品的类别
    $updateQuery = "UPDATE Products SET category_id = :category_id WHERE product_id = :product_id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $updateStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    
    if ($updateStmt->execute()) {
        // 更新成功，重定向回管理员页面
        header('Location: admin.php');
        exit();
    } else {
        // 更新失败，处理错误
        echo "Error updating category for the product.";
    }
} else {
    // 如果不是POST请求，处理错误
    echo "Invalid request method.";
}
?>

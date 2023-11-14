<!-- detail.php-->
<?php
require('connect.php');
require('header.php');
$product_id = $_GET['id']; 
$query = "SELECT Products.*, Images.filename FROM Products LEFT JOIN Images ON Products.product_id = Images.product_id WHERE Products.product_id = :product_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
</head>
<body>
    <?php if ($product): ?>
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p><strong>Description:</strong> <?php echo $product['product_description'] ?? 'No description'; ?></p>
        <p><strong>Price: </strong>$<?php echo htmlspecialchars($product['product_price']); ?></p>
        <p><strong>Stock Quantity: </strong><?php echo htmlspecialchars($product['stock_quantity']); ?></p>
        <p><strong>Category ID: </strong><?php echo htmlspecialchars($product['category_id']); ?></p>
        <?php if ($product['filename']): ?>
            <img src="upload/<?php echo htmlspecialchars($product['filename']); ?>" alt="Product Image">
        <?php endif; ?>
    <?php else: ?>
        <p>Product not found.</p>
    <?php endif; ?>
</body>
</html>

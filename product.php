<!-- product.php-->
<?php
require('connect.php');
require('header.php');
$query = "SELECT Products.product_id, product_name, product_description, product_price, stock_quantity, category_id, Images.filename FROM Products LEFT JOIN Images ON Products.product_id = Images.product_id";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>
</head>
<body>
    <h1>Our Products</h1>
    <?php
    foreach ($products as $product) {
        echo "<a href='detail.php?id=" . htmlspecialchars($product['product_id']) . "'>" . htmlspecialchars($product['product_name']) . "</a><br>";
        if ($product['filename']) {
            echo "<img src='upload/" . htmlspecialchars($product['filename']) . "' alt='" . htmlspecialchars($product['product_name']) . "' style='width:100px; height:auto;'><br><br>";
        }
    }
    require('footer.php');
    ?>
</body>
</html>

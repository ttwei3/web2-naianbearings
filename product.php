<?php
require('connect.php');
require('header.php');

$categoryID = isset($_GET['category_id']) ? $_GET['category_id'] : null;

$query = $categoryID 
    ? "SELECT Products.product_id, product_name, product_description, product_price, stock_quantity, category_id, Images.filename FROM Products LEFT JOIN Images ON Products.product_id = Images.product_id WHERE category_id = :category_id" 
    : "SELECT Products.product_id, product_name, product_description, product_price, stock_quantity, category_id, Images.filename FROM Products LEFT JOIN Images ON Products.product_id = Images.product_id";

$stmt = $db->prepare($query);

if ($categoryID) {
    $stmt->bindParam(':category_id', $categoryID, PDO::PARAM_INT);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<h1 id="margin">Our Products</h1>
<?php
foreach ($products as $product) {
    echo "<div class='product'>";
    echo "<a href='detail.php?id=" . htmlspecialchars($product['product_id']) . "'>" . htmlspecialchars($product['product_name']) . "</a><br>";
    if (!empty($product['filename'])) {
        echo "<img src='upload/" . htmlspecialchars($product['filename']) . "' alt='" . htmlspecialchars($product['product_name']) . "' class='product-image'><br>";
    }
    echo "<p>Price: " . htmlspecialchars($product['product_price']) . "</p>";
    echo "<p>Stock: " . htmlspecialchars($product['stock_quantity']) . "</p>";
    echo "</div><br><br>";
}
?>
<?php require('footer.php'); ?>

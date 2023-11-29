<!-- product_detail.php-->
<?php
require('connect.php');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

$productImage = null; // Initialize variable to store the image path
$imageExists = false; // Initialize variable to check if image exists

if (isset($_GET['id'])) {
    $productId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); 
    $query = "SELECT Products.*, Images.filename AS product_image_filename FROM Products LEFT JOIN Images ON Products.product_id = Images.product_id WHERE Products.product_id = :productId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found.";
        exit;
    }

    if (!empty($product['product_image_filename'])) {
        $imagePath = 'upload/'; 
        $productImage = $imagePath . $product['product_image_filename'];
        $imageExists = file_exists($productImage);
    }
} else {
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="icon" href="./images/page-logo.svg" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<header>
    <div class="top-header2">
    <div class="login-info2">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Log out</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <p>Welcome!</p>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Log in</a>
        <?php endif; ?>
    </div>
    </div>
    <nav>
        <ul class="main-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="admin.php">DashBoard</a></li>
            <li><a href="manage_categories.php">Categories Management</a></li>
            <li><a href="manage_products.php">Products Management</a></li>
            <li><a href="manage_comments.php">Comments Management</a></li>
        </ul>
    </nav>
</header>
    <h1>Product Details</h1>
    <div class="product">
        <h3><?php echo htmlspecialchars($product['product_name'] ?? 'N/A'); ?></h3>
        <p><strong>Description:</strong> <?php echo strip_tags($product['product_description']) ?? 'No description'; ?></p>
        <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['product_price'] ?? '0'); ?></p>
        <p><strong>Stock Quantity:</strong> <?php echo htmlspecialchars($product['stock_quantity'] ?? '0'); ?></p>
        <p><strong>Category ID:</strong> <?php echo htmlspecialchars($product['category_id'] ?? 'N/A'); ?></p>
        <p><strong>Image:</strong>
            <?php if ($imageExists && $productImage): ?>
                <img src="<?php echo htmlspecialchars($productImage); ?>" alt="Product Image" style="width:100px;">
            <?php else: ?>
                No image available.
            <?php endif; ?>
        </p>
        <form action="edit_product.php" method="get">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
            <input type="submit" value="Edit">
        </form>
        <form action="delete_product.php" method="post" onsubmit="return confirm('Are you sure you want to delete this product?');">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
            <input type="submit" value="Delete">
        </form>
    </div>
</body>
</html>

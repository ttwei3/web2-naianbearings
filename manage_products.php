<?php
require('connect.php');
require('header_admin.php');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect non-admins back to index page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

// Default sort settings
$orderBy = 'product_name'; 
$orderDirection = 'ASC'; 

// Check for requested sort column and direction from dropdown
if (isset($_POST['sort_by']) && in_array($_POST['sort_by'], ['product_name', 'product_price', 'stock_quantity'])) {
    $orderBy = $_POST['sort_by'];
}

// Check for requested sort direction from dropdown
if (isset($_POST['sort_dir'])) {
    $orderDirection = $_POST['sort_dir'] === 'DESC' ? 'DESC' : 'ASC';
}

// Fetch products with sorting
$query = "SELECT * FROM Products ORDER BY $orderBy $orderDirection";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


    <h1>Manage Products</h1>
    <form action="manage_products.php" method="post">
        <label for="sort_by">Sort products by:</label>
        <select name="sort_by" id="sort_by">
            <option value="product_name" <?php echo $orderBy == 'product_name' ? 'selected' : ''; ?>>Name</option>
            <option value="product_price" <?php echo $orderBy == 'product_price' ? 'selected' : ''; ?>>Price</option>
            <option value="stock_quantity" <?php echo $orderBy == 'stock_quantity' ? 'selected' : ''; ?>>Stock</option>
        </select>

        <label for="sort_dir">Direction:</label>
        <select name="sort_dir" id="sort_dir">
            <option value="ASC" <?php echo $orderDirection == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo $orderDirection == 'DESC' ? 'selected' : ''; ?>>Descending</option>
        </select>

        <input type="submit" value="Sort">
    </form>

    <p>Currently sorted by: <?php echo htmlspecialchars($orderBy) . ' ' . htmlspecialchars($orderDirection); ?></p>
    <a href="create.php" class="button">Create New Product</a>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <a href='product_detail.php?id=<?php echo urlencode($product['product_id']); ?>'>
                            <?php echo htmlspecialchars($product['product_name']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($product['product_price']); ?></td>
                    <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                    <td>
                        <a href='edit_product.php?id=<?php echo urlencode($product['product_id']); ?>' class="button">Edit</a> 
                        <form action="delete_product.php" method="post" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

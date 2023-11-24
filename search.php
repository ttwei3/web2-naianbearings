<?php
require('connect.php');
require('header.php');
$search_keyword = $_GET['search_keyword'] ?? '';

$query = "SELECT Products.*, categories.category_name FROM Products 
          LEFT JOIN categories ON Products.category_id = categories.category_id 
          WHERE product_name LIKE :keyword 
          OR categories.category_name LIKE :keyword";
$stmt = $db->prepare($query);
$keywordWithWildcards = '%' . $search_keyword . '%';
$stmt->bindParam(':keyword', $keywordWithWildcards);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
</head>
<body>
    <main>
        <h1>Search Results</h1>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <a href='detail.php?id=<?php echo htmlspecialchars($product['product_id']); ?>'>
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </a><br>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found matching your search criteria.</p>
        <?php endif; ?>
    </main>
    <?php
        require('footer.php');
    ?>
    
</body>
</html>

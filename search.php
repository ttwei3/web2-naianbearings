<?php
require('connect.php');
$search_keyword = $_GET['search_keyword'] ?? '';
$category_selected = $_GET['category'] ?? 'all';

$query = "SELECT Products.*, categories.category_name FROM Products 
          LEFT JOIN categories ON Products.category_id = categories.category_id ";

if (!empty($search_keyword)) {
    $keywordWithWildcards = '%' . $search_keyword . '%';
    $query .= "WHERE (product_name LIKE :keyword OR categories.category_name LIKE :keyword)";
    
    if ($category_selected != 'all') {
        $query .= " AND categories.category_id = :category_id";
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(':keyword', $keywordWithWildcards);

    if ($category_selected != 'all') {
        $stmt->bindParam(':category_id', $category_selected, PDO::PARAM_INT);
    }
} else {
    // 如果没有输入搜索关键词，可以选择返回所有产品或者不返回任何产品
    // 这里的代码示例返回所有产品
    $stmt = $db->prepare($query);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

require('header.php');
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

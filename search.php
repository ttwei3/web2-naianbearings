<?php
require('connect.php');
$results_per_page = 10; 
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
}

$count_query = preg_replace('/SELECT Products.\*, categories.category_name FROM/', 'SELECT COUNT(*) FROM', $query);
$count_stmt = $db->prepare($count_query);

if (!empty($search_keyword)) {
    $count_stmt->bindParam(':keyword', $keywordWithWildcards);

    if ($category_selected != 'all') {
        $count_stmt->bindParam(':category_id', $category_selected, PDO::PARAM_INT);
    }
}

$count_stmt->execute();
$number_of_results = $count_stmt->fetchColumn();

$number_of_pages = ceil($number_of_results / $results_per_page);

$current_page = $_GET['page'] ?? 1;
$start_from = ($current_page - 1) * $results_per_page;

$query .= " LIMIT :start_from, :results_per_page";
$stmt = $db->prepare($query);

if (!empty($search_keyword)) {
    $stmt->bindParam(':keyword', $keywordWithWildcards);

    if ($category_selected != 'all') {
        $stmt->bindParam(':category_id', $category_selected, PDO::PARAM_INT);
    }
}

$stmt->bindParam(':start_from', $start_from, PDO::PARAM_INT);
$stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

require('header.php');
?>

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

    <?php if ($number_of_results > 10): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php for ($page = 1; $page <= $number_of_pages; $page++): ?>
                <li class="page-item<?php if ($current_page == $page) echo ' active'; ?>">
                    <a class="page-link" href="search.php?page=<?php echo $page; ?>&search_keyword=<?php echo urlencode($search_keyword); ?>&category=<?php echo urlencode($category_selected); ?>">
                        <?php echo $page; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</main>
<?php require('footer.php'); ?>

    

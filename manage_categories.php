<?php
require('connect.php');
require('header_admin.php');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['submit_category'])) {
    $categoryId = $_POST['category_id'] ?? null;
    $categoryName = $_POST['category_name'];
    $categoryDescription = $_POST['category_description'] ?? '';

    if ($categoryId) {
        $query = "UPDATE categories SET category_name = :category_name, category_description = :category_description WHERE category_id = :category_id";
    } else {
        $query = "INSERT INTO categories (category_name, category_description) VALUES (:category_name, :category_description)";
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(':category_name', $categoryName);
    $stmt->bindParam(':category_description', $categoryDescription);
    if ($categoryId) {
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    }
    $stmt->execute();

    header('Location: manage_categories.php');
    exit();
}

if (isset($_POST['delete_category'])) {
    $categoryId = $_POST['category_id'] ?? null;

    if ($categoryId) {
        $deleteQuery = "DELETE FROM categories WHERE category_id = :category_id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $result = $deleteStmt->execute(); 

        if ($result) {
            header('Location: manage_categories.php');
            exit();
        } else {
            echo "Failed to delete category. Error: " . implode(", ", $deleteStmt->errorInfo());
            exit();
        }
    }
}

$query = "SELECT category_id, category_name, category_description FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


    <h1>Manage Categories</h1>
    <h3>Create Categories</h3>
    <form method="post" action="manage_categories.php">
        <input type="hidden" name="category_id" value="">
        <label for="category_name_create">Category Name:</label>
        <input type="text" id="category_name_create" name="category_name" required><br>
        <label for="category_description_create">Description:</label>
        <textarea id="category_description_create" name="category_description"></textarea><br>
        <input type="submit" name="submit_category" value="Create">
    </form>
    <h3>Existing Categories</h3>
    <?php foreach ($categories as $category): ?>
        <div>
            <form method="post" action="manage_categories.php">
                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['category_id']); ?>">
                <label for="category_name_<?php echo $category['category_id']; ?>">Name:</label>
                <input type="text" id="category_name_<?php echo $category['category_id']; ?>" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required><br>
                <label for="category_description_<?php echo $category['category_id']; ?>">Description:</label>
                <textarea id="category_description_<?php echo $category['category_id']; ?>" name="category_description"><?php echo htmlspecialchars($category['category_description']); ?></textarea><br>
                <input type="submit" name="submit_category" value="Update">
                <input type="submit" name="delete_category" value="Delete" onclick="return confirm('Are you sure you want to delete this category?');">
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>

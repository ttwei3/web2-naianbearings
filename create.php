<!-- create.php-->
<?php

require('connect.php');
require '/Applications/xampp/htdocs/a/php-image-resize-master/lib/ImageResize.php';
require '/Applications/xampp/htdocs/a/php-image-resize-master/lib/ImageResizeException.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 */
$categoryQuery = "SELECT category_id, category_name FROM categories";
$categoryStmt = $db->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type        = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

$isImageValid = true; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['product_name'] = $_POST['product_name'];
    $_SESSION['product_description'] = $_POST['product_description'];
    $_SESSION['product_price'] = $_POST['product_price'];
    $_SESSION['stock_quantity'] = $_POST['stock_quantity'];
    $_SESSION['category_id'] = $_POST['category_id'];


    // Handle file upload if a file was submitted
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadPath = "upload/";
        $temporaryPath = $_FILES['product_image']['tmp_name'];
        $newProductImage = $uploadPath . basename($_FILES['product_image']['name']);

        // Use the 'file_is_an_image' function to verify the image
        if (!file_is_an_image($temporaryPath, $newProductImage)) {
            $isImageValid = false;
            echo "The file is not a valid image.";
        }
    }

    if ($isImageValid) {
        $productName = $_POST['product_name'];
        $productDescription = $_POST['product_description']; 
        $productPrice = $_POST['product_price'];
        $stockQuantity = $_POST['stock_quantity'];
        $categoryID = $_POST['category_id'];

        // Insert product details into the Products table
        $insertProductQuery = "INSERT INTO Products (product_name, product_description, product_price, stock_quantity, category_id) VALUES (:product_name, :product_description, :product_price, :stock_quantity, :category_id)";
        $insertProductStmt = $db->prepare($insertProductQuery);
        $insertProductStmt->bindParam(':product_name', $productName);
        $insertProductStmt->bindParam(':product_description', $productDescription);
        $insertProductStmt->bindParam(':product_price', $productPrice);
        $insertProductStmt->bindParam(':stock_quantity', $stockQuantity);
        $insertProductStmt->bindParam(':category_id', $categoryID);

        if ($insertProductStmt->execute()) {
            $productId = $db->lastInsertId(); // Get the ID of the newly created product

            // Proceed with file upload if the file is a valid image
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                if (!file_exists($newProductImage)) {
                    move_uploaded_file($temporaryPath, $newProductImage);

                    //resize the file
                    $image = new \Gumlet\ImageResize($newProductImage);
                    $image->resizeToWidth(500); 
                    $image->save($newProductImage);

                    $imageData = file_get_contents($newProductImage);

                    $insertImageQuery = "INSERT INTO Images (product_id, filename, image, upload_time) VALUES (:product_id, :filename, :image, NOW())";
                    $insertImageStmt = $db->prepare($insertImageQuery);
                    $insertImageStmt->bindParam(':product_id', $productId);
                    $insertImageStmt->bindParam(':filename', $_FILES['product_image']['name']);
                    $insertImageStmt->bindParam(':image', $imageData, PDO::PARAM_LOB); // PDO::PARAM_LOB tells PDO to map this to a SQL BLOB
                    $insertImageStmt->execute();
                } else {
                    echo $_FILES['product_image']['name'] . " already exists.";
                }
            }

            unset($_SESSION['product_name']);
            unset($_SESSION['product_description']);
            unset($_SESSION['product_price']);
            unset($_SESSION['stock_quantity']);
            unset($_SESSION['category_id']);

            header('Location: admin.php');
            exit();
        } else {
            echo "Product creation failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Product</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
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
    <h2>Create New Product</h2>
    <form method="POST" action="create.php" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label><br>
        <input type="text" id="product_name" name="product_name" required value="<?php echo isset($_SESSION['product_name']) ? $_SESSION['product_name'] : ''; ?>"><br>

        <label for="editor-textarea">Product Description:</label><br>
        <div id="editor-container"></div>
        <textarea name="product_description" id="editor-textarea" style="display:none;"></textarea><br>

        <label for="product_price">Product Price:</label><br>
        <input type="number" id="product_price" name="product_price" step="0.01" required value="<?php echo isset($_SESSION['product_price']) ? $_SESSION['product_price'] : ''; ?>"><br>
        
        <label for="product_image">Product Image:</label><br>
        <input type="file" id="product_image" name="product_image"><br>

        <label for="stock_quantity">Stock Quantity:</label><br>
        <input type="number" id="stock_quantity" name="stock_quantity" required value="<?php echo isset($_SESSION['stock_quantity']) ? $_SESSION['stock_quantity'] : ''; ?>"><br>

        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>" <?php if (isset($_SESSION['category_id']) && $category['category_id'] == $_SESSION['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <input type="submit" value="Create Product">
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            var quill = new Quill('#editor-container', {
                theme: 'snow'
            });
            var form = document.querySelector('form');
            form.onsubmit = function() {
                var product_description = document.querySelector('#editor-textarea');
                product_description.value = quill.root.innerHTML;
            };
            <?php if (isset($_SESSION['product_description'])): ?>
                quill.root.innerHTML = '<?php echo str_replace("\n", "\\n", addslashes($_SESSION['product_description'])); ?>';
            <?php endif; ?>
        </script>
    </form>
</body>
</html>


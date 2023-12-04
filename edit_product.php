<!-- edit_product.php-->
<?php


require('connect.php');
require '/Applications/xampp/htdocs/web2/project-final/lib/ImageResize.php';
require '/Applications/xampp/htdocs/web2/project-final/lib/ImageResizeException.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

$categoryQuery = "SELECT category_id, category_name FROM categories";
$categoryStmt = $db->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$product_id = $_GET['id'];
$query = "SELECT * FROM Products WHERE product_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: admin.php');
    exit();
}

$product_name = $product['product_name'] ?? '';
$product_description = $product['product_description'] ?? '';
$product_price = $product['product_price'] ?? '';
$stock_quantity = $product['stock_quantity'] ?? '';
$category_id = $product['category_id'] ?? '';

function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type        = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deleteImage = isset($_POST['delete_image']); 

    $imageQuery = "SELECT filename FROM Images WHERE product_id = :product_id";
    $imageStmt = $db->prepare($imageQuery);
    $imageStmt->bindParam(':product_id', $product_id);
    $imageStmt->execute();
    $imageRow = $imageStmt->fetch(PDO::FETCH_ASSOC);

    if ($deleteImage && $imageRow) {
        $imagePath = 'upload/' . $imageRow['filename'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $removeImageQuery = "DELETE FROM Images WHERE product_id = :product_id";
        $removeImageStmt = $db->prepare($removeImageQuery);
        $removeImageStmt->bindParam(':product_id', $product_id);
        $removeImageStmt->execute();
    }

    $isImageValid = true;
    if (!$deleteImage && isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadPath = "upload/";
        $temporaryPath = $_FILES['product_image']['tmp_name'];
        $newProductImage = $uploadPath . basename($_FILES['product_image']['name']);

        if (!file_is_an_image($temporaryPath, $newProductImage)) {
            $isImageValid = false;
            echo "The file is not a valid image.";
        } else {
            if (!file_exists($newProductImage)) {
                move_uploaded_file($temporaryPath, $newProductImage);

                $image = new \Gumlet\ImageResize($newProductImage);
                $image->resizeToWidth(500); // 或者使用其他所需的尺寸
                $image->save($newProductImage);

                $imageData = file_get_contents($newProductImage);

                $checkImageQuery = "SELECT * FROM Images WHERE product_id = :product_id";
                $checkImageStmt = $db->prepare($checkImageQuery);
                $checkImageStmt->bindParam(':product_id', $product_id);
                $checkImageStmt->execute();
                $existingImage = $checkImageStmt->fetch(PDO::FETCH_ASSOC);

                if ($existingImage) {
                    $updateImageQuery = "UPDATE Images SET filename = :filename, image = :image, upload_time = NOW() WHERE product_id = :product_id";
                    $updateImageStmt = $db->prepare($updateImageQuery);
                } else {
                    $updateImageQuery = "INSERT INTO Images (product_id, filename, image, upload_time) VALUES (:product_id, :filename, :image, NOW())";
                    $updateImageStmt = $db->prepare($updateImageQuery);
                }

                $updateImageStmt->bindParam(':product_id', $product_id);
                $updateImageStmt->bindParam(':filename', $_FILES['product_image']['name']);
                $updateImageStmt->bindParam(':image', $imageData, PDO::PARAM_LOB);
                $updateImageStmt->execute();
            } else {
                echo $_FILES['product_image']['name'] . " already exists.";
            }
        }
    }

    if ($isImageValid) {
        $productName = $_POST['product_name'];
        $productDescription = $_POST['product_description'];
        $productPrice = $_POST['product_price'];
        $stockQuantity = $_POST['stock_quantity'];
        $categoryID = $_POST['category_id'];

        $updateProductQuery = "UPDATE Products SET product_name = :product_name, product_description = :product_description, product_price = :product_price, stock_quantity = :stock_quantity, category_id = :category_id WHERE product_id = :product_id";
        $updateProductStmt = $db->prepare($updateProductQuery);
        $updateProductStmt->bindParam(':product_name', $productName);
        $updateProductStmt->bindParam(':product_description', $productDescription);
        $updateProductStmt->bindParam(':product_price', $productPrice);
        $updateProductStmt->bindParam(':stock_quantity', $stockQuantity);
        $updateProductStmt->bindParam(':category_id', $categoryID);
        $updateProductStmt->bindParam(':product_id', $product_id);

        if ($updateProductStmt->execute()) {
            header('Location: admin.php');
            exit();
        } else {
            echo "Product update failed.";
        }
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
    <h1>Edit Product</h1>
    <form action="edit_product.php?id=<?php echo htmlspecialchars($product_id); ?>" method="post" enctype="multipart/form-data">
        <label for="product_name">Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required><br>

        <label for="product_description">Description:</label>
        <div id="editor-container"><?php echo htmlspecialchars($product_description); ?></div>
        <textarea name="product_description" id="product_description" style="display:none;"></textarea><br>

        <label for="product_price">Price:</label>
        <input type="text" name="product_price" id="product_price" value="<?php echo htmlspecialchars($product_price); ?>" required><br>

        <label for="stock_quantity">Stock:</label>
        <input type="number" name="stock_quantity" id="stock_quantity" value="<?php echo htmlspecialchars($stock_quantity); ?>" required><br>

        <label for="product_image">Product Image:</label>
        <?php
            $imageQuery = "SELECT filename FROM Images WHERE product_id = :product_id";
            $imageStmt = $db->prepare($imageQuery);
            $imageStmt->bindParam(':product_id', $product_id);
            $imageStmt->execute();
            $imageRow = $imageStmt->fetch(PDO::FETCH_ASSOC);

            if ($imageRow) {
                echo '<img src="upload/' . htmlspecialchars($imageRow['filename']) . '" alt="Current Product Image" width="100"><br>';
                echo 'If you do not select a new file, the current image will remain.<br>';
                echo '<input type="checkbox" name="delete_image" id="delete_image"> <label for="delete_image">Delete current image</label><br>';

            }
        ?>
        <input type="file" name="product_image" id="product_image"><br>

        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>" <?php if ($category['category_id'] == $product['category_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <input type="submit" value="Update Product">
    </form>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow'
        });
        var form = document.querySelector('form');
        form.onsubmit = function() {
            var product_description = document.querySelector('textarea[name=product_description]');
            product_description.value = quill.root.innerHTML;
        };
        quill.root.innerHTML = '<?php echo addslashes($product_description); ?>';

        
    </script>
</body>
</html>
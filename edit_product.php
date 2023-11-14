<!-- edit_product.php-->
<?php


require('connect.php');
require('header_admin.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $isImageValid = true; // Flag to indicate if the image is valid

    // Handle file upload if a file was submitted
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploadPath = "upload/";
        $temporaryPath = $_FILES['product_image']['tmp_name'];
        $newProductImage = $uploadPath . basename($_FILES['product_image']['name']);

        // Use the 'file_is_an_image' function to verify the image
        if (!file_is_an_image($temporaryPath, $newProductImage)) {
            $isImageValid = false;
            echo "Error: The file is not a valid image.";
        }
    }

    if ($isImageValid) {
        $productName = $_POST['product_name'];
        $productDescription = $_POST['product_description']; 
        $productPrice = $_POST['product_price'];
        $stockQuantity = $_POST['stock_quantity'];
        $categoryID = $_POST['category_id'];

        // Update product details in the Products table
        $updateProductQuery = "UPDATE Products SET product_name = :product_name, product_description = :product_description, product_price = :product_price, stock_quantity = :stock_quantity, category_id = :category_id WHERE product_id = :product_id";
        $updateProductStmt = $db->prepare($updateProductQuery);
        $updateProductStmt->bindParam(':product_name', $productName);
        $updateProductStmt->bindParam(':product_description', $productDescription);
        $updateProductStmt->bindParam(':product_price', $productPrice);
        $updateProductStmt->bindParam(':stock_quantity', $stockQuantity);
        $updateProductStmt->bindParam(':category_id', $categoryID);
        $updateProductStmt->bindParam(':product_id', $product_id);

        // Execute the product update statement
        if ($updateProductStmt->execute()) {
            // Handle file upload if the file is a valid image
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                if (!file_exists($newProductImage)) {
                    move_uploaded_file($temporaryPath, $newProductImage);
                    $imageData = file_get_contents($newProductImage);

                    // Check if an image already exists for this product
                    $checkImageQuery = "SELECT * FROM Images WHERE product_id = :product_id";
                    $checkImageStmt = $db->prepare($checkImageQuery);
                    $checkImageStmt->bindParam(':product_id', $product_id);
                    $checkImageStmt->execute();
                    $existingImage = $checkImageStmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingImage) {
                        // Update the existing image
                        $updateImageQuery = "UPDATE Images SET filename = :filename, image = :image, upload_time = NOW() WHERE product_id = :product_id";
                        $updateImageStmt = $db->prepare($updateImageQuery);
                    } else {
                        // Insert new image
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

            header('Location: admin.php');
            exit();
        } else {
            echo "Product edition failed.";
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
</head>
<body>
    <h1>Edit Product</h1>
    <form action="edit_product.php?id=<?php echo htmlspecialchars($product_id); ?>" method="post" enctype="multipart/form-data">
        <label for="product_name">Name:</label>
        <input type="text" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required><br>

        <label for="product_description">Description:</label>
        <div id="editor-container"><?php echo htmlspecialchars($product_description); ?></div>
        <textarea name="product_description" id="product_description" style="display:none;"></textarea><br>

        <label for="product_price">Price:</label>
        <input type="text" name="product_price" value="<?php echo htmlspecialchars($product_price); ?>" required><br>

        <label for="stock_quantity">Stock:</label>
        <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($stock_quantity); ?>" required><br>

        <label for="product_image">Product Image:</label>
        <input type="file" name="product_image" id="product_image"><br>

        <label for="category_id">Category ID:</label>
        <input type="number" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>" required><br>

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
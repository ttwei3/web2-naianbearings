<!-- create.php-->
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

// Function to check if the file is an image
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));
    $actual_mime_type        = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

// Check if the form was submitted
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
        // Assign values from form data
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

        // Execute the product insert statement
        if ($insertProductStmt->execute()) {
            $productId = $db->lastInsertId(); // Get the ID of the newly created product

            // Proceed with file upload if the file is a valid image
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                if (!file_exists($newProductImage)) {
                    move_uploaded_file($temporaryPath, $newProductImage);
                    $imageData = file_get_contents($newProductImage);

                    // Insert image into the Images table
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
</head>
<body>
    <h2>Create New Product</h2>
    <form method="POST" action="create.php" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label><br>
        <input type="text" id="product_name" name="product_name" required><br>

        <label for="product_description">Product Description:</label><br>
        <div id="editor-container"></div>
        <input type="hidden" name="product_description" id="product_description">

        <label for="product_price">Product Price:</label><br>
        <input type="number" id="product_price" name="product_price" step="0.01" required><br>
        
        <label for="product_image">Product Image:</label><br>
        <input type="file" id="product_image" name="product_image"><br>

        <label for="stock_quantity">Stock Quantity:</label><br>
        <input type="number" id="stock_quantity" name="stock_quantity" required><br>

        <label for="category_id">Category ID:</label><br>
        <input type="number" id="category_id" name="category_id" required><br>

        <input type="submit" value="Create Product">
        <script>
            var quill = new Quill('#editor-container', {
                theme: 'snow'
            });
            var form = document.querySelector('form');
            form.onsubmit = function() {
                var product_description = document.querySelector('input[name=product_description]');
                product_description.value = quill.root.innerHTML;
            };
        </script>
    </form>
</body>
</html>


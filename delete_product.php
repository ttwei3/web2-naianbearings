<?php
require('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'] ?? null;

    if ($product_id) {
        $imageQuery = "SELECT filename FROM Images WHERE product_id = :product_id";
        $imageStmt = $db->prepare($imageQuery);
        $imageStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $imageStmt->execute();
        $imageRow = $imageStmt->fetch(PDO::FETCH_ASSOC);

        if ($imageRow) {
            $imagePath = 'upload/' . $imageRow['filename'];
            if (file_exists($imagePath)) {
                unlink($imagePath); 
            }

            $removeImageQuery = "DELETE FROM Images WHERE product_id = :product_id";
            $removeImageStmt = $db->prepare($removeImageQuery);
            $removeImageStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $removeImageStmt->execute();
        }
        
        $query = "DELETE FROM Products WHERE product_id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header('Location: admin.php');
exit;
?>

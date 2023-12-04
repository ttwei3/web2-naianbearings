<!-- admin.php-->
<?php
require('connect.php');
require('header_admin.php');
session_start();

if (isset($_SESSION['success_message'])) {
    echo '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
    unset($_SESSION['success_message']); 
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 


// Fetch products and comments count
$productCountQuery = "SELECT COUNT(*) FROM Products";
$productCountStmt = $db->prepare($productCountQuery);
$productCountStmt->execute();
$productCount = $productCountStmt->fetchColumn();

$commentCountQuery = "SELECT COUNT(*) FROM Comments";
$commentCountStmt = $db->prepare($commentCountQuery);
$commentCountStmt->execute();
$commentCount = $commentCountStmt->fetchColumn();

$categoryCountQuery = "SELECT COUNT(*) FROM categories";
$categoryCountStmt = $db->prepare($categoryCountQuery);
$categoryCountStmt->execute();
$categoryCount = $categoryCountStmt->fetchColumn();


?>

    <h1>Dashboard</h1>
    <table>
        <tr>
            <td>Total Products:</td>
            <td><?php echo $productCount; ?></td>
        </tr>
        <tr>
            <td>Total Comments:</td>
            <td><?php echo $commentCount; ?></td>
        </tr>
        <tr>
            <td>Total Categories:</td>
            <td><?php echo $categoryCount; ?></td>
        </tr>
    </table>
</body>
</html>

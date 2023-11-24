<?php
require('connect.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];

    $deleteQuery = "DELETE FROM Comments WHERE comment_id = :comment_id";
    $stmt = $db->prepare($deleteQuery);
    $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: admin.php');
    exit();
} else {
    header('Location: admin.php');
    exit();
}

?>

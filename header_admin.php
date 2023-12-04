<!-- header_admin.php-->
<?php 
    session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Website</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="icon" href="./images/page-logo.svg" type="image/x-icon">
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

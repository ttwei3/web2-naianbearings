<!-- header_admin.php-->
<?php 
    session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Website</title>
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
    </div>
    <nav>
        <ul class="main-nav">
            <li><a href="admin.php">DashBoard</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="product.php">Products</a></li>
            <li><a href="support.php">Support</a></li>
        </ul>
    </nav>
</header>

<!-- header.php-->
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
    <div class="top-header">
    <div class="login-info">
        <span>Welcome to Naian Bearing!</span>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Log out</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a href="admin.php">Dashboard</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Log in</a>
        <?php endif; ?>
    </div>
        </div>
        <div class="search-bar">
            <form action="search.php" method="get">
                <input type="text" name="search_keyword" placeholder="Search our product">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
    <nav>
        <ul class="main-nav">
            <li><a href="index.php">Home</a></li>
            <li><a href="product.php">Products</a></li>
            <li><a href="support.php">Support</a></li>
        </ul>
    </nav>
</header>

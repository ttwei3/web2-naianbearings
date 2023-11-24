<!-- index.php-->
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
            <div class="search-bar">
                <form action="search.php" method="get">
                    <input type="text" name="search_keyword" placeholder="Search our product">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
        <nav>
            <ul class="main-nav">
                <li><a href="#">Home</a></li>
                <li><a href="product.php">Products</a></li>
                <li><a href="#">Support</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="content-section">
            <h1>Welcome to Our Website</h1>
            <p>At Naian Bearing, we pride ourselves on being a leading manufacturer and distributor of high-quality bearings, serving diverse industries with excellence since 2006. Our unwavering commitment to precision engineering, top-notch materials, and exceptional customer service has made us a trusted partner for businesses worldwide.</p>
        </section>
        <section class="products-section">
            <h2>Our Products</h2>
        </section>
    </main>

    <footer>
        <img src="" alt="" id="footerlogo">
        <div class="footer-content">
            <div class="footer-nav">
                <ul>
                    <li>Copyright © 2006&nbsp;<a href="#"> Naian Bearing</a></li>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="product.php">Products</a></li>
                    <li><a href="support.php">Support</a></li>
                </ul>
            </div>
            <div class="footer-under">
                <div class="contact-info">
                    <h3>Contact Us</h3>
                    <p>Email: naian@gmail.com</p>
                </div>
                <div class="social-media">
                    <h3>Follow Us</h3>
                    <img src="images/ins.svg" alt="fail" id="ins">
                    <img src="images/tw.svg" alt="fail" id="tw">
                    <img src="images/ytb.svg" alt="fail" id="ytb">
                </div>
            </div>
            <div id="indexsocial">
            </div>
        </div>
    </footer>
</body>
</html>

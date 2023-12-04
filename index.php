<!-- index.php-->
<?php
    session_start();
    require('connect.php'); 

    if (isset($_SESSION['success_message'])) {
        echo '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']); 
    }

    $categoryQuery = "SELECT category_id, category_name FROM categories";
    $categoryStmt = $db->query($categoryQuery);
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

    $search_keyword = "";
    $category_selected = "";
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Naian Bearing</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="icon" href="./images/page-logo.svg" type="image/x-icon">
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
                    <input type="text" name="search_keyword" placeholder="Search our product" value="<?php echo htmlspecialchars($search_keyword); ?>">
                    <select name="category">
                        <option value="all" <?php echo ($category_selected == 'all' ? 'selected' : ''); ?>>All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>" 
                                    <?php echo ($category_selected == $category['category_id'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
        <nav>
            <ul class="main-nav">
                <li><a href="#">Home</a></li>
                <li><a href="product.php">Products</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1 class="h1">Welcome to Our Website</h1>
        <p id="paragraph">At Naian Bearing, we pride ourselves on being a leading manufacturer and distributor of high-quality bearings, serving diverse industries with excellence since 2006. Our unwavering commitment to precision engineering, top-notch materials, and exceptional customer service has made us a trusted partner for businesses worldwide.</p>
        <h1 class="h1">WHY CHOOSE US</h1>
        <section id="whychooseus">
            <div id="imgsp1">
                <img src="images/image15.jpg" alt="fail to load" >
                <p id="p1"><b>Unmatched Manufacturing Expertise:</b>Our bearings are crafted using cutting-edge manufacturing processes, supported by seasoned engineers with years of experience. We ensure every product meets the highest industry standards.</p>
            </div>
            <div id="imgsp2">
                <img src="images/image14.jpg" alt="fail to load" >
                <p id="p2"><b>Wide Product Range:</b>With an extensive catalog of bearings, we cater to various industrial applications, including automotive, industrial machinery, agriculture... Whether you need standard or custom-designed bearings, we have the right solution for you.</p>
            </div>
            <div id="imgsp3">
                <img src="images/image13.jpg" alt="fail to load" >
                <p id="p3"><b>Superior Quality Assurance:</b>Quality is at the core of everything in our company. Our company subject our bearings to rigorous testing and inspection processes to ensure exceed your expectations in performance, reliability, and durability.</p>
            </div>
            <div id="imgsp4">
                <img src="images/image12.jpg" alt="fail to load" >
                <p id="p4"><b>Customer-Centric Approach:</b>Your success is our success. Our company value our clients and work closely with them to understand their unique requirements, providing tailored solutions and personalized support to ensure maximum satisfaction.</p>
            </div>
        </section>
    </main>

    <footer>
        <img src="images/logo.svg" alt="fail" id="footerlogo">
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

<!--detail.php-->
<?php
require('connect.php');
require('header.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
$product_id = $_GET['id']; 

// 检查是否有错误消息需要显示
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']); // 清除错误消息

// 处理评论提交
if (isset($_POST['submit_comment'])) {

    $comment_name = empty($_POST['comment_name']) ? 'Anonymous User' : $_POST['comment_name'];
    $comment_text = $_POST['comment_text'];

    // 验证验证码
    if ($_POST['captcha'] !== $_SESSION['captcha']) {
        $_SESSION['error'] = "Invalid CAPTCHA. Please try again.";
        $_SESSION['saved_input'] = $_POST;
        header('Location: detail.php?id=' . $product_id); // 重新加载页面
        exit();
    }

    // 检查用户名是否已存在
    $checkQuery = "SELECT * FROM Comments WHERE username = :username";
    $stmt = $db->prepare($checkQuery);
    $stmt->bindParam(':username', $comment_name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Username is already taken, please choose another username.";
    } else {
        // 插入评论到数据库
        $insertQuery = "INSERT INTO Comments (product_id, user_id, comment_content, comment_date, username) VALUES (:product_id, :user_id, :comment_content, NOW(), :username)";
        $stmt = $db->prepare($insertQuery);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_id', $userId); // 确保这里 $userId 已经正确定义
        $stmt->bindParam(':comment_content', $comment_text);
        $stmt->bindParam(':username', $comment_name);
        $stmt->execute();

        // 重定向以避免表单重复提交
        header('Location: detail.php?id=' . $product_id);
        exit();
    }
}

// 获取产品信息
$query = "SELECT Products.*, Images.filename FROM Products LEFT JOIN Images ON Products.product_id = Images.product_id WHERE Products.product_id = :product_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    

</head>
<body>
    <?php if ($product): ?>
        <!-- 产品详情 -->
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p><strong>Description:</strong> <?php echo $product['product_description'] ?? 'No description'; ?></p>
        <p><strong>Price: </strong>$<?php echo htmlspecialchars($product['product_price']); ?></p>
        <p><strong>Stock Quantity: </strong><?php echo htmlspecialchars($product['stock_quantity']); ?></p>
        <p><strong>Category ID: </strong><?php echo htmlspecialchars($product['category_id']); ?></p>
        <?php if ($product['filename']): ?>
            <img src="upload/<?php echo htmlspecialchars($product['filename']); ?>" alt="Product Image">
        <?php endif; ?>

        <!-- 评论表单 -->
        <h2>Comment Area</h2>
        <form method="post" action="">
            <label for="comment_name">Name:</label>
            <input type="text" id="comment_name" name="comment_name" value="<?php echo !empty($error) && isset($_SESSION['saved_input']['comment_name']) ? htmlspecialchars($_SESSION['saved_input']['comment_name']) : ''; ?>">

            <label for="comment_text">Comment:</label>
            <textarea id="comment_text" name="comment_text" required><?php echo !empty($error) && isset($_SESSION['saved_input']['comment_text']) ? htmlspecialchars($_SESSION['saved_input']['comment_text']) : ''; ?></textarea>

            <!-- 验证码部分 -->
            <label for="captcha">Please enter the captcha:</label>
            <img src="captcha.php" alt="CAPTCHA Image" id="captcha_image">
            <button type="button" id="refresh_captcha">Refresh</button>
            <input type="text" id="captcha" name="captcha" required>

            <?php if (!empty($error)): ?>
                <p class="error" style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <input type="submit" name="submit_comment" value="Submit Comment">
        </form>

        <!-- 显示评论 -->
        <?php
            $commentQuery = "SELECT * FROM Comments WHERE product_id = :product_id AND is_hidden = FALSE ORDER BY comment_date DESC";
            $stmt = $db->prepare($commentQuery);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        
            foreach ($comments as $comment) {
                echo "<div class='comment'>";
                if ($comment['user_id'] === null) {
                    echo "<p><strong>" . htmlspecialchars($comment['username']) . " </strong></p>";
                } else {
                    echo "<p><strong>Admin:</strong></p>";
                }
                echo "<p>" . htmlspecialchars($comment['comment_content']) . "</p>";
                // 显示评论时间
                echo "<p class='comment-date'>Commented on: " . htmlspecialchars($comment['comment_date']) . "</p>";
                echo "</div>";
            }
        ?>
    <?php else: ?>
        <p>Product not found.</p>
    <?php endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('refresh_captcha').addEventListener('click', function() {
                var captchaImage = document.getElementById('captcha_image');
                captchaImage.src = 'captcha.php?' + new Date().getTime();
            });
        });
    </script>
</body>
</html>

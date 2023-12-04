<!--detail.php-->
<?php
require('connect.php');
require('header.php');

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);  */

$product_id = $_GET['id'] ?? null;

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);

if (isset($_POST['submit_comment'])) {

    $comment_name = empty($_POST['comment_name']) ? 'Anonymous User' : $_POST['comment_name'];
    $comment_text = $_POST['comment_text'];

    if ($_POST['captcha'] !== $_SESSION['captcha']) {
        $_SESSION['error'] = "Invalid CAPTCHA. Please try again.";
        $_SESSION['saved_input'] = $_POST;
        header('Location: detail.php?id=' . $product_id);
        exit();
    }

    $insertQuery = "INSERT INTO Comments (product_id, user_id, comment_content, comment_date, username) VALUES (:product_id, :user_id, :comment_content, NOW(), :username)";
    $stmt = $db->prepare($insertQuery);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':user_id', $userId); 
    $stmt->bindParam(':comment_content', $comment_text);
    $stmt->bindParam(':username', $comment_name);
    $stmt->execute();

    header('Location: detail.php?id=' . $product_id);
    exit();
}


$query = "SELECT Products.*, Images.filename FROM Products LEFT JOIN Images ON Products.product_id = Images.product_id WHERE Products.product_id = :product_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
?>


    <?php if ($product): ?>
        <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p><strong>Description:</strong> <?php echo $product['product_description'] ?? 'No description'; ?>
        <p><strong>Price: </strong>$<?php echo htmlspecialchars($product['product_price']); ?></p>
        <p><strong>Stock Quantity: </strong><?php echo htmlspecialchars($product['stock_quantity']); ?></p>
        <p><strong>Category ID: </strong><?php echo htmlspecialchars($product['category_id']); ?></p>
        <?php if ($product['filename']): ?>
            <img src="upload/<?php echo htmlspecialchars($product['filename']); ?>" alt="Product Image">
        <?php endif; ?>

        <h2>Comment Area</h2>
        <form method="post" action="detail.php?id=<?php echo $product_id; ?>">
            <label for="comment_name">Name (optional):</label>
            <input type="text" id="comment_name" name="comment_name" value="<?php echo !empty($error) && isset($_SESSION['saved_input']['comment_name']) ? htmlspecialchars($_SESSION['saved_input']['comment_name']) : ''; ?>">
            <label for="comment_text">Comment:</label>
            <textarea id="comment_text" name="comment_text" required><?php echo !empty($error) && isset($_SESSION['saved_input']['comment_text']) ? htmlspecialchars($_SESSION['saved_input']['comment_text']) : ''; ?></textarea>

            <label for="captcha">Please enter the captcha:</label>
            <img src="captcha.php" alt="CAPTCHA Image" id="captcha_image">
            <button type="button" id="refresh_captcha">Refresh</button>
            <input type="text" id="captcha" name="captcha" required>

            <?php if (!empty($error)): ?>
                <p class="error" style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <input type="submit" name="submit_comment" value="Submit Comment">
        </form>

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
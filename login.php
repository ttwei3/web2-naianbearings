<?php
require('connect.php'); 
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = ''; // 初始化错误消息变量

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'incorrectpassword') {
        $error_message = 'Incorrect password';
    } elseif ($_GET['error'] == 'usernotfound') {
        $error_message = 'User does not exist.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE user_name = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $hashedPassword = $user['user_password'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $user['role'] == 'admin'; 
            $_SESSION['success_message'] = "Login successful! Welcome back, {$username}!";

            if ($_SESSION['is_admin']) {
                header('Location: admin.php');
                exit();
            } else {
                header('Location: index.php');
                exit();
            }
        } else {
            header('Location: login.php?error=incorrectpassword');
            exit();
        }
    } else {
        header('Location: login.php?error=usernotfound');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login</title>
    <link rel="icon" href="./images/page-logo.svg" type="image/x-icon">
</head>
<body>
    <h2>Login</h2>
    <?php if (!empty($error_message)) : ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>

<!-- login.php-->
<?php
require('connect.php');
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'incorrectpassword') {
        echo '<p>Incorrect password</p>';
    } elseif ($_GET['error'] == 'usernotfound') {
        echo '<p>User does not exist.</p>';
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
        // Verify the password
        $hashedPassword = $user['user_password'];
        if (password_verify($password, $hashedPassword)) {
            // Password is correct
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $username;
            // Check if the user is an admin
            $_SESSION['is_admin'] = $user['role'] == 'admin'; // Replace 'role' with the actual column name for user roles in your database
    
            if ($_SESSION['is_admin']) {
                header('Location: admin.php');
                exit();
            } else {
                header('Location: index.php');
                exit();
            }
            
        } else {
            // Password is incorrect
            header('Location: login.php?error=incorrectpassword');
            exit();
        }
    } else {
        // User not found
        header('Location: login.php?error=usernotfound');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>

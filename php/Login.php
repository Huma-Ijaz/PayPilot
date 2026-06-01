<?php
session_start();
require __DIR__ . '/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // Find user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check password
    if ($user && password_verify($password, $user['password'])) {
        
        // Start session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];

        // Remember Me cookie for 7 days
        if ($remember) {
            setcookie('remember_email', $email, time() + 604800);
        }

        // Go to dashboard
        header('Location: Dashboard.html');
        exit;

    } else {
        // Wrong credentials
        header('Location: login.html?error=Invalid email or password');
        exit;
    }
}
?>
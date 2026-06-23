<?php
// auth.php
session_start();
require 'includes/db.php'; 

// --- Handle Registration ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    // AUTOMATED SYSTEM: Check how many users exist
    $countStmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $countStmt->fetchColumn();

    // The very first user is a superuser. Everyone else is a customer.
    $role = ($userCount == 0) ? 'superuser' : 'customer'; 

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password, $role]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Registration failed: " . $e->getMessage()); // Prints EXACT error so we aren't guessing
    }
}

// --- Handle Login & Cookies ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // 1. Set Sessions
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // 2. Set Cookies if "Remember Me" was checked (Lasts for 30 days)
        if (isset($_POST['remember_me'])) {
            setcookie('techstore_user', $user['id'], time() + (86400 * 30), "/");
            setcookie('techstore_role', $user['role'], time() + (86400 * 30), "/");
        }
        
        header("Location: dashboard.php");
        exit();
    } else {
        die("Invalid email or password. Please go back and try again.");
    }
}
?>
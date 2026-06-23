<?php
// superuser.php
session_start();

// Strict Security Check: ONLY Superusers allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superuser') {
    header("Location: dashboard.php"); 
    exit();
}

require 'includes/db.php';

// --- CREATE NEW USER ---
// --- CREATE NEW USER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password, $role]);
        header("Location: superuser.php?msg=user_added");
        exit();
    } catch (PDOException $e) {
        // Now it will print the EXACT database error on the screen
        $error = "Failed to create user. Error: " . $e->getMessage();
    }
}

// --- DELETE USER ---
if (isset($_GET['delete_user_id'])) {
    // Prevent the superuser from deleting themselves!
    if ($_GET['delete_user_id'] != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_GET['delete_user_id']]);
    }
    header("Location: superuser.php?msg=user_deleted");
    exit();
}

// --- FETCH ALL USERS ---
$stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC");
$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super User - Access Management</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f3f4f6; padding: 40px 20px; color: #374151; }
        .wrapper { max-width: 900px; margin: 0 auto; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full { grid-column: span 2; }
        input, select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
        .btn-primary { background: #8b5cf6; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; }
        .btn-danger { background: #ef4444; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; color: white; }
        .bg-super { background: #8b5cf6; }
        .bg-admin { background: #ef4444; }
        .bg-manager { background: #f59e0b; }
        .bg-customer { background: #10b981; }
    </style>
</head>
<body>
<div class="wrapper">
    <header>
        <h2>Super User Control Panel</h2>
        <div>
            <a href="dashboard.php" style="margin-right: 15px; text-decoration: none; color: #2563eb; font-weight: bold;">Back to Store</a>
            <a href="logout.php" class="btn-danger">Log Out</a>
        </div>
    </header>

    <?php if(isset($error)) echo "<p style='color:red; margin-bottom:15px;'>$error</p>"; ?>

    <div class="card">
        <h3 style="margin-bottom: 20px;">Create New User Account</h3>
        <form method="POST">
            <input type="hidden" name="action" value="create_user">
            <div><label>Username</label><input type="text" name="username" required></div>
            <div><label>Email</label><input type="email" name="email" required></div>
            <div><label>Password</label><input type="password" name="password" required></div>
            <div>
                <label>System Role</label>
                <select name="role" required>
                    <option value="customer">Customer (Standard)</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                    <option value="superuser">Super User</option>
                </select>
            </div>
            <button type="submit" class="btn-primary full">Create Account</button>
        </form>
    </div>

    <div class="card" style="padding: 0;">
        <table>
            <tr><th>User ID</th><th>Username</th><th>Email</th><th>Role</th><th>Action</th></tr>
            <?php foreach ($all_users as $user): ?>
            <tr>
                <td>#<?php echo $user['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <span class="badge 
                        <?php 
                            if($user['role'] == 'superuser') echo 'bg-super';
                            elseif($user['role'] == 'admin') echo 'bg-admin';
                            elseif($user['role'] == 'manager') echo 'bg-manager';
                            else echo 'bg-customer';
                        ?>">
                        <?php echo strtoupper($user['role']); ?>
                    </span>
                </td>
                <td>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <a href="?delete_user_id=<?php echo $user['id']; ?>" class="btn-danger" style="padding: 4px 8px; font-size:12px;" onclick="return confirm('Delete this user completely?')">Delete</a>
                    <?php else: ?>
                        <span style="color: #9ca3af; font-size: 12px;">(You)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>
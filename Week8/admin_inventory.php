<?php
// admin_inventory.php
session_start();

// Check if user is logged in and is AT LEAST a manager/admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'superuser')) {
    header("Location: dashboard.php"); 
    exit();
}

require 'includes/db.php'; // Connection string in ONE file

// --- EXPORT TO EXCEL (CSV) ---
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=inventory_report.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Product Name', 'Price (Ksh)', 'Image URL', 'Description'));
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// --- CREATE OPERATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $stmt = $pdo->prepare("INSERT INTO products (name, price, image_url, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['price'], $_POST['image_url'], $_POST['description']]);
    header("Location: admin_inventory.php");
    exit();
}

// --- UPDATE OPERATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, image_url=?, description=? WHERE id=?");
    $stmt->execute([$_POST['name'], $_POST['price'], $_POST['image_url'], $_POST['description'], $_POST['id']]);
    header("Location: admin_inventory.php");
    exit();
}

// --- DELETE OPERATION ---
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: admin_inventory.php");
    exit();
}

// --- READ OPERATION ---
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if we are editing an item
$edit_item = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Admin & Reporting</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background-color: #f3f4f6; padding: 40px 20px; color: #374151; }
        .wrapper { max-width: 1000px; margin: 0 auto; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full { grid-column: span 2; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
        
        .btn-primary { background: #2563eb; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; text-align: center; text-decoration: none;}
        .btn-update { background: #10b981; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%;}
        .btn-danger { background: #ef4444; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .btn-edit { background: #f59e0b; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .btn-del { background: #ef4444; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; }
        
        .report-bar { display: flex; gap: 10px; margin-bottom: 15px; }
        .btn-excel { background: #10b981; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;}
        .btn-pdf { background: #6366f1; color: white; border: none; padding: 8px 16px; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 14px;}

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        
        /* Hide UI elements when printing PDF */
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .card { box-shadow: none; padding: 0; }
            table { border: 1px solid #000; }
            th, td { border: 1px solid #000; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header class="no-print">
        <h2>Admin: Inventory & Reporting</h2>
        <div>
            <a href="dashboard.php" style="margin-right: 15px; text-decoration: none; color: #2563eb; font-weight: bold;">Back to Store</a>
            <a href="logout.php" class="btn-danger">Log Out</a>
        </div>
    </header>

    <div class="card no-print">
        <?php if ($edit_item): ?>
            <h3 style="margin-bottom: 20px; color: #10b981;">Update Hardware: <?php echo htmlspecialchars($edit_item['name']); ?></h3>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <div><label>Hardware Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($edit_item['name']); ?>" required></div>
                <div><label>Price (Ksh)</label><input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($edit_item['price']); ?>" required></div>
                <div class="full"><label>Image Filename</label><input type="text" name="image_url" value="<?php echo htmlspecialchars($edit_item['image_url']); ?>" required></div>
                <div class="full"><label>Description</label><textarea name="description" rows="3" required><?php echo htmlspecialchars($edit_item['description']); ?></textarea></div>
                <div class="full" style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-update">Save Updates</button>
                    <a href="admin_inventory.php" class="btn-danger" style="text-align: center; width: 100%; padding: 12px; display: block;">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <h3 style="margin-bottom: 20px;">Add New Hardware</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div><label>Hardware Name</label><input type="text" name="name" required></div>
                <div><label>Price (Ksh)</label><input type="number" step="0.01" name="price" required></div>
                <div class="full"><label>Image Filename</label><input type="text" name="image_url" required></div>
                <div class="full"><label>Description</label><textarea name="description" rows="3" required></textarea></div>
                <button type="submit" class="btn-primary full">Save to Database</button>
            </form>
        <?php endif; ?>
    </div>

    <div class="card" style="padding: 0;">
        <div style="padding: 20px;" class="no-print">
            <h3 style="margin-bottom: 15px;">Inventory Report Generation</h3>
            <div class="report-bar">
                <a href="?export=csv" class="btn-excel">📥 Export to Excel (CSV)</a>
                <button onclick="window.print()" class="btn-pdf">🖨️ Save as PDF</button>
            </div>
        </div>
        <table>
            <tr><th>ID</th><th>Name</th><th>Price</th><th>Image File</th><th class="no-print">Action</th></tr>
            <?php foreach ($products as $row): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                <td>Ksh <?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['image_url']); ?></td>
                <td class="no-print">
                    <a href="?edit_id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Delete this item?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>
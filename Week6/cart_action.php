<?php
session_start();

// Initialize the cart array if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id > 0) {
        if ($action === 'add') {
            $quantity = (int)($_POST['quantity'] ?? 1);
            // If item exists, add to quantity; otherwise, set it
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
        } 
        elseif ($action === 'update') {
            $quantity = (int)($_POST['quantity'] ?? 1);
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        } 
        elseif ($action === 'remove') {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    // Redirect back to the page the user came from (Dashboard or Cart)
    $referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
    header("Location: $referer");
    exit();
}
?>
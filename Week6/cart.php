<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'includes/db.php';

// Initialize cart array
$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$cart_total = 0;
$total_items = 0;

// Fetch product details for items in the cart
if (!empty($cart)) {
    // Create placeholders for the IN clause (e.g., ?, ?, ?)
    $in_clause = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in_clause)");
    $stmt->execute(array_keys($cart));
    $fetched_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Map quantities and calculate totals
    foreach ($fetched_products as $product) {
        $pid = $product['id'];
        $qty = $cart[$pid];
        $product['cart_quantity'] = $qty;
        $product['subtotal'] = $product['price'] * $qty;
        
        $cart_items[] = $product;
        $cart_total += $product['subtotal'];
        $total_items += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - TechStore</title>
    <style>
        /* Base Reset */
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
        }

        body { 
            background-color: #f1f1f2; /* Light e-commerce grey */
            color: #282828;
        }

        /* Simplified Nav for Cart Page */
        nav { 
            background-color: #111827; 
            color: white; 
            padding: 1rem 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }

        .logo a { 
            color: white; 
            text-decoration: none; 
            font-size: 1.5rem; 
            font-weight: bold; 
        }

        .back-link {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Cart Layout Engine */
        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 5%;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            align-items: start;
        }

        /* Cart Items Section */
        .cart-main {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        .cart-header {
            font-size: 1.5rem;
            font-weight: 600;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e2e2;
            margin-bottom: 1.5rem;
        }

        /* Individual Item Card */
        .cart-item {
            display: flex;
            gap: 20px;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e2e2e2;
            margin-bottom: 1.5rem;
        }

        .cart-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .item-image {
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: #f9fafb;
            border-radius: 6px;
            padding: 10px;
        }

        .item-details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-title {
            font-size: 1.1rem;
            color: #111827;
            margin-bottom: 5px;
        }

        .item-stock {
            color: #10b981; /* Green */
            font-size: 0.85rem;
            font-weight: 500;
        }

        .item-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #282828;
            margin-top: 10px;
        }

        /* Actions inside item (Remove / Qty) */
        .item-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .btn-remove {
            background: none;
            border: none;
            color: #ef4444;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }

        .btn-remove:hover {
            color: #b91c1c;
        }

        /* Quantity Form */
        .qty-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-input {
            width: 60px;
            padding: 6px;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-weight: 600;
        }

        .btn-update {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: background 0.2s;
        }

        .btn-update:hover {
            background: #e5e7eb;
        }

        /* Order Summary Sidebar */
        .cart-sidebar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 1.5rem;
            position: sticky;
            top: 100px; /* Stays in view while scrolling items */
        }

        .summary-header {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e2e2;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: #4b5563;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.25rem;
            font-weight: bold;
            color: #111827;
            padding-top: 1rem;
            border-top: 1px solid #e2e2e2;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn-checkout {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: #f68b1e; /* High converting Orange */
            color: white;
            text-align: center;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 6px rgba(246, 139, 30, 0.2);
        }

        .btn-checkout:hover {
            background-color: #e07b19;
        }

        /* Empty Cart State */
        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-cart h2 {
            margin-bottom: 10px;
        }

        .empty-cart p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .btn-shop {
            display: inline-block;
            background: #111827;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Responsive Breakpoint */
        @media (max-width: 850px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
            .cart-sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo"><a href="dashboard.php">TechStore</a></div>
        <a href="dashboard.php" class="back-link">← Continue Shopping</a>
    </nav>

    <div class="cart-container">
        
        <!-- Left Column: Cart Items -->
        <main class="cart-main">
            <h1 class="cart-header">Cart (<?php echo $total_items; ?> Items)</h1>

            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <svg viewBox="0 0 24 24" width="64" height="64" fill="#d1d5db" style="margin-bottom: 1rem;"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    <h2>Your cart is empty!</h2>
                    <p>Browse our categories and discover our best deals.</p>
                    <a href="dashboard.php" class="btn-shop">START SHOPPING</a>
                </div>
            <?php else: ?>
                
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="../Images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product" class="item-image">
                        
                        <div class="item-details">
                            <div>
                                <h3 class="item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <span class="item-stock">In Stock</span>
                                <div class="item-price">Ksh <?php echo number_format($item['price'], 2); ?></div>
                            </div>
                            
                            <div class="item-actions">
                                <!-- Remove Form -->
                                <form method="POST" action="cart_action.php">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn-remove">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                        Remove
                                    </button>
                                </form>

                                <!-- Quantity Update Form -->
                                <form method="POST" action="cart_action.php" class="qty-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['cart_quantity']; ?>" min="1" class="qty-input">
                                    <button type="submit" class="btn-update">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </main>

        <!-- Right Column: Order Summary -->
        <aside class="cart-sidebar">
            <h2 class="summary-header">Order Summary</h2>
            
            <div class="summary-row">
                <span>Items total (<?php echo $total_items; ?>)</span>
                <span>Ksh <?php echo number_format($cart_total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery fees</span>
                <span>Calculated at checkout</span>
            </div>

            <div class="summary-total">
                <span>Total</span>
                <span>Ksh <?php echo number_format($cart_total, 2); ?></span>
            </div>

            <?php if (!empty($cart_items)): ?>
                <button class="btn-checkout" onclick="alert('Checkout integration coming next!')">CHECKOUT (Ksh <?php echo number_format($cart_total, 2); ?>)</button>
            <?php else: ?>
                <button class="btn-checkout" style="background: #d1d5db; cursor: not-allowed;" disabled>CHECKOUT</button>
            <?php endif; ?>
        </aside>

    </div>

</body>
</html>
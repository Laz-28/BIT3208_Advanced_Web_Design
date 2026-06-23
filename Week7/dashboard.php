<?php
// dashboard.php
session_start();

// 1. Security Check: Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// 2. Database Connection
require 'includes/db.php';

// 3. READ Operation: Fetch all active products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Cart Logic: Calculate total items for the navigation badge
$total_cart_items = 0;
if (isset($_SESSION['cart'])) {
    $total_cart_items = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Dashboard</title>
    <style>
        /* --- Base Reset --- */
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
        }

        body { 
            background-color: #f3f4f6; 
        }
        
        /* --- Dark Theme Navigation --- */
        nav { 
            background-color: #111827; 
            color: white; 
            padding: 1rem 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            gap: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo { 
            font-size: 1.5rem; 
            font-weight: bold; 
            flex-shrink: 0; 
        }

        /* --- Search Bar --- */
        .search-container {
            display: flex;
            flex-grow: 1;
            max-width: 500px;
            background-color: white;
            border-radius: 6px; 
            overflow: hidden;
        }

        .search-container input {
            flex-grow: 1;
            padding: 10px 15px;
            border: none;
            outline: none;
            font-size: 0.95rem;
            color: #111827;
        }

        .search-container button {
            background-color: #f68b1e; 
            color: white;
            border: none;
            padding: 0 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-container button:hover { 
            background-color: #e07b19; 
        }

        /* --- Nav Icons (Help, Cart) --- */
        .nav-actions { 
            display: flex; 
            gap: 20px; 
            align-items: center; 
        }

        .action-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .action-item:hover { 
            color: #f68b1e; 
        }

        .action-item svg { 
            width: 18px; 
            height: 18px; 
            fill: white; 
            transition: fill 0.2s; 
        }

        .action-item:hover svg { 
            fill: #f68b1e; 
        }

        /* --- User Controls --- */
        .user-controls { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            border-left: 1px solid #374151; 
            padding-left: 15px; 
        }

        .admin-link { 
            color: #60a5fa; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 0.9rem;
        }

        .welcome-text { 
            font-size: 0.95rem; 
        }

        .logout-btn { 
            background-color: #ef4444; 
            color: white; 
            padding: 8px 16px; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .logout-btn:hover { 
            background-color: #dc2626; 
        }

        /* --- Hero Section --- */
        .hero { 
            background-color: #2563eb; 
            color: white; 
            text-align: center; 
            padding: 2rem 2rem; 
        }

        .hero h1 { 
            font-size: 2rem; 
            margin-bottom: 0.5rem; 
        }

        .hero p { 
            font-size: 1rem; 
            opacity: 0.9; 
        }
        
        /* --- Main Layout --- */
        .main-container { 
            display: flex; 
            padding: 2rem 5%; 
            gap: 2rem; 
            align-items: flex-start; 
        }

        /* --- Sidebar Navigation --- */
        .sidebar { 
            width: 220px; 
            background: white; 
            padding: 1.5rem; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            flex-shrink: 0; 
        }

        .sidebar h3 { 
            font-size: 1.1rem; 
            color: #111827; 
            margin-bottom: 1rem; 
            padding-bottom: 0.5rem; 
            border-bottom: 2px solid #f3f4f6; 
        }

        .sidebar ul { 
            list-style: none; 
        }

        .sidebar li { 
            margin-bottom: 0.8rem; 
        }

        .sidebar a { 
            text-decoration: none; 
            color: #4b5563; 
            font-weight: 500; 
            display: block; 
            transition: color 0.2s; 
        }

        .sidebar a:hover { 
            color: #2563eb; 
        }

        /* --- Product Grid --- */
        .products { 
            flex-grow: 1; 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 1.5rem; 
        }

        .card { 
            background: white; 
            padding: 1.5rem; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            text-align: center; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            transition: transform 0.2s; 
        }

        .card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 6px 12px rgba(0,0,0,0.1); 
        }

        .card img { 
            width: 100%; 
            height: 160px; 
            object-fit: contain; 
            padding: 10px; 
            margin-bottom: 1rem; 
        }

        .card h3 {
            font-size: 1.1rem;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .description { 
            font-size: 0.9rem; 
            color: #6b7280; 
            margin-bottom: 15px; 
        }

        .price { 
            font-size: 1.25rem; 
            font-weight: bold; 
            color: #2563eb; 
            margin: 10px 0; 
        }

        .buy-btn { 
            width: 100%; 
            padding: 10px; 
            background-color: #111827; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600; 
            transition: background-color 0.2s;
        }

        .buy-btn:hover { 
            background-color: #1f2937; 
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">TechStore</div>
        
        <form class="search-container" action="#" method="GET">
            <input type="text" name="query" placeholder="Search components...">
            <button type="submit">Search</button>
        </form>

        <div class="nav-actions">
            <a href="#" class="action-item">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
                Help
            </a>
            <a href="cart.php" class="action-item">
                <svg viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                Cart (<?php echo $total_cart_items; ?>)
            </a>
        </div>

        <div class="user-controls">
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'superuser'): ?>
                <a href="superuser.php" class="admin-link" style="color: #8b5cf6;">System Admin</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'manager', 'superuser'])): ?>
                <a href="admin_inventory.php" class="admin-link">Inventory</a>
            <?php endif; ?>

            <span class="welcome-text">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
        
    </nav>

    <header class="hero">
        <h1>High-Performance Hardware</h1>
        <p>Explore our latest arrivals in gaming and LLM compute.</p>
    </header>

    <div class="main-container">
        
        <aside class="sidebar">
            <h3>Categories</h3>
            <ul>
                <li><a href="?category=gpus">GPUs</a></li>
                <li><a href="?category=pcs">PCs</a></li>
                <li><a href="?category=laptops">Laptops</a></li>
                <li><a href="?category=monitors">Monitors</a></li>
                <li><a href="?category=storage_ram">Storage & RAM</a></li>
                <li><a href="?category=motherboards">Motherboards</a></li>
                <li><a href="?category=power_supply">Power Supply</a></li>
                <li><a href="?category=case">Case</a></li>
            </ul>
        </aside>

        <section class="products">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $row): ?>
                    <div class="card">
                        <img src="../Images/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div>
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="price">Ksh <?php echo htmlspecialchars(number_format($row['price'], 2)); ?></p>
                        </div>
                        
                        <form action="cart_action.php" method="POST" style="margin-top: auto;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="buy-btn">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; font-size: 1.2rem; color: #6b7280;">Inventory is currently empty.</p>
            <?php endif; ?>
        </section>

    </div>

</body>
</html>
<?php
// dashboard.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'includes/db.php';

// Fetch products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechStore Dashboard</title>

<style>
    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body{
        background:#f4f6f9;
        color:#333;
    }

    /* NAVIGATION */
    nav{
        background:#111827;
        color:white;
        padding:15px 5%;
        display:flex;
        justify-content:space-between;
        align-items:center;
        position:sticky;
        top:0;
        z-index:100;
        box-shadow:0 2px 10px rgba(0,0,0,0.1);
    }

    .logo{
        font-size:1.7rem;
        font-weight:700;
        color:#60a5fa;
    }

    .user-controls{
        display:flex;
        align-items:center;
        gap:15px;
        flex-wrap:wrap;
    }

    .admin-link{
        color:#60a5fa;
        text-decoration:none;
        font-weight:600;
    }

    .logout-btn{
        background:#ef4444;
        color:white;
        padding:8px 16px;
        border-radius:6px;
        text-decoration:none;
        font-weight:600;
        transition:.3s;
    }

    .logout-btn:hover{
        background:#dc2626;
    }

    /* HERO */
    .hero{
        background:linear-gradient(135deg,#2563eb,#1d4ed8);
        color:white;
        text-align:center;
        padding:60px 20px;
    }

    .hero h1{
        font-size:2.7rem;
        margin-bottom:10px;
    }

    .hero p{
        font-size:1.1rem;
        opacity:.9;
    }

    /* PRODUCTS SECTION */
    .products{
    padding: 50px 5%;
    display: grid;
    grid-template-columns: repeat(auto-fit, 320px);
    justify-content: center;
    gap: 30px;
}

.card{
    width: 320px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    transition: all .3s ease;
}

.card img{
    width: 100%;
    height: 220px;
    object-fit: contain;
    padding: 15px;
    background: #fafafa;
}

    .card-content{
        padding:15px;
        flex-grow:1;
        display:flex;
        flex-direction:column;
    }

    .card h3{
        font-size:1rem;
        color:#111827;
        margin-bottom:8px;
    }

    .description{
        font-size:0.85rem;
        color:#6b7280;
        line-height:1.4;
        height:55px;
        overflow:hidden;
        margin-bottom:12px;
    }

    .price{
        font-size:1.15rem;
        font-weight:700;
        color:#2563eb;
        margin-bottom:15px;
    }

    .buy-btn{
        margin-top:auto;
        width:100%;
        border:none;
        background:#111827;
        color:white;
        padding:10px;
        border-radius:6px;
        cursor:pointer;
        font-weight:600;
        transition:.3s;
    }

    .buy-btn:hover{
        background:#2563eb;
    }

    .empty-message{
        grid-column:1/-1;
        text-align:center;
        font-size:1.2rem;
        color:#6b7280;
        padding:40px;
    }

    footer{
        text-align:center;
        padding:25px;
        background:white;
        color:#6b7280;
        border-top:1px solid #ddd;
    }

    @media(max-width:768px){

        nav{
            flex-direction:column;
            gap:10px;
        }

        .hero h1{
            font-size:2rem;
        }

        .products{
            grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
            gap:20px;
        }

        .card{
            max-width:210px;
        }

        .card img{
            height:140px;
        }
    }
</style>

</head>
<body>

<nav>
    <div class="logo">TechStore</div>

    <div class="user-controls">

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin_inventory.php" class="admin-link">
                Inventory Admin
            </a>
        <?php endif; ?>

        <span>
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </span>

        <a href="logout.php" class="logout-btn">
            Log Out
        </a>
    </div>
</nav>

<header class="hero">
    <h1>High-Performance Hardware</h1>
    <p>Gaming GPUs, AI Workstations, CPUs and Enterprise Components.</p>
</header>

<section class="products">

    <?php if (!empty($products)): ?>

        <?php foreach ($products as $row): ?>

            <div class="card">

                <img
                    src="../Images/<?php echo htmlspecialchars($row['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($row['name']); ?>"
                >

                <div class="card-content">

                    <h3>
                        <?php echo htmlspecialchars($row['name']); ?>
                    </h3>

                    <p class="description">
                        <?php echo htmlspecialchars($row['description']); ?>
                    </p>

                    <p class="price">
                        Ksh <?php echo number_format($row['price'], 2); ?>
                    </p>

                    <button class="buy-btn">
                        Add to Cart
                    </button>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="empty-message">
            Inventory is currently empty.
        </div>

    <?php endif; ?>

</section>

<footer>
    © <?php echo date('Y'); ?> TechStore. All rights reserved.
</footer>

</body>
</html>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Store Landing</title>
    <style>
        * { box-sizing: border-box;
          margin: 0;
          padding: 0;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { 
            background-color: #f3f4f6;
         }
        
        
        nav { background-color: #111827;
          color: white;
          padding: 1rem 5%; 
          display: flex; 
          justify-content: space-between; 
          align-items: center; 
        }

        .logo {
            font-size: 1.5rem; 
            font-weight: bold; 
        }
        .user-controls { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
        }
        .logout-btn { 
            background-color: #ef4444; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            font-weight: 600; 
        }
        
        
        .hero { 
            background-color: #2563eb; 
            color: white; 
            text-align: center; 
            padding: 4rem 2rem; 
        }

        .hero h1 {
             font-size: 2.5rem; 
             margin-bottom: 1rem; 
        }
        
        
        .products { 
            padding: 4rem 5%; 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); 
            gap: 2rem; 
        }
        .card { 
            background: white; 
            padding: 1.5rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            text-align: center; 
        }

        .card img { 
            width: 100%; 
            height: 200px; 
            object-fit: contain; 
            padding: 10px;
            border-radius: 8px; 
            margin-bottom: 1rem; 
            background-color: #f9fafb; 
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
        }

    </style>
</head>
<body>

    <nav>
        <div class="logo">CODKA</div>
        <div class="user-controls">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </nav>

    <header class="hero">
        <h1>Next-Gen Tech, Delivered.</h1>
        <p>Explore our latest arrivals in high-performance computing.</p>
    </header>

    <section class="products">
        <div class="card">
            <img src="./Images/wks.webp" alt="Laptop">
            <h3>ProBook Workstation</h3>
            <p class="price">Ksh 129,900</p>
            <button class="buy-btn">Add to Cart</button>
        </div>
        
        <div class="card">
            <img src="./Images/noisecancelling.avif" alt="Headphones">
            <h3>Noise-Canceling Pods</h3>
            <p class="price">Ksh 19,900</p>
            <button class="buy-btn">Add to Cart</button>
        </div>

        <div class="card">
            <img src="./Images/4k.jpeg" alt="Monitor">
            <h3>4K Ultra Display</h3>
            <p class="price">Ksh 39,900</p>
            <button class="buy-btn">Add to Cart</button>
        </div>

        <div class="card">
            <img src="./Images/Asus.jpeg" alt="Asus Nvidia 1050">
            <h3>Asus GTX 1050ti</h3>
            <p class="price">Ksh 15,000</p>
            <button class="buy-btn">Add to Cart</button>

        </div>

        <div class="card">
            <img src="./Images/l13.webp" alt="Laptop">
            <h3>Lenovo L13 Yoga</h3>
            <p class="price">Ksh 45,900</p>
            <button class="buy-btn">Add to Cart</button>
        </div>
        
        <div class="card">
            <img src="./Images/macmini.webp" alt="Mac Mini">
            <h3>Apple Mac Mini</h3>
            <p class="price">Ksh 69,900</p>
            <button class="buy-btn">Add to Cart</button>
        </div>

        <div class="card">
            <img src="./Images/13pro.webp" alt="Iphone 13 pro">
            <h3>iPhone 13 Pro</h3>
            <p class="price">Ksh 69,900</p>
            <button class="buy-btn">Add to Cart</button>
        </div>

        <div class="card">
            <img src="./Images/1060.jpeg" alt="Nvidi 1060">
            <h3>Nvidia GTX 1060 8GB</h3>
            <p class="price">Ksh 18,000</p>
            <button class="buy-btn">Add to Cart</button>

        </div>

        <div class="card">
            <img src="./Images/oraimo.webp" alt="Oraimo free pods">
            <h3>Oraimo Free Pods Neo</h3>
            <p class="price">Ksh 2,000</p>
            <button class="buy-btn">Add to Cart</button>

        </div>


        
    </section>

</body>
</html>
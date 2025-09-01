<?php
session_start();
include 'includes/db.php';

// Fetch products
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store | Home</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- External CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Inline CSS for quick view -->
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #ff6b6b;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --success: #2ecc71;
            --shadow: 0 5px 15px rgba(0,0,0,0.07);
            --border-radius: 12px;
        }
        
        * {
            margin: 0; 
            padding: 0; 
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Header Styles */
        header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1300px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.8rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .logo i {
            color: var(--secondary);
            margin-right: 10px;
            font-size: 2rem;
        }
        
        nav {
            display: flex;
            align-items: center;
        }
        
        nav a, nav button {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        nav a:hover {
            transform: translateY(-3px);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        nav i {
            margin-right: 6px;
            font-size: 1.1rem;
        }
        
        .cart-icon {
            margin-right: 5px;
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--secondary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .logout-button {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 15px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .logout-button:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            margin-bottom: 50px;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-content h2 {
            font-size: 3rem;
            margin-bottom: 15px;
            animation: fadeInDown 1s;
        }
        
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            animation: fadeIn 1.5s;
        }
        
        .shop-now-btn {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            animation: fadeInUp 2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .shop-now-btn:hover {
            background-color: #ff5252;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255,107,107,0.4);
        }

        /* Main Content */
        .main-container {
            max-width: 1300px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .section-title h2:after {
            content: '';
            display: block;
            width: 70%;
            height: 4px;
            background: var(--primary);
            margin: 10px auto 0;
            border-radius: 2px;
        }
        
        .section-title p {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .product-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto; /* This centers the grid horizontally */
}

/* Keep your existing media queries */
@media (max-width: 1200px) {
    .product-list {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .product-list {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .product-list {
        grid-template-columns: repeat(1, 1fr);
    }
}
}
        /* Add this media query for responsive behavior */
@media (max-width: 1200px) {
    .product-list {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .product-list {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .product-list {
        grid-template-columns: repeat(1, 1fr);
    }
}
        /* With this corrected version */
.product {
  background-color: #fff;
  padding: 25px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  border-radius: var(--border-radius);
  text-align: center;
  width: 100%;
  transition: transform 0.3s ease-in-out;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
        
        .product:hover {
            transform: translateY(-12px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            left: -35px;
            background: var(--secondary);
            color: white;
            padding: 5px 40px;
            transform: rotate(-45deg);
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1;
        }
        
        .product-image-wrapper {
            width: 100%;
            height: 220px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product:hover .product-image {
            transform: scale(1.1);
        }
        
        .product h3 {
            font-size: 1.3rem;
            margin: 10px 0;
            color: var(--dark);
            font-weight: 600;
        }
        
        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            margin: 8px 0;
        }
        
        .product-description {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.5;
            flex-grow: 1;
        }
        
        .product-rating {
            color: #ffc107;
            margin: 8px 0;
            font-size: 1.1rem;
        }
        
        .add-to-cart-button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 15px;
        }
        
        .add-to-cart-button i {
            margin-right: 8px;
        }
        
        .add-to-cart-button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        /* Features Section */
        .features-section {
            background-color: white;
            padding: 60px 0;
            margin: 60px 0;
            box-shadow: var(--shadow);
        }
        
        .features-container {
            max-width: 1300px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 0 20px;
        }
        
        .feature {
            text-align: center;
            padding: 20px;
        }
        
        .feature i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .feature h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .feature p {
            color: var(--gray);
            font-size: 0.95rem;
        }

        /* Newsletter */
        .newsletter {
            background: linear-gradient(135deg, #6c5ce7, #4361ee);
            padding: 60px 20px;
            text-align: center;
            color: white;
            margin: 60px 0;
            border-radius: var(--border-radius);
        }
        
        .newsletter h3 {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .newsletter p {
            max-width: 600px;
            margin: 0 auto 25px;
            font-size: 1.1rem;
        }
        
        .newsletter-form {
            max-width: 500px;
            margin: 0 auto;
            display: flex;
        }
        
        .newsletter-input {
            flex-grow: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 30px 0 0 30px;
            font-size: 1rem;
            outline: none;
        }
        
        .newsletter-button {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 0 30px 30px 0;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .newsletter-button:hover {
            background-color: #ff5252;
        }

        /* Footer */
        footer {
            background-color: #222;
            color: #fff;
            padding: 60px 0 20px;
            margin-top: 70px;
        }
        
        .footer-container {
            max-width: 1300px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            padding: 0 20px;
        }
        
        .footer-column h3 {
            color: white;
            margin-bottom: 20px;
            font-size: 1.3rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-column h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s;
            display: inline-block;
        }
        
        .footer-links a:hover {
            color: var(--secondary);
            transform: translateX(5px);
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .footer-social a {
            color: white;
            background: rgba(255,255,255,0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .footer-social a:hover {
            background: var(--primary);
            transform: translateY(-5px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #aaa;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                padding: 15px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            nav {
                width: 100%;
                justify-content: space-around;
                margin-top: 10px;
            }
            
            nav a, nav button {
                margin: 0 5px;
                font-size: 0.9rem;
            }
            
            .hero-content h2 {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-input {
                border-radius: 30px;
                margin-bottom: 10px;
            }
            
            .newsletter-button {
                border-radius: 30px;
                padding: 12px;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 1s;
        }
        .header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #3498db;
    padding: 10px 20px;
    color: #fff;
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

nav {
    display: flex;
    align-items: center;
    gap: 20px;
}

nav a, .logout-button {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    background: none;
    border: none;
    cursor: pointer;
}

nav a:hover, .logout-button:hover {
    text-decoration: underline;
}

/* NEW: Search Form */
.search-form {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    margin: 0 20px;
}

.search-form input[type="text"] {
    border: none;
    padding: 8px 12px;
    outline: none;
    width: 200px;
}

.search-form button {
    background: #2980b9;
    border: none;
    padding: 8px 12px;
    color: #fff;
    cursor: pointer;
}

.search-form button:hover {
    background: #1c5980;
}

    </style>
</head>

<body>

<header>
    <div class="header-container">
        <div class="logo">
            <i class="fas fa-shopping-bag"></i>
            ShopRADO
        </div>

        <!-- Search Bar Added Here -->
        <form class="search-form" action="pages/search.php" method="GET">
            <input type="text" name="query" placeholder="Search products..." required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <nav>
            <a href="pages/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="pages/register.php"><i class="fas fa-user-plus"></i> Register</a>
            <a href="pages/cart.php" class="cart-link">
                <div class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </div>
                Cart
            </a>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-button"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </div>
</header>


<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h2>Welcome to ShopRADO</h2>
        <p>Discover amazing products with unbeatable prices and quality.</p>
        <a href="#products" class="shop-now-btn"><i class="fas fa-shopping-cart"></i> Shop Now</a>
    </div>
</section>

<div class="main-container">
    <main>
        <div class="section-title" id="products">
            <h2>Our Latest Products</h2>
            <p>Handpicked just for you</p>
        </div>
        <div class="product-list">
            <?php if (empty($products)) : ?>
                <p>No products available at the moment. Please check back later!</p>
            <?php else : ?>
                <?php foreach ($products as $index => $product) : ?>
                    <?php 
                        // Random features for demo purposes
                        $isNew = $index % 3 === 0;
                        $rating = rand(3, 5);
                        $stars = str_repeat('<i class="fas fa-star"></i>', $rating) . 
                                str_repeat('<i class="far fa-star"></i>', 5 - $rating);
                    ?>
                    <div class="product fade-in">
                        <?php if ($isNew) : ?>
                            <div class="product-badge">NEW</div>
                        <?php endif; ?>
                        <div class="product-image-wrapper">
                            <?php if (!empty($product['image'])) : ?>
                                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php else : ?>
                                <img src="images/placeholder.jpg" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($product['name']); ?></h3>
                        <div class="product-rating"><?= $stars ?></div>
                        <div class="product-price">$<?= number_format($product['price'], 2); ?></div>
                        <p class="product-description"><?= htmlspecialchars($product['description']); ?></p>
                        <form method="POST" action="pages/cart.php">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-button">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Features Section -->
<section class="features-section">
    <div class="features-container">
        <div class="feature">
            <i class="fas fa-shipping-fast"></i>
            <h3>Free Shipping</h3>
            <p>Free shipping on all orders over $50</p>
        </div>
        <div class="feature">
            <i class="fas fa-undo"></i>
            <h3>30 Days Return</h3>
            <p>Simply return it within 30 days for an exchange</p>
        </div>
        <div class="feature">
            <i class="fas fa-lock"></i>
            <h3>100% Secure Payment</h3>
            <p>Your payment information is processed securely</p>
        </div>
        <div class="feature">
            <i class="fas fa-headset"></i>
            <h3>24/7 Support</h3>
            <p>Contact us 24 hours a day, 7 days a week</p>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<div class="main-container">
    <section class="newsletter">
        <h3>Subscribe to Our Newsletter</h3>
        <p>Get the latest updates about new products and upcoming sales right to your inbox.</p>
        <form class="newsletter-form">
            <input type="email" class="newsletter-input" placeholder="Enter your email address" required>
            <button type="submit" class="newsletter-button">Subscribe</button>
        </form>
    </section>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-column">
            <h3>ShopRADO</h3>
            <p>Your one-stop destination for high-quality products at affordable prices. We're dedicated to providing exceptional shopping experiences.</p>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
            </div>
        </div>
        <div class="footer-column">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="#">Home</a></li>
                <li><a href="#">Shop</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">FAQ</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Customer Service</h3>
            <ul class="footer-links">
                <li><a href="#">My Account</a></li>
                <li><a href="#">Order Tracking</a></li>
                <li><a href="#">Wishlist</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Contact Info</h3>
            <ul class="footer-links">
                <li><i class="fas fa-map-marker-alt"></i> 123 Street, City, Country</li>
                <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                <li><i class="fas fa-envelope"></i> info@shoperado.com</li>
                <li><i class="fas fa-clock"></i> Mon-Fri: 9AM - 6PM</li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; <?= date('Y'); ?> ShopeRADO. All rights reserved.</p>
    </div>
</footer>

<!-- Simple animation script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add fade-in class to products when they appear in viewport
        const products = document.querySelectorAll('.product');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        products.forEach(product => {
            observer.observe(product);
        });
    });
</script>

</body>
</html>
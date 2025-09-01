<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];  // Assuming user ID is stored in session

// Handle Add to Cart with Quantity
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;  // Default to 1 if quantity is not set

    // Check if product is already in the user's cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Update quantity if the product is already in the cart
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$new_quantity, $user_id, $product_id]);
    } else {
        // Add new product to the cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
}

// Handle Product Removal from Cart
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

// Handle Quantity Update
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Update the quantity in the cart
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
}

// Fetch the user's cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count cart items for the header display
$cart_count = count($cart_items);

$total_cost = 0;  // Initialize total cost variable
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | ShopRADO</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #ff6b6b;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
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
            text-decoration: none;
            color: white;
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
        
        /* Search Form */
        .search-form {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 30px;
            overflow: hidden;
            margin: 0 20px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .search-form:hover, .search-form:focus-within {
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: translateY(-2px);
        }
        
        .search-form input[type="text"] {
            border: none;
            padding: 12px 20px;
            flex-grow: 1;
            font-size: 1rem;
            outline: none;
            width: 100%;
        }
        
        .search-form button {
            background: var(--primary);
            border: none;
            padding: 12px 20px;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .search-form button:hover {
            background: var(--primary-dark);
        }

        /* Container Styles */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #fff;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border-radius: var(--border-radius);
            animation: fadeIn 0.5s ease;
        }
        
        /* Cart Page Heading */
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark);
            position: relative;
        }
        
        .page-title h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .page-title h2:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary);
            margin: 10px auto 0;
            border-radius: 2px;
        }
        
        .page-title p {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        /* Cart Items */
        .cart-items {
            margin-bottom: 30px;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            position: relative;
        }
        
        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
        }
        
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-right: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .item-details {
            flex-grow: 1;
        }
        
        .item-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .item-price {
            font-size: 1.2rem;
            color: var(--primary);
            font-weight: 600;
        }
        
        .subtotal {
            font-size: 0.95rem;
            color: var(--gray);
            margin-top: 5px;
        }
        
        .item-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            background-color: var(--light);
            border-radius: 30px;
            padding: 5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .quantity-btn:hover {
            background-color: var(--primary-dark);
            transform: scale(1.1);
        }
        
        .quantity {
            width: 50px;
            text-align: center;
            font-size: 1rem;
            border: none;
            background: transparent;
            margin: 0 8px;
            font-weight: 600;
        }
        
        .quantity::-webkit-inner-spin-button, 
        .quantity::-webkit-outer-spin-button { 
            -webkit-appearance: none;
            margin: 0;
        }
        
        .remove-btn {
            background-color: var(--danger);
            color: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .remove-btn:hover {
            background-color: #c0392b;
            transform: scale(1.1);
        }
        
        /* Cart Summary */
        .cart-summary {
            background-color: var(--light);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 40px;
        }
        
        .summary-title {
            font-size: 1.4rem;
            color: var(--dark);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: 600;
            color: var(--gray);
        }
        
        .summary-value {
            font-weight: 700;
            color: var(--dark);
        }
        
        .total-row {
            font-size: 1.3rem;
            color: var(--primary);
            border-top: 2px solid rgba(0,0,0,0.1);
            padding-top: 15px;
            margin-top: 10px;
        }
        
        /* Cart Actions */
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .action-btn {
            padding: 14px 28px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .continue-shopping {
            background-color: var(--light);
            color: var(--dark);
            border: 2px solid var(--primary);
        }
        
        .continue-shopping:hover {
            background-color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .checkout-btn {
            background-color: var(--success);
            color: white;
        }
        
        .checkout-btn:hover {
            background-color: #27ae60;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        
        .btn-icon {
            margin-right: 8px;
        }
        
        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: var(--gray);
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-cart h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .empty-cart p {
            font-size: 1.1rem;
            color: var(--gray);
            margin-bottom: 30px;
        }
        
        .start-shopping {
            background-color: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
        }
        
        .start-shopping:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3);
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .cart-item img {
                margin-right: 0;
                margin-bottom: 15px;
                width: 120px;
                height: 120px;
            }
            
            .item-details {
                margin-bottom: 20px;
            }
            
            .item-actions {
                width: 100%;
                justify-content: center;
            }
            
            .cart-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .action-btn {
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .search-form {
                margin: 15px 0;
                width: 100%;
            }
            
            nav {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
            }
            
            nav a, nav button {
                margin: 0;
            }
            
            .cart-summary {
                margin-top: 20px;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                width: 95%;
                padding: 15px;
            }
            
            .page-title h2 {
                font-size: 1.8rem;
            }
            
            .quantity-control {
                flex-direction: column;
                gap: 5px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 1s;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <a href="../index.php" class="logo">
                <i class="fas fa-shopping-bag"></i>
                ShopRADO
            </a>

            <!-- Search Bar -->
            <form class="search-form" action="search.php" method="GET">
                <input type="text" name="query" placeholder="Search products..." required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <nav>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="account.php"><i class="fas fa-user-circle"></i> My Account</a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
                <a href="cart.php" class="cart-link">
                    <div class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?= $cart_count ?></span>
                    </div>
                    Cart
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="../includes/logout.php" style="display: inline;">
                        <button type="submit" name="logout" class="logout-button">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h2>Your Shopping Cart</h2>
            <p>Review and modify your selected items</p>
        </div>

        <?php if (empty($cart_items)) : ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any products to your cart yet.</p>
                <a href="../index.php" class="start-shopping">
                    <i class="fas fa-shopping-bag btn-icon"></i> Start Shopping
                </a>
            </div>
        <?php else : ?>
            <div class="cart-items">
                <?php
                // Fetch product details for each cart item
                $product_ids = array_column($cart_items, 'product_id');
                $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
                $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
                $stmt->execute($product_ids);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($products as $product) {
                    $quantity = 0;
                    foreach ($cart_items as $cart_item) {
                        if ($cart_item['product_id'] == $product['id']) {
                            $quantity = $cart_item['quantity'];
                            break;
                        }
                    }
                    $subtotal = $product['price'] * $quantity;
                    $total_cost += $subtotal;

                    echo "<div class='cart-item fade-in'>
                            <img src='../images/" . (empty($product['image']) ? 'placeholder.jpg' : htmlspecialchars($product['image'])) . "' alt='" . htmlspecialchars($product['name']) . "' class='item-image'>
                            <div class='item-details'>
                                <div class='item-name'>" . htmlspecialchars($product['name']) . "</div>
                                <div class='item-price'>$" . number_format($product['price'], 2) . "</div>
                                <div class='subtotal'>Subtotal: $" . number_format($subtotal, 2) . "</div>
                            </div>
                            <div class='item-actions'>
                                <form method='POST' class='quantity-form'>
                                    <input type='hidden' name='product_id' value='" . $product['id'] . "'>
                                    <div class='quantity-control'>
                                        <button type='button' class='quantity-btn decrease-btn' onclick='decreaseQuantity(this.parentNode)'>-</button>
                                        <input type='number' name='quantity' value='" . $quantity . "' min='1' class='quantity' required>
                                        <button type='button' class='quantity-btn increase-btn' onclick='increaseQuantity(this.parentNode)'>+</button>
                                    </div>
                                    <button type='submit' name='update_quantity' style='display:none;' id='update-btn-" . $product['id'] . "'>Update</button>
                                </form>
                                <form method='POST'>
                                    <input type='hidden' name='product_id' value='" . $product['id'] . "'>
                                    <button type='submit' name='remove_from_cart' class='remove-btn' title='Remove Item'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                </form>
                            </div>
                        </div>";
                }
                ?>
            </div>

            <div class="cart-summary">
                <div class="summary-title">Order Summary</div>
                <div class="summary-row">
                    <div class="summary-label">Items (<?= $cart_count ?>):</div>
                    <div class="summary-value">$<?= number_format($total_cost, 2) ?></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Shipping:</div>
                    <div class="summary-value">Free</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Tax:</div>
                    <div class="summary-value">$<?= number_format($total_cost * 0.1, 2) ?></div>
                </div>
                <div class="summary-row total-row">
                    <div class="summary-label">Total:</div>
                    <div class="summary-value">$<?= number_format($total_cost * 1.1, 2) ?></div>
                </div>
            </div>

            <div class="cart-actions">
                <a href="../index.php" class="action-btn continue-shopping">
                    <i class="fas fa-arrow-left btn-icon"></i> Continue Shopping
                </a>
                <a href="checkout.php" class="action-btn checkout-btn">
                    <i class="fas fa-credit-card btn-icon"></i> Proceed to Checkout
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript for real-time interaction -->
    <script>
        // Function to decrease quantity
        function decreaseQuantity(parent) {
            const input = parent.querySelector('input[type="number"]');
            const updateBtn = document.getElementById('update-btn-' + parent.parentNode.querySelector('input[name="product_id"]').value);
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
                updateBtn.click(); // Auto-submit the form
            }
        }
        
        // Function to increase quantity
        function increaseQuantity(parent) {
            const input = parent.querySelector('input[type="number"]');
            const updateBtn = document.getElementById('update-btn-' + parent.parentNode.querySelector('input[name="product_id"]').value);
            let value = parseInt(input.value);
            input.value = value + 1;
            updateBtn.click(); // Auto-submit the form
        }
        
        // Auto-submit on quantity change
        document.querySelectorAll('.quantity').forEach(input => {
            input.addEventListener('change', function() {
                const updateBtn = document.getElementById('update-btn-' + this.closest('form').querySelector('input[name="product_id"]').value);
                updateBtn.click();
            });
        });
        
        // Add animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>
</html>
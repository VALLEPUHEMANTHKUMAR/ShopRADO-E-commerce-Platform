<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch the user's cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count cart items for the header display
$cart_count = count($cart_items);

// Fetch user information for billing
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate order total
$subtotal = 0;
$product_ids = array();

if (!empty($cart_items)) {
    $product_ids = array_column($cart_items, 'product_id');
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        foreach ($cart_items as $cart_item) {
            if ($cart_item['product_id'] == $product['id']) {
                $subtotal += $product['price'] * $cart_item['quantity'];
                break;
            }
        }
    }
}

$tax = $subtotal * 0.1; // 10% tax
$total = $subtotal + $tax;

// Process Order
if (isset($_POST['place_order'])) {
    try {
        // Begin transaction
        $conn->beginTransaction();
        
        // Create order record
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_date, total_amount, status, shipping_address, payment_method) 
                               VALUES (?, NOW(), ?, 'Processing', ?, ?)");
        $shipping_address = $_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ' ' . $_POST['zip_code'] . ', ' . $_POST['country'];
        $payment_method = $_POST['payment_method'];
        $stmt->execute([$user_id, $total, $shipping_address, $payment_method]);
        
        $order_id = $conn->lastInsertId();
        
        // Add order items
        foreach ($cart_items as $item) {
            // Get product details
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $product['price']]);
            
            // Update product inventory (optional)
            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // Clear the user's cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to order confirmation
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $error_message = "There was a problem processing your order: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | ShopRADO</title>
    
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
            animation: fadeIn 0.5s ease;
        }
        
        /* Page Heading */
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
        
        /* Checkout Layout */
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        /* Checkout Card Styles */
        .checkout-card {
            background-color: #fff;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border-radius: var(--border-radius);
            margin-bottom: 30px;
        }
        
        .checkout-card h3 {
            font-size: 1.4rem;
            margin-bottom: 20px;
            color: var(--dark);
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            font-weight: 600;
        }
        
        /* Form Styles */
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            outline: none;
        }
        
        /* Radio and Checkbox Styles */
        .radio-group {
            margin-bottom: 25px;
        }
        
        .radio-label {
            display: flex;
            align-items: center;
            background: white;
            border: 2px solid rgba(0,0,0,0.08);
            border-radius: var(--border-radius);
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .radio-label:hover {
            border-color: var(--primary);
            background-color: rgba(67, 97, 238, 0.05);
            transform: translateY(-2px);
        }
        
        .radio-label.active {
            border-color: var(--primary);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .radio-input {
            display: none;
        }
        
        .radio-custom {
            width: 22px;
            height: 22px;
            border: 2px solid rgba(0,0,0,0.2);
            border-radius: 50%;
            margin-right: 15px;
            position: relative;
            flex-shrink: 0;
        }
        
        .radio-custom:after {
            content: '';
            width: 12px;
            height: 12px;
            background: var(--primary);
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: all 0.2s;
        }
        
        .radio-input:checked + .radio-custom:after {
            transform: translate(-50%, -50%) scale(1);
        }
        
        .radio-details {
            flex-grow: 1;
        }
        
        .radio-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .payment-logo {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .radio-description {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 5px;
        }
        
        /* Order Summary Styles */
        .order-summary {
            position: sticky;
            top: 100px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        
        .summary-row:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
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
        
        /* Credit Card Form Styles */
        .card-container {
            perspective: 1000px;
            margin-bottom: 30px;
        }
        
        .credit-card {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #4361ee, #805ad5);
            border-radius: 16px;
            padding: 25px;
            color: white;
            position: relative;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }
        
        .credit-card.flipped {
            transform: rotateY(180deg);
        }
        
        .card-front, .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            padding: 25px;
            backface-visibility: hidden;
        }
        
        .card-front {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .card-back {
            transform: rotateY(180deg);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-logo {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 2px;
        }
        
        .card-chip {
            width: 50px;
            height: 35px;
            background: linear-gradient(135deg, #ffd166, #ffb347);
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }
        
        .card-chip:before {
            content: '';
            position: absolute;
            width: 30px;
            height: 15px;
            background: rgba(255, 255, 255, 0.3);
            top: 10px;
            left: 10px;
            border-radius: 3px;
        }
        
        .card-number {
            font-size: 1.4rem;
            font-family: 'Courier New', monospace;
            letter-spacing: 3px;
            margin: 10px 0;
        }
        
        .card-details {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }
        
        .card-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .card-holder {
            text-transform: uppercase;
        }
        
        .card-stripe {
            background-color: #333;
            height: 50px;
            width: 100%;
            margin: 20px 0;
        }
        
        .card-signature {
            background-color: #fff;
            height: 40px;
            width: 90%;
            margin-left: auto;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #333;
        }
        
        .card-signature .ccv {
            font-weight: 700;
        }
        
        .signature-line {
            width: 70%;
            height: 2px;
            background: repeating-linear-gradient(
                90deg,
                #333,
                #333 5px,
                transparent 5px,
                transparent 10px
            );
        }
        
        .card-network {
            position: absolute;
            right: 25px;
            bottom: 25px;
            font-size: 2rem;
        }
        
        /* Payment Form */
        .payment-fields {
            margin-top: 20px;
        }
        
        .card-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        /* Submit Button */
        .place-order-btn {
            background-color: var(--success);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 15px 0;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.2);
        }
        
        .place-order-btn:hover {
            background-color: #27ae60;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.3);
        }
        
        .btn-icon {
            margin-right: 10px;
        }
        
        /* Order Items Preview */
        .order-items {
            margin-top: 15px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        
        .item-info {
            flex-grow: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .item-quantity {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .item-price {
            font-weight: 600;
            color: var(--primary);
        }
        
        /* Payment Processing Animation */
        .payment-processing {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        
        .payment-processing.show {
            opacity: 1;
            visibility: visible;
        }
        
        .processing-content {
            background-color: white;
            padding: 40px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            max-width: 90%;
            width: 400px;
        }
        
        .loader {
            width: 80px;
            height: 80px;
            border: 5px solid rgba(67, 97, 238, 0.2);
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            margin: 0 auto 30px;
            animation: spin 1s linear infinite;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: var(--success);
            border-radius: 50%;
            margin: 0 auto 30px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            transform: scale(0);
            transition: transform 0.3s ease;
        }
        
        .success-icon.show {
            transform: scale(1);
        }
        
        .processing-title {
            font-size: 1.6rem;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .processing-message {
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .loader-steps {
            margin-top: 30px;
            text-align: left;
        }
        
        .loader-step {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            opacity: 0.5;
            transition: opacity 0.3s;
        }
        
        .loader-step.active {
            opacity: 1;
        }
        
        .loader-step.complete {
            color: var(--success);
        }
        
        .step-icon {
            margin-right: 12px;
            width: 24px;
            font-size: 1.2rem;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .order-summary {
                position: static;
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
            
            .card-number {
                font-size: 1.2rem;
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
            
            .card-form-row {
                grid-template-columns: 1fr;
            }
            
            .credit-card {
                height: 190px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(46, 204, 113, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }
        
        .fade-in {
            animation: fadeIn 1s;
        }
        
        /* Additional Styling */
        .security-badges {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 25px;
            gap: 20px;
            color: var(--gray);
            font-size: 1.2rem;
        }
        
        .badge-text {
            font-size: 0.8rem;
            margin-left: 5px;
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
            <h2>Checkout</h2>
            <p>Complete your purchase securely</p>
        </div>

        <?php if (empty($cart_items)) : ?>
            <div class="checkout-card" style="text-align: center; padding: 50px 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: var(--gray); margin-bottom: 20px;"></i>
                <h3>Your cart is empty</h3>
                <p style="margin-bottom: 30px;">You need to add products to your cart before checking out.</p>
                <a href="../index.php" style="background-color: var(--primary); color: white; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">
                    <i class="fas fa-shopping-bag" style="margin-right: 8px;"></i> Go Shopping
                </a>
                </div>
        <?php else : ?>
        <form method="POST" action="" id="checkout-form">
            <div class="checkout-container">
                <div class="checkout-details">
                    <!-- Billing Information -->
                    <div class="checkout-card">
                        <h3><i class="fas fa-user"></i> Billing Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="checkout-card">
                        <h3><i class="fas fa-map-marker-alt"></i> Shipping Address</h3>
                        <div class="form-group">
                            <label class="form-label" for="address">Street Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="state">State/Province</label>
                                <input type="text" class="form-control" id="state" name="state" value="<?= htmlspecialchars($user['state'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="zip_code">ZIP/Postal Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="country">Country</label>
                                <select class="form-control" id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="United States" <?= ($user['country'] ?? '') == 'United States' ? 'selected' : '' ?>>United States</option>
                                    <option value="India" <?= ($user['country'] ?? '') == 'India' ? 'selected' : '' ?>>India</option>
                                    <option value="United Kingdom" <?= ($user['country'] ?? '') == 'United Kingdom' ? 'selected' : '' ?>>United Kingdom</option>
                                    <option value="Australia" <?= ($user['country'] ?? '') == 'Australia' ? 'selected' : '' ?>>Australia</option>
                                    <option value="France" <?= ($user['country'] ?? '') == 'France' ? 'selected' : '' ?>>France</option>
                                    <option value="Germany" <?= ($user['country'] ?? '') == 'Germany' ? 'selected' : '' ?>>Germany</option>
                                    <option value="Japan" <?= ($user['country'] ?? '') == 'Japan' ? 'selected' : '' ?>>Japan</option>
                                    <!-- Add more countries as needed -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-card">
                        <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                        <!-- Credit Card Option -->
                        <div class="radio-group">
                            <label class="radio-label active" for="payment_credit_card">
                                <input type="radio" id="payment_credit_card" name="payment_method" value="credit_card" class="radio-input" checked>
                                <span class="radio-custom"></span>
                                <div class="radio-details">
                                    <div class="radio-title">
                                        Credit Card
                                        <div class="payment-logo">
                                            <i class="fab fa-cc-visa"></i>
                                            <i class="fab fa-cc-mastercard"></i>
                                            <i class="fab fa-cc-amex"></i>
                                        </div>
                                    </div>
                                    <div class="radio-description">Pay securely with your credit card</div>
                                </div>
                            </label>
                            
                            <!-- Credit Card Form - Show when Credit Card is selected -->
                            <div class="payment-fields" id="credit-card-fields">
                                <div class="card-container">
                                    <div class="credit-card" id="credit-card">
                                        <div class="card-front">
                                            <div class="card-header">
                                                <div class="card-logo">CARD</div>
                                                <div class="card-chip"></div>
                                            </div>
                                            <div class="card-number" id="card-number-display">•••• •••• •••• ••••</div>
                                            <div class="card-details">
                                                <div class="card-holder-container">
                                                    <div class="card-label">Card Holder</div>
                                                    <div class="card-holder" id="card-holder-display">YOUR NAME</div>
                                                </div>
                                                <div class="card-expiry-container">
                                                    <div class="card-label">Expires</div>
                                                    <div class="card-expiry" id="card-expiry-display">MM/YY</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-back">
                                            <div class="card-stripe"></div>
                                            <div class="card-signature">
                                                <div class="signature-line"></div>
                                                <div class="ccv" id="card-ccv-display">CCV</div>
                                            </div>
                                            <div class="card-network">
                                                <i class="fab fa-cc-visa"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="card_number">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="card_holder">Cardholder Name</label>
                                    <input type="text" class="form-control" id="card_holder" name="card_holder" placeholder="Name as shown on card" required>
                                </div>
                                <div class="card-form-row">
                                    <div class="form-group">
                                        <label class="form-label" for="card_expiry">Expiration Date</label>
                                        <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="card_ccv">Security Code (CCV)</label>
                                        <input type="text" class="form-control" id="card_ccv" name="card_ccv" placeholder="CCV" maxlength="4" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PayPal Option -->
                        <div class="radio-group">
                            <label class="radio-label" for="payment_paypal">
                                <input type="radio" id="payment_paypal" name="payment_method" value="paypal" class="radio-input">
                                <span class="radio-custom"></span>
                                <div class="radio-details">
                                    <div class="radio-title">
                                        PayPal
                                        <div class="payment-logo">
                                            <i class="fab fa-paypal"></i>
                                        </div>
                                    </div>
                                    <div class="radio-description">Pay via PayPal; you can pay with your credit card if you don't have a PayPal account.</div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Bank Transfer Option -->
                        <div class="radio-group">
                            <label class="radio-label" for="payment_bank">
                                <input type="radio" id="payment_bank" name="payment_method" value="bank_transfer" class="radio-input">
                                <span class="radio-custom"></span>
                                <div class="radio-details">
                                    <div class="radio-title">
                                        Direct Bank Transfer
                                        <div class="payment-logo">
                                            <i class="fas fa-university"></i>
                                        </div>
                                    </div>
                                    <div class="radio-description">Make your payment directly into our bank account. Please use your Order ID as the payment reference.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="checkout-card">
                        <h3><i class="fas fa-shopping-basket"></i> Order Summary</h3>
                        
                        <!-- Order Items -->
                        <div class="order-items">
                            <?php 
                            $total_items = 0;
                            foreach ($products as $product): 
                                foreach ($cart_items as $cart_item):
                                    if ($cart_item['product_id'] == $product['id']):
                                        $total_items += $cart_item['quantity'];
                            ?>
                                <div class="order-item">
                                    
                                        <div class="item-name"><?= htmlspecialchars($product['name']) ?></div>
                                        <div class="item-quantity">Qty: <?= $cart_item['quantity'] ?></div>
                                    </div>
                                    <div class="item-price">$<?= number_format($product['price'] * $cart_item['quantity'], 2) ?></div>
                                </div>
                            <?php 
                                    endif;
                                endforeach;
                            endforeach; 
                            ?>
                        </div>
                        
                        <!-- Summary Details -->
                        <div class="summary-row">
                            <div class="summary-label">Items (<?= $total_items ?>):</div>
                            <div class="summary-value">$<?= number_format($subtotal, 2) ?></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Tax (10%):</div>
                            <div class="summary-value">$<?= number_format($tax, 2) ?></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Shipping:</div>
                            <div class="summary-value">FREE</div>
                        </div>
                        <div class="summary-row total-row">
                            <div class="summary-label">Total:</div>
                            <div class="summary-value">$<?= number_format($total, 2) ?></div>
                        </div>
                        
                        <!-- Security Badges -->
                        <div class="security-badges">
                            <div>
                                <i class="fas fa-lock"></i>
                                <span class="badge-text">Secure Checkout</span>
                            </div>
                            <div>
                                <i class="fas fa-shield-alt"></i>
                                <span class="badge-text">Protected Payment</span>
                            </div>
                        </div>
                        
                        <!-- Place Order Button -->
                        <button type="submit" name="place_order" class="place-order-btn" id="place-order-btn">
                            <i class="fas fa-check-circle btn-icon"></i>
                            Place Order Now
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Payment Processing Modal -->
    <div class="payment-processing" id="payment-processing">
        <div class="processing-content">
            <div class="loader" id="process-loader"></div>
            <div class="success-icon" id="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="processing-title" id="processing-title">Processing Payment</h3>
            <p class="processing-message" id="processing-message">Please wait while we process your payment...</p>
            
            <div class="loader-steps">
                <div class="loader-step active" id="step-validating">
                    <span class="step-icon"><i class="fas fa-check-circle"></i></span>
                    <span class="step-text">Validating payment details</span>
                </div>
                <div class="loader-step" id="step-processing">
                    <span class="step-icon"><i class="fas fa-circle-notch"></i></span>
                    <span class="step-text">Processing payment</span>
                </div>
                <div class="loader-step" id="step-creating">
                    <span class="step-icon"><i class="fas fa-box"></i></span>
                    <span class="step-text">Creating order</span>
                </div>
                <div class="loader-step" id="step-complete">
                    <span class="step-icon"><i class="fas fa-check-circle"></i></span>
                    <span class="step-text">Order complete!</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Credit Card Form Handling
            const cardNumberDisplay = document.getElementById('card-number-display');
            const cardHolderDisplay = document.getElementById('card-holder-display');
            const cardExpiryDisplay = document.getElementById('card-expiry-display');
            const cardCcvDisplay = document.getElementById('card-ccv-display');
            const creditCard = document.getElementById('credit-card');
            
            // Card Number Input Formatting
            document.getElementById('card_number').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                let formattedValue = '';
                
                // Format the card number in groups of 4
                for(let i = 0; i < value.length; i++) {
                    if(i > 0 && i % 4 === 0) {
                        formattedValue += ' ';
                    }
                    formattedValue += value[i];
                }
                
                e.target.value = formattedValue.substring(0, 19); // Max 16 digits + 3 spaces
                
                // Update display
                cardNumberDisplay.textContent = formattedValue || '•••• •••• •••• ••••';
            });
            
            // Card Holder Name
            document.getElementById('card_holder').addEventListener('input', function(e) {
                cardHolderDisplay.textContent = e.target.value.toUpperCase() || 'YOUR NAME';
            });
            
            // Expiry Date Formatting
            document.getElementById('card_expiry').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                
                e.target.value = value;
                cardExpiryDisplay.textContent = value || 'MM/YY';
            });
            
            // CCV Input and Card Flipping
            const cardCcv = document.getElementById('card_ccv');
            cardCcv.addEventListener('focus', function() {
                creditCard.classList.add('flipped');
            });
            
            cardCcv.addEventListener('blur', function() {
                creditCard.classList.remove('flipped');
            });
            
            cardCcv.addEventListener('input', function(e) {
                cardCcvDisplay.textContent = e.target.value || 'CCV';
            });
            
            // Payment Method Radio Buttons
            const paymentMethods = document.querySelectorAll('.radio-label');
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove active class from all methods
                    paymentMethods.forEach(item => item.classList.remove('active'));
                    // Add active class to selected method
                    this.classList.add('active');
                });
            });
            
            // Form Submission and Processing Animation
            const checkoutForm = document.getElementById('checkout-form');
            const paymentProcessing = document.getElementById('payment-processing');
            const processingSteps = [
                document.getElementById('step-validating'),
                document.getElementById('step-processing'),
                document.getElementById('step-creating'),
                document.getElementById('step-complete')
            ];
            const processLoader = document.getElementById('process-loader');
            const successIcon = document.getElementById('success-icon');
            const processingTitle = document.getElementById('processing-title');
            const processingMessage = document.getElementById('processing-message');
            
            <?php if(empty($error_message)): ?>
            // Simulated processing animation (remove this in production)
            if(checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    // Don't prevent the default form submission in production
                    // e.preventDefault();
                    
                    // Show processing modal
                    paymentProcessing.classList.add('show');
                    
                    // Simulate processing steps (remove this in production)
                    setTimeout(() => {
                        processingSteps[0].classList.add('complete');
                        processingSteps[1].classList.add('active');
                    }, 1500);
                    
                    setTimeout(() => {
                        processingSteps[1].classList.add('complete');
                        processingSteps[2].classList.add('active');
                    }, 3000);
                    
                    setTimeout(() => {
                        processingSteps[2].classList.add('complete');
                        processingSteps[3].classList.add('active');
                        processLoader.style.display = 'none';
                        successIcon.classList.add('show');
                        processingTitle.textContent = 'Payment Successful!';
                        processingMessage.textContent = 'Redirecting to order confirmation...';
                    }, 4500);
                    
                    // In a real implementation, the form would submit naturally here
                    // and the server would redirect to the confirmation page
                });
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>
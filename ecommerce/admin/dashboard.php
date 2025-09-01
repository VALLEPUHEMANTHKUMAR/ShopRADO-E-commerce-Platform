<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopRADO Admin</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0074D9;
            --secondary: #FF4081;
            --dark: #333;
            --light: #f8f9fa;
            --success: #28a745;
            --danger: #dc3545;
            --gray: #6c757d;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: var(--white);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .logo-icon {
            color: var(--secondary);
            font-size: 24px;
            margin-right: 8px;
        }
        
        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .logo-text span {
    color: #0074D9; /* or var(--primary) to use the blue color already defined */
}
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark);
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: rgba(0,116,217,0.1);
            border-left: 4px solid var(--primary);
        }
        
        .menu-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .menu-item.logout {
            margin-top: 20px;
            color: var(--danger);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            transition: all 0.3s ease;
        }
        
        .header {
            background-color: var(--white);
            padding: 15px 20px;
            box-shadow: var(--shadow);
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            opacity: 0.3;
        }
        
        .card h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .card p {
            color: var(--gray);
            font-size: 14px;
        }
        
        .card.primary {
            border-left: 4px solid var(--primary);
        }
        
        .card.secondary {
            border-left: 4px solid var(--secondary);
        }
        
        .card.success {
            border-left: 4px solid var(--success);
        }
        
        .recent-actions {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 20px;
        }
        
        .actions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .action-list {
            list-style: none;
        }
        
        .action-item {
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        
        .action-item:last-child {
            border-bottom: none;
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(0,116,217,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
        }
        
        .action-content {
            flex: 1;
        }
        
        .action-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .action-time {
            font-size: 12px;
            color: var(--gray);
        }
        
        .action-button {
            background-color: var(--light);
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-button:hover {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .toggle-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--dark);
        }
        
        footer {
            text-align: center;
            padding: 20px;
            margin-top: auto;
            background-color: var(--white);
            box-shadow: 0 -2px 4px rgba(0,0,0,0.05);
            margin-left: 250px;
            transition: all 0.3s ease;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                transform: translateX(0);
            }
            
            .sidebar.active {
                width: 250px;
            }
            
            .sidebar:not(.active) .logo-text,
            .sidebar:not(.active) .menu-item span {
                display: none;
            }
            
            .sidebar:not(.active) .menu-item {
                justify-content: center;
            }
            
            .sidebar:not(.active) .menu-item i {
                margin-right: 0;
            }
            
            .main-content, footer {
                margin-left: 80px;
            }
            
            .main-content.full, footer.full {
                margin-left: 250px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content, footer {
                margin-left: 0;
            }
            
            .toggle-sidebar {
                display: block;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-shopping-bag logo-icon" style="color: #FF4081;"></i>
                <div class="logo-text">Shop<span>RADO</span></div>
            </div>
            <p>Admin Panel</p>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="add_product.php" class="menu-item">
                <i class="fas fa-plus-circle"></i>
                <span>Add Product</span>
            </a>
            <a href="manage_products.php" class="menu-item">
                <i class="fas fa-box"></i>
                <span>Manage Products</span>
            </a>
            <a href="orders.php" class="menu-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
            <a href="customers.php" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Customers</span>
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="logout.php" class="menu-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="header">
            <button class="toggle-sidebar" id="toggle-sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h2>Dashboard</h2>
            <div class="user-info">
                <img src="../images/admin profile.jpg" alt="Admin">
                <span>Welcome, Admin</span>
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="card primary">
                <div class="card-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3>254</h3>
                <p>Total Products</p>
            </div>
            <div class="card secondary">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>75</h3>
                <p>New Orders</p>
            </div>
            <div class="card success">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>1,250</h3>
                <p>Customers</p>
            </div>
        </div>

        <div class="recent-actions">
            <div class="actions-header">
                <h3>Recent Activities</h3>
                <button class="action-button">View All</button>
            </div>
            <ul class="action-list">
                <li class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">New product added: "Wireless Headphones"</div>
                        <div class="action-time">Today, 10:30 AM</div>
                    </div>
                    <button class="action-button">View</button>
                </li>
                <li class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">New order #5421 received</div>
                        <div class="action-time">Yesterday, 3:45 PM</div>
                    </div>
                    <button class="action-button">Process</button>
                </li>
                <li class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">New customer registered: John Doe</div>
                        <div class="action-time">Yesterday, 1:20 PM</div>
                    </div>
                    <button class="action-button">View</button>
                </li>
            </ul>
        </div>
    </div>

    <footer id="footer">
        <p>&copy; <?php echo date("Y"); ?> ShopRADO Admin Dashboard</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const footer = document.getElementById('footer');
            const toggleBtn = document.getElementById('toggle-sidebar');
            
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('full');
                footer.classList.toggle('full');
            });
            
            // For responsiveness
            function checkWidth() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('full');
                    footer.classList.remove('full');
                }
            }
            
            window.addEventListener('resize', checkWidth);
            checkWidth();
        });
    </script>
</body>
</html>
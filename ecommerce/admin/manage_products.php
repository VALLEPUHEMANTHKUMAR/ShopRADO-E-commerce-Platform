<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
include '../includes/db.php';
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - ShopRADO Admin</title>
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
            color: #0074D9; /* RADO in blue color */
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
        
        .toggle-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--dark);
        }
        
        .content-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 25px;
            margin-bottom: 20px;
            overflow-x: auto;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 10px 20px;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 4px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn-primary {
            color: #fff;
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .btn-success {
            color: #fff;
            background-color: var(--success);
            border-color: var(--success);
        }
        
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        
        .btn-danger {
            color: #fff;
            background-color: var(--danger);
            border-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        
        .products-table th, 
        .products-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .products-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--dark);
            position: sticky;
            top: 0;
        }
        
        .products-table tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .product-description {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .search-filter {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .search-box {
            flex: 1;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,116,217,0.25);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
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
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s ease;
        }
        
        .pagination-item:hover, 
        .pagination-item.active {
            background-color: var(--primary);
            color: var(--white);
            border-color: var(--primary);
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
            
            .content-card {
                padding: 15px;
            }
            
            .products-table th, 
            .products-table td {
                padding: 10px;
            }
            
            .product-description {
                max-width: 150px;
            }
            
            .search-filter {
                flex-direction: column;
                align-items: stretch;
            }
            
            .content-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
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
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="add_product.php" class="menu-item">
                <i class="fas fa-plus-circle"></i>
                <span>Add Product</span>
            </a>
            <a href="manage_products.php" class="menu-item active">
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
            <h2>Manage Products</h2>
            <div class="user-info">
                <img src="../images/admin profile.jpg" alt="Admin">
                <span>Welcome, Admin</span>
            </div>
        </div>

        <div class="content-card">
            <div class="content-header">
                <h3>Product Inventory</h3>
                <a href="add_product.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Add New Product
                </a>
            </div>
            
            <div class="search-filter">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search products...">
                </div>
            </div>
            
            <?php if(count($products) > 0): ?>
            <table class="products-table" id="productsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id']; ?></td>
                        <td>
                            <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                        </td>
                        <td><?= htmlspecialchars($product['name']); ?></td>
                        <td>$<?= number_format($product['price'], 2); ?></td>
                        <td class="product-description" title="<?= htmlspecialchars($product['description']); ?>">
                            <?= htmlspecialchars($product['description']); ?>
                        </td>
                        <td class="actions">
                            <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn action-btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="delete_product.php?id=<?= $product['id']; ?>" class="btn action-btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No products found</h3>
                <p>Start by adding your first product.</p>
                <a href="add_product.php" class="btn btn-primary">Add Product</a>
            </div>
            <?php endif; ?>
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
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchValue = this.value.toLowerCase();
                    const table = document.getElementById('productsTable');
                    if(table) {
                        const rows = table.getElementsByTagName('tr');
                        
                        for(let i = 1; i < rows.length; i++) {
                            let found = false;
                            const cells = rows[i].getElementsByTagName('td');
                            
                            for(let j = 0; j < cells.length; j++) {
                                const cellText = cells[j].textContent || cells[j].innerText;
                                
                                if(cellText.toLowerCase().indexOf(searchValue) > -1) {
                                    found = true;
                                    break;
                                }
                            }
                            
                            if(found) {
                                rows[i].style.display = '';
                            } else {
                                rows[i].style.display = 'none';
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
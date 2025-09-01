<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
include '../includes/db.php';

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // Upload the image to the 'images' folder
    move_uploaded_file($_FILES['image']['tmp_name'], "../images/$image");

    // Insert product details into the database
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $description, $image]);

    $success_message = "Product added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - ShopRADO Admin</title>
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
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,116,217,0.25);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #f0f2f5;
            border: 1px dashed #aaa;
            border-radius: 4px;
            color: var(--dark);
            font-weight: 500;
            text-align: center;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-input-button:hover {
            background-color: #e9ecef;
            border-color: var(--primary);
        }
        
        .file-input {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        
        .file-name {
            margin-top: 8px;
            font-size: 14px;
            color: var(--gray);
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 12px 25px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 4px;
            transition: all 0.3s ease;
            cursor: pointer;
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
        
        .btn-secondary {
            color: #fff;
            background-color: var(--gray);
            border-color: var(--gray);
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
        
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
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
            
            .content-card {
                padding: 15px;
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
            <a href="add_product.php" class="menu-item active">
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
            <h2>Add Product</h2>
            <div class="user-info">
                <img src="../images/admin profile.jpg" alt="Admin">
                <span>Welcome, Admin</span>
            </div>
        </div>

        <?php if(isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <div class="content-card">
            <div class="content-header">
                <h3>Add New Product</h3>
                <a href="manage_products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="form-label">Product Name:</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="price" class="form-label">Price ($):</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" id="description" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Product Image:</label>
                    <div class="file-input-wrapper">
                        <div class="file-input-button">
                            <i class="fas fa-cloud-upload-alt"></i> Choose Image
                        </div>
                        <input type="file" name="image" id="image" class="file-input" required accept="image/*">
                    </div>
                    <div id="file-name" class="file-name">No file chosen</div>
                </div>

                <div class="actions">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" name="add_product" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Product
                    </button>
                </div>
            </form>
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
            
            // Display selected filename
            const fileInput = document.getElementById('image');
            const fileNameDisplay = document.getElementById('file-name');
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    fileNameDisplay.textContent = this.files[0].name;
                } else {
                    fileNameDisplay.textContent = 'No file chosen';
                }
            });
        });
    </script>
</body>
</html>
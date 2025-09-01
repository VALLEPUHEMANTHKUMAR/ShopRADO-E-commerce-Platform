# ShopRADO-E-commerce-Platform

A comprehensive e-commerce platform built with PHP and MySQL, designed to provide seamless shopping experiences for customers and efficient management tools for administrators.

## 📋 Project Description

shopRADO is a full-featured e-commerce web application that enables customers to browse products, manage shopping carts, and make purchases while providing administrators with powerful tools to manage inventory and user accounts. The platform features a clean, user-friendly interface with robust backend functionality.

### Key Highlights:
- **Dual Interface**: Separate customer and admin portals for optimal user experience
- **Secure Authentication**: Role-based access control with encrypted password storage
- **Product Management**: Complete CRUD operations for product inventory
- **Shopping Cart**: Dynamic cart functionality with real-time updates
- **Database Integration**: Well-structured MySQL database with relational integrity

## 🚀 Features

### Customer Features
- User registration and secure login/logout
- Browse products with detailed information
- Add products to shopping cart
- Cart management (view, update, remove items)
- Session-based user tracking

### Admin Features
- Secure admin authentication
- Centralized dashboard for store management
- Add new products with image uploads
- Edit and delete existing products
- User role management

## 🛠️ Technology Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Security:** Password hashing, Session management
- **File Handling:** Image upload and management

## 🖥️ Development Environment

### XAMPP Server Configuration
This project is developed and optimized to run on **XAMPP** (Cross-Platform Apache MySQL PHP Perl), providing a complete local development environment.

**XAMPP Components Used:**
- **Apache HTTP Server** - Web server for hosting the PHP application
- **MySQL Database Server** - Relational database management system for data storage
- **PHP** - Server-side scripting language for dynamic web content
- **phpMyAdmin** - Web-based MySQL administration tool for database management

**Why XAMPP?**
- **All-in-One Solution**: Complete web development stack in a single package
- **Cross-Platform**: Compatible with Windows, macOS, and Linux
- **Easy Setup**: Simple installation and configuration process
- **Local Development**: Perfect for testing and development without external hosting
- **Integrated Tools**: Built-in phpMyAdmin for efficient database management

**System Requirements:**
- XAMPP v3.3.0 or higher
- PHP 7.4+ (included with XAMPP)
- MySQL 5.7+ (included with XAMPP)
- Apache 2.4+ (included with XAMPP)

## 🗄️ Database Setup

### Database Configuration with phpMyAdmin

The shopRADO platform uses a MySQL database that can be easily set up through phpMyAdmin, which comes integrated with XAMPP.

### Step 1: Creating the Database.

**Database Name:** `shoprado`

### Database Schema

The application uses three main tables to handle user management, product catalog, and shopping cart functionality:

**Database Tables Overview:**
- **users** - Stores customer and admin account information
- **products** - Contains product catalog with details and pricing
- **cart** - Manages shopping cart items for each user

### Table Structure & SQL Scripts

#### Users Table
Stores user account information with role-based access control.

**Structure:**
- `id` - Primary key, auto-increment
- `username` - User display name (50 characters max)
- `email` - Unique email address (100 characters max)
- `password` - Encrypted password (255 characters for hash)
- `created_at` - Account creation timestamp
- `role` - User role (user/admin) with default as 'user'

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('user', 'admin') DEFAULT 'user'
);
```

#### Products Table
Contains product catalog information with pricing and media.

**Structure:**
- `id` - Primary key, auto-increment
- `name` - Product name (255 characters max)
- `price` - Product price (decimal with 2 decimal places)
- `description` - Product description (text field)
- `image` - Product image filename (255 characters max)
- `created_at` - Product creation timestamp

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Cart Table
Manages shopping cart functionality with user-product relationships.

**Structure:**
- `id` - Primary key, auto-increment
- `user_id` - Foreign key linking to users table
- `product_id` - Foreign key linking to products table
- `quantity` - Number of items in cart
- `created_at` - Item addition timestamp
- `updated_at` - Last modification timestamp

```sql
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Database Setup Instructions

1. **Open phpMyAdmin** through XAMPP Control Panel
2. **Create Database:**
   - Click "New" in the left sidebar
   - Enter database name: `shoprado`
   - Click "Create"
3. **Create Tables:**
   - Select the `shoprado` database
   - Go to "SQL" tab
   - Copy and paste each table creation script above
   - Execute each script to create the tables
4. **Verify Setup:**
   - Check that all three tables are created successfully
   - Verify table structures match the specifications above



### Step 2: Setting Up `db.php`

1. **Create Project Structure:**
   - Create a folder named `includes` in your project root directory
   - This folder will contain all configuration and utility files

2. **Database Configuration File:**
   - Inside the `includes` folder, create a file named `db.php`
   - This file handles the MySQL database connection

**File Path:** `includes/db.php`

```php
<?php
$host = 'localhost';
$dbname = 'shoprado';
$user = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
```

**Configuration Details:**
- **Host:** `localhost` (XAMPP default)
- **Database Name:** `shoprado` (as created in phpMyAdmin)
- **Username:** `root` (XAMPP default MySQL user)
- **Password:** Empty string (XAMPP default - no password)

**PDO Benefits:**
- **Security:** Prepared statements prevent SQL injection
- **Portability:** Works with multiple database systems
- **Error Handling:** Comprehensive exception handling
- **Performance:** Efficient database operations

### Step 3: Testing Database Connection

Create a test file to verify your database connection is working properly.

**File Path:** `test_db.php` (in project root)

```php
<?php
include 'includes/db.php';

if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Failed to connect to the database.";
}
?>
```

**Testing Instructions:**
1. Save the test file in your project root directory
2. Ensure XAMPP Apache and MySQL services are running
3. Open your web browser and navigate to: `http://localhost/shopRADO/test_db.php`
4. Verify the message **"Database connected successfully!"** appears
5. If successful, you can delete the test file (optional)

**Troubleshooting Connection Issues:**
- Ensure MySQL service is running in XAMPP Control Panel
- Verify database name matches exactly: `shoprado`
- Check that all tables are created in phpMyAdmin
- Confirm the `includes` folder path is correct

---

## 🌐 Application Pages Overview

### 👥 Customer Interface

| Page | Description | URL |
|------|-------------|-----|
| **🔐 Register** | Enables new users to create an account by providing a username, email, and secure password. All passwords are hashed before storage, and roles are assigned as `user` by default. | [http://localhost/ecommerce/pages/register.php](http://localhost/ecommerce/pages/register.php) |
| **🔑 Login** | Authenticates users via email and password, initializing secure sessions to manage access to protected pages and cart functionality. | [http://localhost/ecommerce/pages/login.php](http://localhost/ecommerce/pages/login.php) |
| **🚪 Logout** | Logs out the user and destroys session data, redirecting back to the homepage for a secure exit. | [http://localhost/ecommerce/pages/logout.php](http://localhost/ecommerce/pages/logout.php) |
| **🏠 Home (Product Listing)** | Serves as the storefront showcasing all available products. Users can browse, view details, and add items to their shopping cart. | [http://localhost/ecommerce/index.php](http://localhost/ecommerce/index.php) |
| **🛒 Shopping Cart** | Displays all items added to the user’s cart. Allows updates to quantity, item removal, and prepares the data for future checkout steps. | [http://localhost/ecommerce/pages/cart.php](http://localhost/ecommerce/pages/cart.php) |

---

### 🛠️ Admin Interface

| Page | Description | URL |
|------|-------------|-----|
| **🔐 Admin Login** | Secure admin login portal. Grants access only to users with the `admin` role defined in the `users` table. | [http://localhost/ecommerce/admin/login.php](http://localhost/ecommerce/admin/login.php) |
| **🚪 Admin Logout** | Terminates the admin session and redirects securely to the login screen. | [http://localhost/ecommerce/admin/logout.php](http://localhost/ecommerce/admin/logout.php) |
| **📊 Dashboard** | Centralized control panel for administrators. Displays an overview of system activity such as product count, users, and quick links to management tools. | [http://localhost/ecommerce/admin/dashboard.php](http://localhost/ecommerce/admin/dashboard.php) |
| **📦 Manage Products** | Lists all products with admin options to edit or delete each item. Data is dynamically fetched from the `products` table. | [http://localhost/ecommerce/admin/manage_products.php](http://localhost/ecommerce/admin/manage_products.php) |
| **➕ Add Product** | A form-based interface for adding new products. Supports product name, price, description, and image upload. | [http://localhost/ecommerce/admin/add_product.php](http://localhost/ecommerce/admin/add_product.php) |

---

## 🌐 Detailed Page-by-Page Overview

This section provides a comprehensive explanation of all the key pages in the shopRADO e-commerce platform, covering both customer-facing and administrative functionalities.

---

### 👥 Customer-Facing Pages

**🔐 Register** 

![image](https://github.com/user-attachments/assets/a649f6dc-611b-4e2c-b823-147f9b148a9c)

📍 `http://localhost/ecommerce/pages/register.php`  
The registration page allows new users to create an account on the platform. It captures essential details like username, email, and password. The backend performs robust validation, ensuring unique email addresses and secure password criteria. Passwords are encrypted using industry-standard hashing techniques before being stored in the database. Once registered, users are automatically assigned the role of `user` and can immediately begin shopping. This page ensures a smooth onboarding experience for new customers.

---

**🔑 Login**  

![image](https://github.com/user-attachments/assets/4466578b-89b6-44cc-a623-b6be1136b163)

📍 `http://localhost/ecommerce/pages/login.php`  
This page authenticates returning users by verifying their login credentials against the database. It uses secure session management to maintain user state throughout their shopping experience. Once logged in, users gain access to restricted pages such as the cart and checkout process. Proper error handling ensures users receive appropriate messages in case of invalid login attempts or missing data.

---

**🚪 Logout**  
📍 `http://localhost/ecommerce/pages/logout.php`  
The logout page securely ends the user's session, clears session variables, and redirects them to the homepage. This action ensures that no residual session data remains, maintaining user privacy and protecting against unauthorized access. It’s a vital component of secure user session management in the application.

---

**🏠 Home (Product Listing)**  

![image](https://github.com/user-attachments/assets/a5eb8b92-0d39-4d58-a3d9-dc49f06d6266)

📍 `http://localhost/ecommerce/index.php`  
The homepage serves as the storefront for the platform, dynamically fetching and displaying all available products from the database. Each product entry includes its image, name, price, and an “Add to Cart” button for immediate interaction. The layout is user-centric, making product discovery seamless. This page is the heart of the customer experience, offering real-time access to inventory and enabling impulse and informed purchases.

---

**🛒 Shopping Cart**  

![image](https://github.com/user-attachments/assets/dca3b0c2-329e-49d0-80c1-f4a913e27476)


📍 `http://localhost/ecommerce/pages/cart.php`  
This page provides users with a detailed view of the items they have added to their cart. It supports modifying item quantities, removing products, and preparing for checkout. Cart data is linked to the logged-in user or session, ensuring personalized experiences. The cart reflects live data from the database and adapts in real-time, making it a critical component of the shopping workflow.

---

### 🛠️ Admin Pages

**🔐 Admin Login** 

![image](https://github.com/user-attachments/assets/bb30c5f6-0b62-400a-8cdb-0aadd4489409)
 
📍 `http://localhost/ecommerce/admin/login.php`  
A secure access point designed specifically for administrators. This page authenticates admin accounts using the `users` table, checking for the `admin` role. Only verified admins can proceed to the backend dashboard. It enforces strict access control and prevents unauthorized users from accessing sensitive functionalities.

---

**🚪 Admin Logout**  
📍 `http://localhost/ecommerce/admin/logout.php`  
This page securely terminates the current admin session. It ensures that all admin-related session data is cleared, returning the user to the login page. It's a fundamental part of maintaining a secure and compliant administrative environment.

---

**📊 Admin Dashboard**  

![image](https://github.com/user-attachments/assets/f8a6d600-555c-4672-8e64-a5d741a92abe)

📍 `http://localhost/ecommerce/admin/dashboard.php`  
The central control panel for all administrative tasks. The dashboard provides a snapshot of system metrics such as the number of products listed, user registrations, and overall system performance. It includes navigation links to all major admin tools and streamlines workflow for store management. The dashboard enhances operational visibility and decision-making for administrators.

---

**📦 Manage Products**  

![image](https://github.com/user-attachments/assets/c4312955-4c36-4b88-a649-086a478b7d5d)

📍 `http://localhost/ecommerce/admin/manage_products.php`  
This page enables admins to view a full list of all products currently available in the store. It provides options to edit or delete any product directly. Each action is tied to database operations, and all updates are reflected instantly in the customer-facing store. The interface is designed for efficiency and ease of use, making inventory control smooth and accurate.

---

**➕ Add Product**  

![image](https://github.com/user-attachments/assets/b16bcc35-4c11-47f5-a649-f438628a9033)

📍 `http://localhost/ecommerce/admin/add_product.php`  
This form-based page allows administrators to add new products to the platform. It supports uploading product images, entering names, descriptions, and setting prices. All submitted data is validated before insertion into the `products` table. The newly added products appear instantly on the homepage, making this page essential for expanding the product catalog and keeping the storefront fresh.

---
---

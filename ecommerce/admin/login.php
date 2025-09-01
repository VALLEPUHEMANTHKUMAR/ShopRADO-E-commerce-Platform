<?php
include '../includes/db.php';
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email belongs to an admin
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Start admin session and redirect to dashboard
        $_SESSION['admin_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid credentials or not an admin.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ShopRADO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4285f4;
            --primary-hover: #3367d6;
            --secondary: #ff4081;
            --text-dark: #333;
            --text-light: #777;
            --white: #fff;
            --light-bg: #f5f5f5;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background-color: var(--white);
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 100%;
            z-index: 10;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header .container {
            display: flex;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
        }
        
        .logo i {
            color: var(--secondary);
            margin-right: 8px;
            font-size: 28px;
        }
        
        .split-screen {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        
        .left-half {
            flex: 1;
            background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://via.placeholder.com/1000x800');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: var(--white);
            padding: 40px;
            position: relative;
        }
        
        .welcome-content {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
            z-index: 2;
        }
        
        .welcome-content h1 {
            font-size: 42px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .welcome-content p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .right-half {
            flex: 1;
            background-color: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            background-color: var(--white);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .login-container h2 {
            font-size: 32px;
            margin-bottom: 10px;
            color: var(--text-dark);
        }
        
        .login-subtext {
            color: var(--text-light);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s ease;
        }
        
        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
        }
        
        .form-group .input-with-icon {
            position: relative;
        }
        
        .form-group .input-with-icon i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        
        .form-group .input-with-icon input {
            padding-right: 40px;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 8px;
        }
        
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #ddd;
        }
        
        .divider span {
            background-color: var(--white);
            padding: 0 15px;
            position: relative;
            color: var(--text-light);
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--white);
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
        }
        
        .facebook {
            background-color: #3b5998;
        }
        
        .google {
            background-color: #dd4b39;
        }
        
        .twitter {
            background-color: #1da1f2;
        }
        
        .apple {
            background-color: #000;
        }
        
        .error-message {
            background-color: #fff4f4;
            border-left: 4px solid #ff5252;
            color: #d32f2f;
            padding: 12px 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .split-screen {
                flex-direction: column;
                overflow-y: auto;
            }
            
            .left-half {
                display: none;
            }
            
            .right-half {
                padding: 20px;
            }
            
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="../index.php" class="logo">
                <i class="fas fa-shopping-bag"></i>
                ShopRADO
            </a>
        </div>
    </header>
    
    <div class="split-screen">
        <div class="left-half">
            <div class="welcome-content">
                <h1>Welcome to Admin Panel</h1>
                <p>Discover amazing products and shop with confidence on our secure platform.</p>
            </div>
        </div>
        
        <div class="right-half">
            <div class="login-container">
                <h2>Sign In</h2>
                <p class="login-subtext">Enter your credentials to access your account</p>
                
                <?php if(isset($error_message)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <input type="email" id="email" name="email" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <input type="password" id="password" name="password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    
                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary">Sign In</button>
                </form>
                
                <div class="divider">
                    <span>Or sign in with</span>
                </div>
                
                <div class="social-login">
                    <div class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="social-btn google">
                        <i class="fab fa-google"></i>
                    </div>
                    <div class="social-btn twitter">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <div class="social-btn apple">
                        <i class="fab fa-apple"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add focus effect to input icons
        const inputs = document.querySelectorAll('.input-with-icon input');
        
        inputs.forEach(input => {
            const icon = input.nextElementSibling;
            
            input.addEventListener('focus', () => {
                icon.style.color = '#4285f4';
            });
            
            input.addEventListener('blur', () => {
                icon.style.color = '#aaa';
            });
        });
    </script>
</body>
</html>
<?php
include('../includes/db.php');  // Database connection
session_start();

$error_message = '';
$success_message = '';

if (isset($_POST['register'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match. Please try again.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; // Default role for users

        try {
            // Check if the email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $error_message = "This email is already registered. Please try logging in instead.";
            } else {
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
                $result = $stmt->execute([$email, $password_hash, $role]);
                
                if ($result) {
                    // Log the user in after successful registration
                    $_SESSION['user_id'] = $conn->lastInsertId();
                    $success_message = "Registration successful! Redirecting...";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '../pages/login.php';
                        }, 2000);
                    </script>";
                } else {
                    $error_message = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "System error. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | ShopRADO</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            min-height: 100vh;
            overflow-y: auto;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        .left-section {
            flex: 1;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/signup bg.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            position: relative;
        }
        
        .left-content {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }
        
        .welcome-text {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .welcome-description {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            min-width: 400px;
            max-width: 600px;
            background-color: white;
            overflow-y: auto;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .logo-icon {
            color: #ff0088;
            margin-right: 10px;
            font-size: 24px;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #3498db;
        }
        
        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .form-header {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-header h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #777;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            cursor: pointer;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: #fde8e8;
            color: #e53e3e;
            border-left: 4px solid #e53e3e;
        }
        
        .alert-success {
            background-color: #e6f6ea;
            color: #38a169;
            border-left: 4px solid #38a169;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #777;
        }
        
        .divider::before, 
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        
        .divider span {
            padding: 0 10px;
            font-size: 14px;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            transition: transform 0.3s;
        }
        
        .social-btn:hover {
            transform: scale(1.1);
        }
        
        .facebook {
            background-color: #3b5998;
        }
        
        .google {
            background-color: #db4437;
        }
        
        .twitter {
            background-color: #1da1f2;
        }
        
        .apple {
            background-color: #000;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
        
        .login-link a {
            color: #3498db;
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .form-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        
        /* Strength meter and helpers */
        .strength-meter {
            height: 4px;
            background-color: #edf2f7;
            margin-top: 8px;
            border-radius: 2px;
            position: relative;
        }
        
        .strength-meter-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease, background-color 0.3s ease;
            width: 0;
        }
        
        .strength-text, 
        .password-match {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .left-section {
                display: none;
            }
            
            .right-section {
                padding: 30px;
                max-width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .right-section {
                padding: 20px;
                min-width: 100%;
            }
            
            .form-header h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="left-content">
                <h1 class="welcome-text">Start Your Shopping Journey!</h1>
                <p class="welcome-description">Create an account to discover amazing products and shop with confidence on our secure platform.</p>
            </div>
        </div>
        
        <div class="right-section">
            <div class="logo-container">
                <i class="fas fa-shopping-bag logo-icon"></i>
                <span class="logo-text">ShopRADO</span>
            </div>
            
            <div class="form-container">
                <div class="form-header">
                    <h2>Sign Up</h2>
                    <p>Create your account to get started</p>
                </div>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-meter-fill" id="strengthMeter"></div>
                        </div>
                        <span class="strength-text" id="strengthText"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                        </div>
                        <span class="password-match" id="passwordMatch"></span>
                    </div>
                    
                    <button type="submit" name="register" class="btn">Sign Up</button>
                </form>
                
                <div class="divider">
                    <span>Or sign up with</span>
                </div>
                
                <div class="social-login">
                    <a href="#" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-btn google">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="social-btn twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-btn apple">
                        <i class="fab fa-apple"></i>
                    </a>
                </div>
                
                <div class="login-link">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
                
                <div class="form-footer">
                    By creating an account, you agree to our Terms of Service and Privacy Policy
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility for password field
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Toggle password visibility for confirm password field
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirm_password');
            const icon = this;
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Password strength meter
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const meter = document.getElementById('strengthMeter');
            const text = document.getElementById('strengthText');
            
            // Simple password strength calculation
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 25;
            
            // Contains lowercase
            if (/[a-z]/.test(password)) strength += 25;
            
            // Contains uppercase
            if (/[A-Z]/.test(password)) strength += 25;
            
            // Contains number or special char
            if (/[0-9!@#$%^&*()]/.test(password)) strength += 25;
            
            // Update the meter
            meter.style.width = strength + '%';
            
            // Color based on strength
            if (strength < 25) {
                meter.style.backgroundColor = '#e53e3e'; // Red
                text.textContent = 'Weak';
                text.style.color = '#e53e3e';
            } else if (strength < 50) {
                meter.style.backgroundColor = '#ed8936'; // Orange
                text.textContent = 'Fair';
                text.style.color = '#ed8936';
            } else if (strength < 75) {
                meter.style.backgroundColor = '#3498db'; // Blue
                text.textContent = 'Good';
                text.style.color = '#3498db';
            } else {
                meter.style.backgroundColor = '#38a169'; // Green
                text.textContent = 'Strong';
                text.style.color = '#38a169';
            }
            
            // Check password match
            checkPasswordMatch();
        });
        
        // Check if passwords match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchText.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchText.textContent = 'Passwords match';
                matchText.style.color = '#38a169'; // Green
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.style.color = '#e53e3e'; // Red
            }
        }
        
        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);
    </script>
</body>
</html>
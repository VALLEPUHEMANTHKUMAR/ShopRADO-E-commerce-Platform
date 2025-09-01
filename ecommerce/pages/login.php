<?php
include('../includes/db.php');  // Include the database connection
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        header("Location: ../index.php"); // Redirect to the main page
        exit();
    } else {
        // Invalid login
        $error_message = "Invalid email or password.";
        
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ShopRADO</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3a86ff;
            --primary-dark: #2667cc;
            --secondary: #ff006e;
            --accent: #ffbe0b;
            --text-dark: #333333;
            --text-light: #666666;
            --text-lighter: #999999;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --border-color: #e1e1e1;
            --success: #02c39a;
            --warning: #ffaa00;
            --error: #e63946;
            --shadow-sm: 0 2px 5px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 20px rgba(0,0,0,0.12);
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 16px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-wrapper {
            display: flex;
            flex-grow: 1;
            min-height: 100vh;
        }

        /* Header Styles */
        header {
            background-color: var(--bg-white);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .logo i {
            color: var(--secondary);
        }

        /* Login Container */
        .split-container {
            display: flex;
            flex-grow: 1;
        }

        .login-image {
            flex: 1;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/login bg.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 2rem;
            position: relative;
        }

        .login-image-content {
            text-align: center;
            max-width: 80%;
            z-index: 2;
        }

        .login-image h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .login-image p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .login-form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-form {
            width: 100%;
            max-width: 450px;
            background-color: var(--bg-white);
            padding: 2.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-size: 2rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.15);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: var(--text-lighter);
        }

        .input-with-icon {
            padding-left: 2.75rem;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-lighter);
            cursor: pointer;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            margin-right: 0.5rem;
            width: 1rem;
            height: 1rem;
        }

        .form-check-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 1.5rem;
        }

        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-loading .btn-text {
            display: none;
        }

        .btn-loading .spinner {
            display: inline-block;
        }

        .spinner {
            display: none;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .social-login {
            margin-top: 2rem;
            text-align: center;
        }

        .social-login-text {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .social-login-text::before,
        .social-login-text::after {
            content: "";
            flex-grow: 1;
            height: 1px;
            background-color: var(--border-color);
        }

        .social-login-text::before {
            margin-right: 1rem;
        }

        .social-login-text::after {
            margin-left: 1rem;
        }

        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            transition: var(--transition);
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
            background-color: #000000;
        }

        .social-button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .register-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: rgba(230, 57, 70, 0.1);
            color: var(--error);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .success-message {
            background-color: rgba(2, 195, 154, 0.1);
            color: var(--success);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .toast {
            background-color: white;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            min-width: 300px;
            max-width: 400px;
            transform: translateX(120%);
            transition: transform 0.3s ease;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.success {
            border-left: 4px solid var(--success);
        }

        .toast.error {
            border-left: 4px solid var(--error);
        }

        .toast-icon {
            margin-right: 0.75rem;
            font-size: 1.5rem;
        }

        .toast.success .toast-icon {
            color: var(--success);
        }

        .toast.error .toast-icon {
            color: var(--error);
        }

        .toast-content {
            flex-grow: 1;
        }

        .toast-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .toast-message {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-lighter);
            cursor: pointer;
            margin-left: 0.5rem;
        }

        /* Form validation styling */
        .form-control.is-invalid {
            border-color: var(--error);
        }

        .invalid-feedback {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none;
        }

        .form-control.is-invalid + .invalid-feedback {
            display: block;
        }

        /* Responsiveness */
        @media (max-width: 992px) {
            .login-image {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .login-form {
                padding: 1.5rem;
            }

            .login-header h2 {
                font-size: 1.75rem;
            }

            .social-buttons {
                flex-wrap: wrap;
            }
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
        </div>
    </header>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Main Content -->
    <div class="page-wrapper">
        <!-- Login Image Section -->
        <div class="login-image">
            <div class="login-image-content">
                <h2>Welcome Back!</h2>
                <p>Discover amazing products and shop with confidence on our secure platform.</p>
            </div>
        </div>

        <!-- Login Form Section -->
        <div class="login-form-container">
            <div class="login-form">
                <div class="login-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>

                

                <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error_message); ?></span>
                </div>
                <?php endif; ?>

                <form id="loginForm" method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control input-with-icon" id="email" name="email" placeholder="Enter your email" required>
                            <div class="invalid-feedback">Please enter a valid email address</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control input-with-icon" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="invalid-feedback">Password is required</div>
                        </div>
                    </div>

                    <div class="form-row" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <div class="forgot-password">
                            <a href="forgot-password.php">Forgot Password?</a>
                        </div>
                    </div>

                    <button type="submit" name="login" id="loginButton" class="btn btn-primary">
                        <span class="btn-text">Sign In</span>
                        <div class="spinner"></div>
                    </button>
                </form>

                <div class="social-login">
                    <div class="social-login-text">Or sign in with</div>
                    <div class="social-buttons">
                        <a href="#" class="social-button facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-button google">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-button twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-button apple">
                            <i class="fab fa-apple"></i>
                        </a>
                    </div>
                </div>

                <div class="register-link">
                    Don't have an account? <a href="register.php">Sign Up</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');

            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
            });

            // Form validation
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const loginButton = document.getElementById('loginButton');

            // Email validation function
            function validateEmail(email) {
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            }

            // Real-time email validation
            emailInput.addEventListener('input', function() {
                if (!validateEmail(this.value) && this.value !== '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Submit form with AJAX
            loginForm.addEventListener('submit', function(e) {
                // Prevent default form submission
                e.preventDefault();

                // Validate form
                let isValid = true;

                if (!validateEmail(emailInput.value)) {
                    emailInput.classList.add('is-invalid');
                    isValid = false;
                }

                if (passwordInput.value.trim() === '') {
                    passwordInput.classList.add('is-invalid');
                    isValid = false;
                }

                if (!isValid) {
                    return;
                }

                // Show loading state
                loginButton.classList.add('btn-loading');

                // Get form data
                const formData = new FormData(loginForm);

                // Send AJAX request
                fetch(loginForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading state
                    loginButton.classList.remove('btn-loading');

                    if (data.success) {
                        // Show success toast
                        showToast('success', 'Login Successful', 'Redirecting to dashboard...');
                        
                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        // Show error toast
                        showToast('error', 'Login Failed', data.message);
                    }
                })
                .catch(error => {
                    // Hide loading state
                    loginButton.classList.remove('btn-loading');
                    
                    // Show error toast
                    showToast('error', 'Error', 'An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            });

            // Toast notification function
            function showToast(type, title, message) {
                const toastContainer = document.getElementById('toastContainer');
                
                // Create toast element
                const toast = document.createElement('div');
                toast.className = 'toast ' + type;
                
                // Set toast content
                toast.innerHTML = `
                    <div class="toast-icon">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                    <button class="toast-close">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                // Add toast to container
                toastContainer.appendChild(toast);
                
                // Show toast with animation
                setTimeout(() => {
                    toast.classList.add('show');
                }, 10);
                
                // Add event listener to close button
                toast.querySelector('.toast-close').addEventListener('click', () => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                });
                
                // Auto close after 5 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            }

            // Show success toast if redirected from registration
            <?php if ($reg_success): ?>
            setTimeout(() => {
                showToast('success', 'Registration Successful', 'You can now login with your credentials.');
            }, 500);
            <?php endif; ?>
        });
    </script>
</body>
</html>
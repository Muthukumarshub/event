<?php
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $role = $_POST['role']; // 'organizer' or 'user'

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        $success = true;
        $message = "Registration successful!";
    } else {
        $error = true;
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        /* Modern color scheme and base styles */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(78, 84, 200, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.5s ease;
        }
        
        .success-modal {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            width: 400px;
            padding: 40px;
            text-align: center;
            animation: scaleUp 0.5s ease;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #2ecc71;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            fill: none;
            stroke: white;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        
        .success-title {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .success-message {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .success-button {
            display: inline-block;
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(78, 84, 200, 0.3);
        }
        
        .success-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(78, 84, 200, 0.4);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes scaleUp {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        /* Error message styling */
        .error-message-container {
            color: #ff6b6b;
            background-color: rgba(255, 107, 107, 0.1);
            border-left: 4px solid #ff6b6b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
            font-size: 15px;
            animation: fadeIn 0.5s ease;
        }
        :root {
            --primary-color: #4e54c8;
            --primary-gradient: linear-gradient(to right, #4e54c8, #8f94fb);
            --secondary-color: #f9f9f9;
            --accent-color: #ff6b6b;
            --text-color: #333;
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--primary-gradient);
            position: relative;
            overflow: hidden;
        }
        
        /* Animated background */
        body::before {
            content: "";
            position: absolute;
            width: 150%;
            height: 150%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1600 800'%3E%3Cpolygon fill='%236a6fc9' points='1600 160 0 460 0 350 1600 50'/%3E%3Cpolygon fill='%237a7fd1' points='1600 260 0 560 0 450 1600 150'/%3E%3Cpolygon fill='%238a8fd9' points='1600 360 0 660 0 550 1600 250'/%3E%3Cpolygon fill='%239a9fe1' points='1600 460 0 760 0 650 1600 350'/%3E%3Cpolygon fill='%23abaee8' points='1600 800 0 800 0 750 1600 450'/%3E%3C/svg%3E");
            background-size: cover;
            z-index: -1;
            animation: move-background 15s linear infinite;
            opacity: 0.7;
        }
        
        @keyframes move-background {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            100% {
                transform: translateY(-50px) rotate(5deg);
            }
        }
        
        .container {
            width: 450px;
            background-color: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 40px;
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        
        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 600;
            position: relative;
        }
        
        h2::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 4px;
            background: var(--primary-gradient);
            border-radius: 10px;
        }
        
        form {
            display: flex;
            flex-direction: column;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .input-group label {
            position: absolute;
            top: 16px;
            left: 15px;
            color: #999;
            font-size: 16px;
            transition: all 0.3s ease;
            pointer-events: none;
        }
        
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .input-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            cursor: pointer;
        }
        
        .input-group input:focus,
        .input-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
            outline: none;
        }
        
        .input-group input:focus + label,
        .input-group input:valid + label {
            top: -12px;
            left: 10px;
            font-size: 13px;
            background: white;
            padding: 0 5px;
            color: var(--primary-color);
        }
        
        /* For select, handle label differently */
        .input-group.select-group label {
            top: -12px;
            left: 10px;
            font-size: 13px;
            background: white;
            padding: 0 5px;
            color: var(--primary-color);
        }
        
        button {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(78, 84, 200, 0.3);
        }
        
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(78, 84, 200, 0.4);
        }
        
        button:active {
            transform: translateY(1px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .login-link a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        
        /* Success message styling */
        .success-message {
            color: #2ecc71;
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid #2ecc71;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
            font-size: 15px;
            animation: fadeIn 0.5s ease;
        }
        
        /* Error message styling */
        .error-message {
            color: var(--accent-color);
            background-color: rgba(255, 107, 107, 0.1);
            border-left: 4px solid var(--accent-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
            font-size: 15px;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Role selection enhancement */
        .role-selector {
            display: flex;
            margin-bottom: 25px;
        }
        
        .role-option {
            flex: 1;
            position: relative;
        }
        
        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .role-option label {
            display: block;
            padding: 15px 10px;
            text-align: center;
            background: #f5f5f5;
            border: 2px solid #eee;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .role-option:first-child label {
            border-radius: 8px 0 0 8px;
        }
        
        .role-option:last-child label {
            border-radius: 0 8px 8px 0;
        }
        
        .role-option input[type="radio"]:checked + label {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(78, 84, 200, 0.3);
        }
    </style>
</head>
<body>
<?php if(isset($success) && $success): ?>
    <!-- Success Overlay -->
    <div class="success-overlay">
        <div class="success-modal">
            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h2 class="success-title">Registration Successful!</h2>
            <p class="success-message">Your account has been created successfully. You can now log in with your credentials.</p>
            <a href="login.php" class="success-button">Go to Login</a>
        </div>
    </div>
    <?php else: ?>
    <?php endif; ?>
    <div class="container">
        <h2>Create Account</h2>
        <?php if(isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="input-group">
                <input type="text" name="name" id="name" required>
                <label for="name">Full Name</label>
            </div>
            
            <div class="input-group">
                <input type="email" name="email" id="email" required>
                <label for="email">Email Address</label>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" id="password" required>
                <label for="password">Password</label>
            </div>
            
            <!-- Alternative role selector with radio buttons
            <div class="input-group select-group">
                <label for="role">Account Type</label>
                <select name="role" id="role" required>
                    <option value="user">User</option>
                    <option value="organizer">Organizer</option>
                </select>
            </div>
            -->
             
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" name="role" id="role-user" value="user" checked>
                    <label for="role-user">User</label>
                </div>
                <div class="role-option">
                    <input type="radio" name="role" id="role-organizer" value="organizer">
                    <label for="role-organizer">Organizer</label>
                </div>
            </div>
            
            
            <button type="submit">Create Account</button>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Login now</a>
            </div>
        </form>
    </div>
    
    <script>
        // Add some interactivity with JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Focus effect for text inputs
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
            
            inputs.forEach(input => {
                // Check on page load if inputs have values
                if (input.value.trim() !== '') {
                    input.nextElementSibling.classList.add('active');
                }
                
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
            
            // Button effect
            const button = document.querySelector('button');
            button.addEventListener('mousedown', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            button.addEventListener('mouseup', function() {
                this.style.transform = 'scale(1)';
            });
            
            // Success message fade-out
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.opacity = '0';
                    successMessage.style.transform = 'translateY(-10px)';
                    successMessage.style.transition = 'all 0.5s ease';
                }, 5000);
            }
        });
    </script>
</body>
</html>
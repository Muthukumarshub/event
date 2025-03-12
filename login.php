<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['email'] = $row['email'];


            if ($row['role'] === 'organizer') {
                header("Location: organizer_dashboard.php"); // Redirect to Organizer Dashboard
            } else {
                header("Location: user_dashboard.php"); // Redirect to User Dashboard
            }
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        /* Modern color scheme and base styles */
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
            width: 400px;
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
        
        .input-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .input-group input:focus {
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
        
        .register-link {
            text-align: center;
            margin-top: 25px;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .register-link a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        
        /* Error message styling */
        .error-message {
            color: var(--accent-color);
            background-color: rgba(255, 107, 107, 0.1);
            border-left: 4px solid var(--accent-color);
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
            font-size: 14px;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome Back</h2>
        <?php if(isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="input-group">
                <input type="email" name="email" id="email" required>
                <label for="email">Email Address</label>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" id="password" required>
                <label for="password">Password</label>
            </div>
            
            <button type="submit">Sign In</button>
            
            <div class="register-link">
                Don't have an account? <a href="register.php">Register now</a>
            </div>
        </form>
    </div>
    
    <script>
        // Add some interactivity with JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Focus effect
            const inputs = document.querySelectorAll('input');
            
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
        });
    </script>
</body>
</html>
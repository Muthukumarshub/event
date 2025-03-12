<?php
include "db.php";

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if event_id is provided in URL
if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch event details
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if event exists
if (mysqli_num_rows($result) == 0) {
    header("Location: user_dashboard.php");
    exit();
}

$event = mysqli_fetch_assoc($result);

// Check if user is already registered for this event
$checkRegistration = "SELECT * FROM attendees WHERE event_id = ? AND email = ?";
$checkStmt = mysqli_prepare($conn, $checkRegistration);
mysqli_stmt_bind_param($checkStmt, "is", $event_id, $user_id);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
$alreadyRegistered = mysqli_num_rows($checkResult) > 0;

// Process form submission
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $ticket_type = $_POST['ticket_type'];
    
    // Basic validation
    if (empty($full_name) || empty($email) || empty($phone_number)) {
        $message = "All fields are required.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } else {
        // Check if already registered with this email
        $checkEmail = "SELECT * FROM attendees WHERE event_id = ? AND email = ?";
        $emailStmt = mysqli_prepare($conn, $checkEmail);
        mysqli_stmt_bind_param($emailStmt, "is", $event_id, $email);
        mysqli_stmt_execute($emailStmt);
        $emailResult = mysqli_stmt_get_result($emailStmt);
        
        if (mysqli_num_rows($emailResult) > 0) {
            $message = "You are already registered for this event with this email.";
            $messageType = "error";
        } else {
            // Insert registration
            $insert = "INSERT INTO attendees (event_id, full_name, email, phone_number, ticket_type) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($insertStmt, "issss", $event_id, $full_name, $email, $phone_number, $ticket_type);
            
            if (mysqli_stmt_execute($insertStmt)) {
                $message = "Registration successful! We'll send the event details to your email.";
                $messageType = "success";
                $alreadyRegistered = true;
            } else {
                $message = "Registration failed. Please try again.";
                $messageType = "error";
            }
        }
    }
}

// Calculate days left
$eventDate = new DateTime($event['event_date']);
$today = new DateTime();
$daysLeft = $today->diff($eventDate)->days;

// Handle event image
if (!empty($event['event_image'])) {
    // Check if image path contains a full URL
    if (strpos($event['event_image'], 'http://') === 0 || strpos($event['event_image'], 'https://') === 0) {
        $imagePath = $event['event_image'];
    } else {
        // Check if the path already contains 'uploads/'
        if (strpos($event['event_image'], 'uploads/') === 0) {
            $imagePath = $event['event_image'];
        } else {
            $imagePath = 'uploads/' . $event['event_image'];
        }
    }
} else {
    // Make sure the default image path exists
    $imagePath = file_exists('assets/default-event.jpg') ? 'assets/default-event.jpg' : 'https://via.placeholder.com/800x400?text=Event';
}

// Check if the file exists
if (!filter_var($imagePath, FILTER_VALIDATE_URL) && !file_exists($imagePath)) {
    $imagePath = 'https://via.placeholder.com/800x400?text=No+Image';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for <?php echo htmlspecialchars($event['event_name']); ?> - PlanPro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #8a78ff;
            --light-color: #f5f6fa;
            --dark-color: #2d3436;
            --success-color: #00b894;
            --warning-color: #fdcb6e;
            --danger-color: #d63031;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4efe9 100%);
            background-attachment: fixed;
            position: relative;
            min-height: 100vh;
        }
        
        /* Decorative elements for background */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(108, 92, 231, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 80% 60%, rgba(0, 184, 148, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 40% 80%, rgba(253, 203, 110, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 10%, rgba(108, 92, 231, 0.05) 0%, transparent 20%);
            z-index: -1;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 20px rgba(108, 92, 231, 0.1);
            backdrop-filter: blur(5px);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            text-shadow: 0 1px 2px rgba(108, 92, 231, 0.2);
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .nav-links a:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .logout-btn {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(108, 92, 231, 0.3);
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
        }
        
        .container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.05),
                rgba(255, 255, 255, 0.05) 10px,
                rgba(255, 255, 255, 0.02) 10px,
                rgba(255, 255, 255, 0.02) 20px
            );
            animation: shine 20s linear infinite;
            z-index: 1;
        }
        
        .page-header h1 {
            position: relative;
            z-index: 2;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        @keyframes shine {
            0% {
                transform: translateX(-50%) translateY(-50%) rotate(0deg);
            }
            100% {
                transform: translateX(-50%) translateY(-50%) rotate(360deg);
            }
        }
        
        .registration-container {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .event-details-card {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            border-top: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .event-details-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }
        
        .event-banner {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .event-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .event-category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .days-left-badge {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background-color: rgba(0, 0, 0, 0.75);
            color: white;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }
        
        .event-content {
            padding: 1.5rem;
        }
        
        .event-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .event-organizer {
            font-style: italic;
            margin-bottom: 1rem;
            color: #636e72;
            border-left: 3px solid var(--primary-color);
            padding-left: 10px;
        }
        
        .event-description {
            color: #636e72;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .event-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .event-info-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.8rem;
            background-color: var(--light-color);
            border-radius: 8px;
            color: var(--dark-color);
        }
        
        .event-info-item i {
            color: var(--primary-color);
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }
        
        .price-badge {
            background: linear-gradient(to right, var(--success-color), #55efc4);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            margin: 0.5rem 0;
            box-shadow: 0 4px 10px rgba(0, 184, 148, 0.2);
        }
        
        .registration-form-container {
            flex: 1;
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            border-top: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .registration-form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }
        
        .form-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .form-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .form-header p {
            opacity: 0.8;
        }
        
        .form-content {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #dfe6e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }
        
        .select-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c5ce7' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.5l-4.5-4.5h9L8 11.5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px;
        }
        
        .submit-btn {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }
        
        .submit-btn:hover::before {
            left: 100%;
        }
        
        .submit-btn:hover {
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.4);
            transform: translateY(-2px);
        }
        
        .submit-btn:disabled {
            background: #b2bec3;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .submit-btn:disabled::before {
            display: none;
        }
        
        .back-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .message.success {
            background-color: rgba(0, 184, 148, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .message.error {
            background-color: rgba(214, 48, 49, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        .already-registered {
            text-align: center;
            padding: 2rem;
            background-color: rgba(108, 92, 231, 0.1);
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .already-registered i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .already-registered h3 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .already-registered p {
            color: #636e72;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 992px) {
            .registration-container {
                flex-direction: column;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .event-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">PlanPro</div>
        <div class="nav-links">
            <a href="user_dashboard.php">Events</a>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
    
    <div class="container">
        <div class="page-header">
            <h1>Event Registration</h1>
        </div>
        
        <div class="registration-container">
            <div class="event-details-card">
                <div class="event-banner">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>" onerror="this.src='https://via.placeholder.com/800x400?text=Image+Not+Found';">
                    <div class="event-category-badge"><?php echo htmlspecialchars($event['event_category']); ?></div>
                    <div class="days-left-badge"><?php echo $daysLeft; ?> days left</div>
                </div>
                <div class="event-content">
                    <div class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></div>
                    <div class="event-organizer">By <?php echo htmlspecialchars($event['organizer_name']); ?>, <?php echo htmlspecialchars($event['company_name']); ?></div>
                    <div class="event-description"><?php echo htmlspecialchars($event['event_description']); ?></div>
                    
                    <div class="event-info-grid">
                        <div class="event-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                        </div>
                        <div class="event-info-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                        </div>
                        <div class="event-info-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                        </div>
                        <div class="event-info-item">
                            <i class="fas fa-users"></i>
                            <span><?php echo htmlspecialchars($event['max_attendees']); ?> Seats</span>
                        </div>
                    </div>
                    
                    <div class="price-badge">
                        Ticket Price: Rs.<?php echo number_format($event['ticket_price'], 2); ?>
                    </div>
                </div>
            </div>
            
            <div class="registration-form-container">
                <div class="form-header">
                    <h2>Register Now</h2>
                    <p>Fill in your details to secure your spot</p>
                </div>
                <div class="form-content">
                    <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
                        <?php echo $message; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($alreadyRegistered): ?>
                    <div class="already-registered">
                        <i class="fas fa-check-circle"></i>
                        <h3>You're All Set!</h3>
                        <p>You have already registered for this event. We've sent the details to your email.</p>
                        <a href="user_dashboard.php" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Back to Events
                        </a>
                    </div>
                    <?php else: ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_id); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control" placeholder="Enter your phone number" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="ticket_type">Ticket Type</label>
                            <select id="ticket_type" name="ticket_type" class="form-control select-control" required>
                                <option value="Regular">Regular</option>
                                <option value="VIP">VIP</option>
                                <option value="Student">Student</option>
                                <option value="Guest">Guest</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-ticket-alt"></i> Confirm Registration
                        </button>
                    </form>
                    
                    <a href="user_dashboard.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Events
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // JavaScript to show fallback image if the image fails to load
        document.addEventListener('DOMContentLoaded', function() {
            const banner = document.querySelector('.event-banner img');
            banner.addEventListener('error', function() {
                this.src = 'https://via.placeholder.com/800x400?text=Image+Not+Found';
            });
        });
    </script>
</body>
</html>
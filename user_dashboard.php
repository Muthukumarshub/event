<?php

include "db.php";

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch events from the database
$sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
$result = mysqli_query($conn, $sql);
$name = $_SESSION['name'];
// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Store events in an array
$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

// Get user information
$userId = $_SESSION['user_id'];
// Using email field instead of id
$userQuery = "SELECT * FROM users WHERE email = '$userId'";
$userResult = mysqli_query($conn, $userQuery);

// Check if the query was successful before fetching
if ($userResult && mysqli_num_rows($userResult) > 0) {
    $userData = mysqli_fetch_assoc($userResult);
    $userName = $userData['name'] ?? $userData['username'] ?? $userData['full_name'] ?? 'User';
} else {
    $userName = 'User'; // Fallback if query fails or no user found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanPro - Events</title>
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
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2.5rem;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
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
        
        .dashboard-header h1 {
            position: relative;
            z-index: 2;
            font-size: 2.5rem;
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
        
        .welcome-card {
            background-color: white;
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .welcome-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        }
        
        .welcome-card h2 {
            font-size: 2.2rem;
            margin-bottom: 0.8rem;
            color: var(--dark-color);
        }
        
        .welcome-card p {
            color: #636e72;
            font-size: 1.2rem;
            line-height: 1.6;
        }
        
        .events-section h3 {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1.8rem;
            color: var(--dark-color);
            font-size: 1.8rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(108, 92, 231, 0.2);
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2.5rem;
        }
        
        .event-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            border-top: 4px solid transparent;
        }
        
        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--primary-color);
        }
        
        .event-image {
            height: 200px;
            overflow: hidden;
            position: relative;
            background-color: #dfe6e9;
        }
        
        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .event-card:hover .event-image img {
            transform: scale(1.05);
        }
        
        .event-category {
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
            z-index: 2;
        }
        
        .days-left {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background-color: rgba(0, 0, 0, 0.75);
            color: white;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 2;
            backdrop-filter: blur(5px);
        }
        
        .event-details {
            padding: 1.8rem;
            flex-grow: 1;
        }
        
        .event-name {
            font-weight: bold;
            margin-bottom: 0.7rem;
            color: var(--dark-color);
            font-size: 1.4rem;
        }
        
        .event-description {
            color: #636e72;
            margin-bottom: 1.2rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .event-organizer {
            font-style: italic;
            margin-bottom: 1rem;
            color: #636e72;
            border-left: 3px solid var(--primary-color);
            padding-left: 10px;
        }
        
        .event-info {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 0.9rem;
            color: #636e72;
            font-size: 0.95rem;
        }
        
        .event-info i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }
        
        .event-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .event-price {
            font-weight: bold;
            color: var(--success-color);
            font-size: 1.2rem;
        }
        
        .event-capacity {
            color: #636e72;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .register-btn {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            text-align: center;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin-top: auto;
            display: block;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .register-btn::before {
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
        
        .register-btn:hover::before {
            left: 100%;
        }
        
        .register-btn:hover {
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.4);
            transform: translateY(-2px);
        }
        
        .no-events {
            grid-column: 1 / -1;
            background-color: white;
            padding: 3rem;
            border-radius: 15px;
            text-align: center;
            color: #636e72;
            font-size: 1.2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 1rem;
            }
            
            .dashboard-header {
                padding: 1.5rem;
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
        <div class="dashboard-header">
            <h1>Discover Events</h1>
        </div>
        
        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
            <p>Browse and register for exciting events happening around you.</p>
        </div>
        
        <div class="events-section">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
            
            <div class="events-grid">
                <?php if (count($events) > 0): ?>
                    <?php foreach ($events as $event): 
                        // Calculate days left
                        $eventDate = new DateTime($event['event_date']);
                        $today = new DateTime();
                        $daysLeft = $today->diff($eventDate)->days;
                        
                        // Fix image path handling
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
                            $imagePath = file_exists('assets/default-event.jpg') ? 'assets/default-event.jpg' : 'https://via.placeholder.com/400x200?text=Event';
                        }
                        
                        // Check if the file exists
                        if (!filter_var($imagePath, FILTER_VALIDATE_URL) && !file_exists($imagePath)) {
                            $imagePath = 'https://via.placeholder.com/400x200?text=No+Image';
                        }
                    ?>
                    <div class="event-card">
                        <div class="event-image">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>" onerror="this.src='https://via.placeholder.com/400x200?text=Image+Not+Found';">
                            <div class="event-category"><?php echo htmlspecialchars($event['event_category']); ?></div>
                            <div class="days-left"><?php echo $daysLeft; ?> days left</div>
                        </div>
                        <div class="event-details">
                            <div class="event-name"><?php echo htmlspecialchars($event['event_name']); ?></div>
                            <div class="event-organizer">By <?php echo htmlspecialchars($event['organizer_name']); ?>, <?php echo htmlspecialchars($event['company_name']); ?></div>
                            <div class="event-description"><?php echo htmlspecialchars(substr($event['event_description'], 0, 150)) . (strlen($event['event_description']) > 150 ? '...' : ''); ?></div>
                            
                            <div class="event-info">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($event['event_location']); ?>
                            </div>
                            <div class="event-info">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                            </div>
                            <div class="event-info">
                                <i class="fas fa-clock"></i>
                                <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                            </div>
                            
                            <div class="event-meta">
                                <div class="event-price">Rs.<?php echo number_format($event['ticket_price'], 2); ?></div>
                                <div class="event-capacity"><i class="fas fa-users"></i> <?php echo htmlspecialchars($event['max_attendees']); ?> seats</div>
                            </div>
                        </div>
                        <!-- Find the correct event ID field -->
                        <?php 
                        $eventId = 0;
                        if (isset($event['id'])) {
                            $eventId = $event['id'];
                        } elseif (isset($event['event_id'])) {
                            $eventId = $event['event_id'];
                        }
                        ?>
                        <a href="register_event.php?event_id=<?php echo $eventId; ?>" class="register-btn">Register Now</a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="no-events">
                    <p>No upcoming events found. Check back later!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // JavaScript to show fallback image if the image fails to load
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.event-image img');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    this.src = 'https://via.placeholder.com/400x200?text=Image+Not+Found';
                });
            });
        });
    </script>
</body>
</html>
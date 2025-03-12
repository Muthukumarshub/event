<?php

include "db.php";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}


// Get organizer's email from session
$email = $_SESSION['email']; // Make sure you have this in your session

// Fetch events created by this organizer
$sql = "SELECT * FROM events WHERE email = ? ORDER BY event_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Count statistics
$totalEvents = $result->num_rows;

// Calculate total potential revenue (based on ticket price × max attendees)
$totalPotentialRevenue = 0;

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
    // Calculate potential revenue
    $totalPotentialRevenue += ($row['ticket_price'] * $row['max_attendees']);
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanPro - Organizer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            color: #333;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><circle cx="30" cy="30" r="30" fill="%23ffffff10"/><circle cx="70" cy="70" r="20" fill="%23ffffff10"/><circle cx="10" cy="70" r="15" fill="%23ffffff10"/></svg>');
            background-size: 100px 100px;
            opacity: 0.1;
            z-index: -1;
            animation: move-bg 40s linear infinite;
        }
        
        @keyframes move-bg {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 100px 100px;
            }
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            background: linear-gradient(to right, #7c4dff, #6c3aef);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-decoration: none;
            position: relative;
        }
        
        .logo::after {
            content: "";
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to right, #7c4dff, #6c3aef);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .logo:hover::after {
            transform: scaleX(1);
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            padding: 5px 0;
        }
        
        .nav-links a::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #7c4dff;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.3s ease;
        }
        
        .nav-links a:hover {
            color: #7c4dff;
        }
        
        .nav-links a:hover::after {
            transform: scaleX(1);
        }
        
        .btn {
            background: linear-gradient(to right, #7c4dff, #6c3aef);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(108, 58, 239, 0.3);
        }
        
        .btn:hover {
            background: linear-gradient(to right, #6c3aef, #5627e5);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 58, 239, 0.4);
        }
        
        .btn:active {
            transform: translateY(1px);
        }
        
        .dashboard {
            max-width: 1200px;
            margin: 50px auto;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        
        .dashboard::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                         rgba(255,255,255,0.3) 0%, 
                         rgba(255,255,255,0.1) 40%, 
                         transparent 60%);
            pointer-events: none;
            z-index: -1;
        }
        
        .dashboard-header {
            background: linear-gradient(45deg, #6c3aef, #7c4dff);
            padding: 40px 30px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: "";
            position: absolute;
            top: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .dashboard-header::after {
            content: "";
            position: absolute;
            bottom: -70px;
            left: 60px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        
        .dashboard-header h1 {
            font-size: 32px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
        }
        
        .dashboard-content {
            padding: 40px;
        }
        
        .greeting {
            margin-bottom: 30px;
            position: relative;
        }
        
        .greeting h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .greeting h2::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #7c4dff, #6c3aef);
            border-radius: 3px;
        }
        
        .greeting p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-top: 40px;
            margin-bottom: 50px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #f2f3f5 100%);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s;
            border: 1px solid rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, #7c4dff, #6c3aef);
        }
        
        .stat-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                         rgba(255,255,255,0.5) 0%, 
                         rgba(255,255,255,0.2) 40%, 
                         transparent 60%);
            pointer-events: none;
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card h3 {
            font-size: 36px;
            background: linear-gradient(to right, #7c4dff, #6c3aef);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 12px;
        }
        
        .stat-card p {
            color: #666;
            font-size: 15px;
            font-weight: 500;
        }
        
        .events-container {
            margin-top: 50px;
        }
        
        .events-heading {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .events-heading i {
            margin-right: 12px;
            color: #7c4dff;
            font-size: 28px;
        }
        
        .events-heading::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(to right, #7c4dff, #6c3aef);
            border-radius: 3px;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
            gap: 30px;
        }
        
        .event-card {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.4s;
            border: 1px solid rgba(0,0,0,0.04);
            position: relative;
        }
        
        .event-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                         rgba(255,255,255,0.2) 0%, 
                         rgba(255,255,255,0.05) 40%, 
                         transparent 60%);
            pointer-events: none;
        }
        
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .event-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
            transition: transform 0.5s;
        }
        
        .event-card:hover .event-image {
            transform: scale(1.05);
        }
        
        .event-category {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(to right, #7c4dff, #6c3aef);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            z-index: 2;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .event-details {
            padding: 25px;
        }
        
        .event-name {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .event-description {
            color: #666;
            margin-bottom: 20px;
            font-size: 15px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .event-meta {
            display: flex;
            align-items: center;
            color: #777;
            font-size: 14px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        
        .event-card:hover .event-meta {
            color: #555;
        }
        
        .event-meta i {
            margin-right: 10px;
            width: 16px;
            text-align: center;
            color: #7c4dff;
        }
        
        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 25px;
            background: linear-gradient(135deg, #f8f9fa 0%, #f2f3f5 100%);
            border-top: 1px solid #eee;
        }
        
        .event-price {
            font-weight: 600;
            color: #7c4dff;
            font-size: 16px;
        }
        
        .event-actions {
            display: flex;
            gap: 18px;
        }
        
        .event-actions a {
            font-size: 14px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .event-actions a::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: currentColor;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }
        
        .event-actions a:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .event-actions a i {
            margin-right: 5px;
            transition: transform 0.3s;
        }
        
        .event-actions a:hover {
            color: #7c4dff;
        }
        
        .event-actions a:hover i {
            transform: translateY(-2px);
        }
        
        .event-actions a.delete-btn {
            color: #ff5252;
        }
        
        .event-actions a.delete-btn:hover {
            color: #ff0000;
        }
        
        .no-events {
            text-align: center;
            padding: 60px 40px;
            color: #888;
            background: linear-gradient(135deg, #f8f9fa 0%, #f2f3f5 100%);
            border-radius: 12px;
            font-size: 18px;
            border: 1px dashed #ddd;
        }
        
        .event-date-badge {
            position: absolute;
            left: 15px;
            top: 15px;
            background-color: white;
            color: #333;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            z-index: 2;
            transition: all 0.3s;
        }
        
        .event-card:hover .event-date-badge {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            text-align: center;
            transform: translateY(20px);
            animation: modal-appear 0.3s forwards;
        }
        
        @keyframes modal-appear {
            to {
                transform: translateY(0);
            }
        }
        
        .modal-title {
            font-size: 22px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .modal-text {
            margin-bottom: 25px;
            color: #666;
            line-height: 1.6;
        }
        
        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .modal-btn {
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
        }
        
        .modal-btn.cancel {
            background-color: #e0e0e0;
            color: #666;
        }
        
        .modal-btn.delete {
            background: linear-gradient(to right, #ff5252, #ff0000);
            color: white;
            box-shadow: 0 4px 10px rgba(255, 82, 82, 0.3);
        }
        
        .modal-btn:hover {
            transform: translateY(-3px);
        }
        
        .modal-btn.cancel:hover {
            background-color: #d0d0d0;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        
        .modal-btn.delete:hover {
            background: linear-gradient(to right, #ff0000, #e50000);
            box-shadow: 0 6px 15px rgba(255, 0, 0, 0.3);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                width: 100%;
                justify-content: center;
            }
            
            .dashboard-content {
                padding: 25px;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-stats {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    
    <nav class="navbar">
        <a href="index.php" class="logo">PlanPro</a>
        <div class="nav-links">
            <a href="organizer_dashboard.php">Dashboard</a>
            <a href="create_event.php">Create Event</a>
        </div>
        <a href="logout.php" class="btn">Logout</a>
    </nav>
    
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Organizer Dashboard</h1>
        </div>
        <div class="dashboard-content">
            <div class="greeting">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
                <p>Manage all aspects of your events from this central dashboard.</p>
            </div>
            
            <div class="quick-stats">
                <div class="stat-card">
                    <h3><?php echo $totalEvents; ?></h3>
                    <p>Active Events</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo array_sum(array_column($events, 'max_attendees')); ?></h3>
                    <p>Max Capacity</p>
                </div>
                <div class="stat-card">
                    <h3>Rs.<?php echo number_format($totalPotentialRevenue, 2); ?></h3>
                    <p>Potential Revenue</p>
                </div>
            </div>
            
            <div class="actions">
                <a href="create_event.php" class="btn">Create New Event</a>
            </div>
            
            <div class="events-container">
                <h2 class="events-heading"><i class="fas fa-calendar-alt"></i> Your Events</h2>
                
                <?php if (empty($events)): ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times" style="font-size: 48px; color: #ccc; display: block; margin-bottom: 20px;"></i>
                    <p>You haven't created any events yet.</p>
                    <a href="create_event.php" class="btn" style="margin-top: 20px;">Create Your First Event</a>
                </div>
                <?php else: ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-image" style="background-image: url('<?php echo htmlspecialchars($event['event_image']); ?>')">
                            <div class="event-category"><?php echo htmlspecialchars($event['event_category']); ?></div>
                            <?php
                            $eventDate = new DateTime($event['event_date']);
                            $currentDate = new DateTime();
                            $interval = $currentDate->diff($eventDate);
                            $daysRemaining = $interval->format('%r%a');
                            
                            if ($daysRemaining > 0) {
                                echo '<div class="event-date-badge">' . $daysRemaining . ' days left</div>';
                            } elseif ($daysRemaining == 0) {
                                echo '<div class="event-date-badge" style="background-color: #ff7700; color: white;">Today!</div>';
                            } else {
                                echo '<div class="event-date-badge" style="background-color: #dddddd;">Past event</div>';
                            }
                            ?>
                        </div>
                        <div class="event-details">
                            <h3 class="event-name"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                            <p class="event-description"><?php echo htmlspecialchars($event['event_description']); ?></p>
                            <div class="event-meta">
                                <i class="far fa-calendar"></i>
                                <?php 
                                $date = new DateTime($event['event_date']);
                                echo $date->format('F j, Y'); 
                                ?>
                            </div>
                            <div class="event-meta">
                                <i class="far fa-clock"></i>
                                <?php echo htmlspecialchars($event['event_time']); ?>
                            </div>
                            <div class="event-meta">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($event['event_location']); ?>
                            </div>
                            <div class="event-meta">
                                <i class="fas fa-users"></i>
                                Capacity: <?php echo htmlspecialchars($event['max_attendees']); ?>
                            </div>
                        </div>
                        <div class="event-footer">
                            <div class="event-price">
                                <?php if ($event['ticket_price'] > 0): ?>
                                    Rs.<?php echo htmlspecialchars($event['ticket_price']); ?>
                                <?php else: ?>
                                    Free
                                <?php endif; ?>
                            </div>
                            <div class="event-actions">
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                <a href="event_details.php?id=<?php echo $event['id']; ?>"><i class="fas fa-eye"></i> View</a>
                                <a href="#" class="delete-btn" onclick="confirmDelete(<?php echo $event['id']; ?>, '<?php echo addslashes(htmlspecialchars($event['event_name'])); ?>')"><i class="fas fa-trash"></i> Delete</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Confirm Delete</h3>
            <p class="modal-text">Are you sure you want to delete "<span id="eventName"></span>"? This action cannot be undone.</p>
            <div class="modal-actions">
                <button class="modal-btn cancel" onclick="closeModal()">Cancel</button>
                <form id="deleteForm" method="post" action="delete_event.php" style="display: inline;">
                    <input type="hidden" name="event_id" id="eventId">
                    <button type="submit" class="modal-btn delete">Delete Event</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Function to show delete confirmation modal
        function confirmDelete(id, name) {
            document.getElementById('eventId').value = id;
            document.getElementById('eventName').textContent = name;
            document.getElementById('deleteModal').style.display = 'flex';
        }
        
        // Function to close modal
        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
<?php
include "db.php";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$organizer_email = $_SESSION['email'];

// Initialize variables to store event data
$event = null;
$errors = [];
$success_message = '';

// Fetch event data
if ($event_id > 0) {
    $sql = "SELECT * FROM events WHERE id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $event_id, $organizer_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Event not found or doesn't belong to this organizer
        header("Location: organizer_dashboard.php");
        exit();
    }
    
    $event = $result->fetch_assoc();
    $stmt->close();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $event_name = trim($_POST['event_name']);
    $event_description = trim($_POST['event_description']);
    $event_category = trim($_POST['event_category']);
    $event_date = trim($_POST['event_date']);
    $event_time = trim($_POST['event_time']);
    $event_location = trim($_POST['event_location']);
    $max_attendees = intval($_POST['max_attendees']);
    $ticket_price = floatval($_POST['ticket_price']);
    
    // Basic validation
    if (empty($event_name)) {
        $errors[] = "Event name is required";
    }
    
    if (empty($event_description)) {
        $errors[] = "Event description is required";
    }
    
    if (empty($event_category)) {
        $errors[] = "Event category is required";
    }
    
    if (empty($event_date)) {
        $errors[] = "Event date is required";
    } elseif (strtotime($event_date) === false) {
        $errors[] = "Invalid date format";
    }
    
    if (empty($event_location)) {
        $errors[] = "Event location is required";
    }
    
    if ($max_attendees <= 0) {
        $errors[] = "Maximum attendees must be a positive number";
    }
    
    if ($ticket_price < 0) {
        $errors[] = "Ticket price cannot be negative";
    }
    
    // Process image upload if a new image is provided
    $event_image = $event['event_image']; // Default to current image
    
    if (!empty($_FILES['event_image']['name'])) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["event_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "event_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is valid
        $check = getimagesize($_FILES["event_image"]["tmp_name"]);
        if ($check === false) {
            $errors[] = "File is not a valid image.";
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES["event_image"]["size"] > 5000000) {
            $errors[] = "File is too large. Maximum size is 5MB.";
        }
        
        // Allow only certain file formats
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
        
        // If no errors, upload the file
        if (empty($errors)) {
            if (move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file)) {
                $event_image = $target_file;
            } else {
                $errors[] = "Error uploading file.";
            }
        }
    }
    
    // If no errors, update event in database
    if (empty($errors)) {
        $sql = "UPDATE events SET 
                event_name = ?, 
                event_description = ?, 
                event_category = ?, 
                event_date = ?, 
                event_time = ?, 
                event_location = ?, 
                max_attendees = ?, 
                ticket_price = ?, 
                event_image = ?
                WHERE id = ? AND email = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssidsis",
            $event_name,
            $event_description,
            $event_category,
            $event_date,
            $event_time,
            $event_location,
            $max_attendees,
            $ticket_price,
            $event_image,
            $event_id,
            $organizer_email
        );
        
        if ($stmt->execute()) {
            $success_message = "Event updated successfully!";
            
            // Refresh event data
            $sql = "SELECT * FROM events WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
        } else {
            $errors[] = "Error updating event: " . $conn->error;
        }
        
        $stmt->close();
    }
}

// Fetch available categories for dropdown
$categories = [
    "Conference",
    "Workshop",
    "Seminar",
    "Concert",
    "Exhibition",
    "Networking",
    "Party",
    "Sports",
    "Other"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanPro - Edit Event</title>
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
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #7c4dff;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #7c4dff;
        }
        
        .btn {
            background-color: #7c4dff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background-color: #6c3aef;
        }
        
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .form-header {
            background-color: #7c4dff;
            padding: 30px;
            color: white;
            text-align: center;
        }
        
        .form-content {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #7c4dff;
            outline: none;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .col {
            flex: 1;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn-cancel {
            background-color: #e0e0e0;
            color: #666;
        }
        
        .btn-cancel:hover {
            background-color: #d0d0d0;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            color: white;
        }
        
        .alert-danger {
            background-color: #ff5252;
        }
        
        .alert-success {
            background-color: #4CAF50;
        }
        
        .image-preview {
            margin-top: 10px;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Custom file input */
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            cursor: pointer;
        }
        
        .file-input-container input[type="file"] {
            position: absolute;
            font-size: 100px;
            opacity: 0;
            right: 0;
            top: 0;
            cursor: pointer;
        }
        
        .file-input-label {
            display: inline-block;
            padding: 8px 20px;
            background-color: #f0f0f0;
            border-radius: 50px;
            transition: background-color 0.3s;
            cursor: pointer;
            font-size: 14px;
        }
        
        .file-input-container:hover .file-input-label {
            background-color: #e0e0e0;
        }
        
        .selected-file {
            margin-top: 8px;
            font-size: 14px;
            color: #666;
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
    
    <div class="form-container">
        <div class="form-header">
            <h1>Edit Event</h1>
        </div>
        <div class="form-content">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul style="list-style-type: none; margin: 0; padding: 0;">
                        <?php foreach($errors as $error): ?>
                            <li><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="edit_event.php?id=<?php echo $event_id; ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="event_name">Event Name</label>
                    <input type="text" id="event_name" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="event_description">Event Description</label>
                    <textarea id="event_description" name="event_description" class="form-control" required><?php echo htmlspecialchars($event['event_description']); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="event_category">Category</label>
                            <select id="event_category" name="event_category" class="form-control" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category; ?>" <?php echo ($event['event_category'] === $category) ? 'selected' : ''; ?>>
                                        <?php echo $category; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="event_date">Date</label>
                            <input type="date" id="event_date" name="event_date" class="form-control" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="event_time">Time</label>
                            <input type="time" id="event_time" name="event_time" class="form-control" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="event_location">Location</label>
                            <input type="text" id="event_location" name="event_location" class="form-control" value="<?php echo htmlspecialchars($event['event_location']); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="max_attendees">Maximum Attendees</label>
                            <input type="number" id="max_attendees" name="max_attendees" class="form-control" min="1" value="<?php echo intval($event['max_attendees']); ?>" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="ticket_price">Ticket Price ($)</label>
                            <input type="number" id="ticket_price" name="ticket_price" class="form-control" min="0" step="0.01" value="<?php echo number_format($event['ticket_price'], 2, '.', ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="event_image">Event Image</label>
                    <div class="file-input-container">
                        <span class="file-input-label"><i class="fas fa-upload"></i> Choose File</span>
                        <input type="file" id="event_image" name="event_image" accept="image/*" onchange="updateFileName(this)">
                    </div>
                    <div class="selected-file" id="file-name">No file chosen</div>
                    
                    <?php if (!empty($event['event_image'])): ?>
                        <div class="image-preview">
                            <p style="margin-top: 10px; margin-bottom: 5px; color: #666;">Current Image:</p>
                            <img src="<?php echo htmlspecialchars($event['event_image']); ?>" alt="Event Image">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <a href="organizer_dashboard.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn">Update Event</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name || "No file chosen";
            document.getElementById("file-name").textContent = fileName;
        }
    </script>
</body>
</html>
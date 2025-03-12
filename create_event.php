<?php
// Include database connection
include 'db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $organizerName = mysqli_real_escape_string($conn, $_POST['organizerName']);
    $companyName = mysqli_real_escape_string($conn, $_POST['companyName']);
    $eventName = mysqli_real_escape_string($conn, $_POST['eventName']);
    $eventDescription = mysqli_real_escape_string($conn, $_POST['eventDescription']);
    $eventDate = mysqli_real_escape_string($conn, $_POST['eventDate']);
    $eventTime = mysqli_real_escape_string($conn, $_POST['eventTime']);
    $eventLocation = mysqli_real_escape_string($conn, $_POST['eventLocation']);
    $eventCategory = mysqli_real_escape_string($conn, $_POST['eventCategory']);
    $maxAttendees = mysqli_real_escape_string($conn, $_POST['maxAttendees']);
    $ticketPrice = mysqli_real_escape_string($conn, $_POST['ticketPrice']);
    
    // Upload image if provided
    $eventImage = '';
    if(isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] == 0) {
        $targetDir = "uploads/events/";
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = basename($_FILES["eventImage"]["name"]);
        $targetFilePath = $targetDir . time() . '_' . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if(in_array($fileType, $allowTypes)){
            // Upload file to server
            if(move_uploaded_file($_FILES["eventImage"]["tmp_name"], $targetFilePath)){
                $eventImage = $targetFilePath;
            }
        }
    }
    
    // Insert event into database
    // Note: You'll need to update your database schema to include organizer_name and company_name fields
    $sql = "INSERT INTO events (email, organizer_name, company_name, event_name, event_description, event_date, event_time, event_location, 
            event_category, max_attendees, ticket_price, event_image, created_at) 
            VALUES ('$email', '$organizerName', '$companyName', '$eventName', '$eventDescription', '$eventDate', '$eventTime', '$eventLocation', 
            '$eventCategory', '$maxAttendees', '$ticketPrice', '$eventImage', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        $successMessage = "Event created successfully!";
    } else {
        $errorMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanPro - Create Event</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --accent-color: #f6c23e;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Nunito', sans-serif;
        }
        
        .navbar {
            background-color: var(--primary-color);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .dashboard-container {
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-title {
            margin-bottom: 0;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 12px;
            border: 1px solid #d1d3e2;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 20px;
            font-weight: 600;
            border-radius: 5px;
        }
        
        .btn-primary:hover {
            background-color: #3a5ccf;
            border-color: #3a5ccf;
        }
        
        .alert {
            border-radius: 5px;
            padding: 15px;
        }
        
        label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }
        
        .required-field:after {
            content: " *";
            color: red;
        }
        
        .form-text {
            color: #6c757d;
            font-size: 14px;
        }

        .section-divider {
            width: 100%;
            height: 1px;
            background-color: #e3e6f0;
            margin: 20px 0;
        }

        .section-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-calendar-check"></i> PlanPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="organizer_dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="create_event.php">
                            <i class="fas fa-plus-circle"></i> Create Event
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container dashboard-container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-plus-circle"></i> Create New Event
                </h1>
                <p class="text-muted">Fill out the form below to create a new event</p>
            </div>
        </div>
        
        <?php if(isset($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $successMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $errorMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Event Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Organizer Information Section -->
                        <div class="col-12">
                            <h5 class="section-title"><i class="fas fa-user-tie me-2"></i>Organizer Information</h5>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="email" class="required-field">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="form-text">This will be used for communication and notifications</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="organizerName" class="required-field">Organizer Name</label>
                            <input type="text" class="form-control" id="organizerName" name="organizerName" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="companyName">Company/Organization Name</label>
                            <input type="text" class="form-control" id="companyName" name="companyName">
                            <div class="form-text">Leave blank if organizing as an individual</div>
                        </div>
                        
                        <div class="col-12">
                            <div class="section-divider"></div>
                        </div>
                        
                        <!-- Event Basic Information -->
                        <div class="col-12">
                            <h5 class="section-title"><i class="fas fa-info-circle me-2"></i>Event Details</h5>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="eventName" class="required-field">Event Name</label>
                            <input type="text" class="form-control" id="eventName" name="eventName" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="eventCategory" class="required-field">Event Category</label>
                            <select class="form-select" id="eventCategory" name="eventCategory" required>
                                <option value="" selected disabled>Select Category</option>
                                <option value="Conference">Conference</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Concert">Concert</option>
                                <option value="Exhibition">Exhibition</option>
                                <option value="Sports">Sports</option>
                                <option value="Networking">Networking</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="eventDescription" class="required-field">Event Description</label>
                            <textarea class="form-control" id="eventDescription" name="eventDescription" rows="4" required></textarea>
                            <div class="form-text">Provide a detailed description of your event. Include what attendees can expect.</div>
                        </div>
                        
                        <!-- Event Date and Time -->
                        <div class="col-md-6 mb-3">
                            <label for="eventDate" class="required-field">Event Date</label>
                            <input type="date" class="form-control" id="eventDate" name="eventDate" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="eventTime" class="required-field">Event Time</label>
                            <input type="time" class="form-control" id="eventTime" name="eventTime" required>
                        </div>
                        
                        <!-- Event Location -->
                        <div class="col-12 mb-3">
                            <label for="eventLocation" class="required-field">Event Location</label>
                            <input type="text" class="form-control" id="eventLocation" name="eventLocation" placeholder="Venue name, address, or online platform" required>
                        </div>
                        
                        <!-- Event Capacity and Price -->
                        <div class="col-md-6 mb-3">
                            <label for="maxAttendees" class="required-field">Maximum Attendees</label>
                            <input type="number" class="form-control" id="maxAttendees" name="maxAttendees" min="1" required>
                            <div class="form-text">Set to 0 for unlimited attendees</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ticketPrice" class="required-field">Ticket Price ($)</label>
                            <input type="number" class="form-control" id="ticketPrice" name="ticketPrice" min="0" step="0.01" required>
                            <div class="form-text">Set to 0 for free events</div>
                        </div>
                        
                        <!-- Event Image -->
                        <div class="col-12 mb-4">
                            <label for="eventImage">Event Image</label>
                            <input type="file" class="form-control" id="eventImage" name="eventImage" accept="image/*" onchange="previewImage(this);">
                            <div class="form-text">Recommended size: 1200 x 600 pixels (16:9 ratio). Max file size: 2MB</div>
                            <div class="mt-2">
                                <img id="imagePreview" class="preview-image d-none" alt="Image Preview">
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-plus me-2"></i> Create Event
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.classList.add('d-none');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.setAttribute('src', e.target.result);
                    preview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
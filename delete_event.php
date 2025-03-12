<?php
include "db.php";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Initialize response variables
$success = false;
$message = "";

// Process delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $organizer_email = $_SESSION['email'];
    
    // First, check if the event exists and belongs to this organizer
    $sql = "SELECT id, event_name, event_image FROM events WHERE id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $event_id, $organizer_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Event not found or doesn't belong to this organizer
        $message = "Event not found or you don't have permission to delete it.";
    } else {
        $event = $result->fetch_assoc();
        
        try {
            // Delete the event
            $sql = "DELETE FROM events WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success = true;
                $message = "Event '" . htmlspecialchars($event['event_name']) . "' has been successfully deleted.";
                
                // Delete the event image file if it exists and is not a default image
                if (!empty($event['event_image']) && file_exists($event['event_image']) && strpos($event['event_image'], 'default') === false) {
                    unlink($event['event_image']);
                }
            } else {
                $message = "Error deleting event. Please try again.";
            }
        } catch (Exception $e) {
            $message = "Error deleting event: " . $e->getMessage();
        }
    }
    
    $stmt->close();
} else {
    // Invalid request
    $message = "Invalid request.";
}

$conn->close();

// Redirect back to the dashboard with appropriate message
if ($success) {
    $_SESSION['success_message'] = $message;
} else {
    $_SESSION['error_message'] = $message;
}

header("Location: organizer_dashboard.php");
exit();
?>
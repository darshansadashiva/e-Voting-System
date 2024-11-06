<?php
include 'includes/session.php';

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id); // Assuming 'id' is an integer

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Voter deleted successfully';
    } else {
        $_SESSION['error'] = $stmt->error; // Capture any error
    }

    $stmt->close(); // Close the statement
} else {
    $_SESSION['error'] = 'Select item to delete first'; // Error if no ID is provided
}

// Redirect back to the voters page
header('location: voters.php');
?>
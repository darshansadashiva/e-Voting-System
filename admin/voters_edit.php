<?php
include 'includes/session.php';

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $mobile_number = isset($_POST['mobile_number']) ? $_POST['mobile_number'] : ''; // Default to empty string if not set
    $password = $_POST['password'];

    // Retrieve the existing password
    $stmt = $conn->prepare("SELECT password FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($existingPassword);
    $stmt->fetch();
    $stmt->close();

    // Check if a new password was provided or keep the existing password
    if (empty($password)) {
        $password = $existingPassword; // Keep the existing password if no new password is provided
    }

    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE voters SET firstname = ?, lastname = ?, password = ?, mobile_number = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $firstname, $lastname, $password, $mobile_number, $id); // Bind parameters

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Voter updated successfully';
    } else {
        $_SESSION['error'] = $stmt->error; // Capture any error
    }

    $stmt->close(); // Close the statement
} else {
    $_SESSION['error'] = 'Fill up edit form first'; // Error if form is not filled
}

// Redirect back to the voters page
header('location: voters.php');
?>

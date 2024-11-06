<?php
include 'includes/session.php';

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password']; // Save as plain text

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($existingPassword);
    $stmt->fetch();
    $stmt->close();

    // Check if the password is correct
    if ($password === $existingPassword) {
        // If the password matches, keep the existing password (plain text)
        $password = $existingPassword;
    } else {
        // If the password does not match, save the new password as plain text
        // Note: You may want to add additional logic here to handle password changes
    }

    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE voters SET firstname = ?, lastname = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $firstname, $lastname, $password, $id); // Bind parameters

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
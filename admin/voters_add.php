<?php
include 'includes/session.php';

if (isset($_POST['add'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password']; // Save as plain text
    $mobile_number = $_POST['mobile_number']; 
    $filename = $_FILES['photo']['name'];

    // Handle photo upload if a file is provided
    if (!empty($filename)) {
        move_uploaded_file($_FILES['photo']['tmp_name'], '../images/' . $filename);
    }

    // Generate voter ID
    $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $voter = substr(str_shuffle($set), 0, 15);

    // Prepare SQL statement to insert voter data
    $sql = "INSERT INTO voters (voters_id, password, firstname, lastname, photo, mobile_number) VALUES ('$voter', '$password', '$firstname', '$lastname', '$filename', '$mobile_number')";
    
    // Execute the query and check for success
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Voter added successfully';
    } else {
        $_SESSION['error'] = $conn->error; // Capture any error
    }
} else {
    $_SESSION['error'] = 'Fill up add form first'; // Error if form is not filled
}

// Redirect back to the voters page
header('location: voters.php');
?>
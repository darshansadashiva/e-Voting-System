<?php
include 'includes/session.php';

if (isset($_POST['voters_id'])) {
    $voters_id = $_POST['voters_id'];

    // Retrieve voter details
    $sql = "SELECT mobile_number FROM voters WHERE id = '$voters_id'";
    $query = $conn->query($sql);

    if ($query->num_rows > 0) {
        $voter = $query->fetch_assoc();
        $mobile_number = $voter['mobile_number'];

        // 2Factor API configuration
        $api_key = "a3216e2f-afbf-11ef-8b17-0200cd936042"; // Replace with your actual API key
        $url = "https://2factor.in/API/V1/$api_key/SMS/$mobile_number/AUTOGEN";

        $response = file_get_contents($url);
        $result = json_decode($response, true);

        if ($result['Status'] == 'Success') {
            $_SESSION['otp_session_id'] = $result['Details'];
            echo json_encode(['success' => true, 'message' => 'OTP sent successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Voter not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>

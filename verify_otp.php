<?php
include 'includes/session.php';

if (isset($_POST['otp']) && isset($_SESSION['otp_session_id'])) {
    $otp = $_POST['otp'];
    $otp_session_id = $_SESSION['otp_session_id'];

    // 2Factor API configuration
    $api_key = "a3216e2f-afbf-11ef-8b17-0200cd936042"; // Replace with your actual API key
    $url = "https://2factor.in/API/V1/$api_key/SMS/VERIFY/$otp_session_id/$otp";

    $response = file_get_contents($url);
    $result = json_decode($response, true);

    if ($result['Status'] == 'Success') {
        $_SESSION['otp_verified'] = true;
        echo json_encode(['success' => true, 'message' => 'OTP verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>

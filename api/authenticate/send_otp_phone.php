<?php

include("header.php");

// Define the regular expression pattern for Nigerian phone numbers
$phonePattern = "/^0[7-9][0-9]{9}$/";

// Get posted data
$data = file_get_contents("php://input");
$data = json_decode($data, true);

if (!empty($data)) {
    $phone_number = $data['phone'];

    // Remove any non-digit characters from the phone number
    $cleaned_phone_number = preg_replace("/[^0-9]/", "", $phone_number);

    if (preg_match($phonePattern, $cleaned_phone_number)) {
        $otpResponse = GenerateOtpPhone($phone_number);

        if ($otpResponse) {
            // Simulate sending OTP via SMS
            // Replace the following line with your SMS sending logic
            $smsSent = sendOtpViaSms($otpResponse);

            if ($smsSent) {
                http_response_code(200);
                echo json_encode(array("message" => "OTP sent", "status" => "200"));
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Failed to send OTP via SMS", "status" => 400));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Failed to generate OTP", "status" => 400));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid Nigerian phone number", "status" => 400));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Fill in appropriate data.", "status" => 400));
}


function sendOtpViaSms($otp) {
    // Implement your SMS sending logic here
    // Return true if the SMS was sent successfully, otherwise return 400
    // You can use third-party services or APIs to send SMS
    // Example: return sendSmsUsingSomeService($phone_number, $otp);
    return true; // Simulated success
}
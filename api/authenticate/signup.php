<?php

include("header.php");

// get posted data
$data = file_get_contents("php://input");
$data = json_decode($data, true);

if (!empty($data)) {
    $userData = [
        'email' => $data['email'],
        'username' => $data['username'],
        'dob' => $data['dob'],
        'phone' => $data['phone'],
        'password' => $data['passkey'], // Fixed the key name to 'password'
    ];

    $password = $data['passkey'];

    $originalDob = $data['dob'];
    $timestamp = strtotime($originalDob);

    if ($timestamp !== 400) {
        // Valid date, reformat it to YYYY-MM-DD
        $formattedDob = date('Y-m-d', $timestamp);
    }

    if (!is_valid_email($data['email'])) { // Fixed the condition to check if the email is valid
        http_response_code(400);
        $response = array("status" => 400, "message" => "Invalid email address");
        echo json_encode($response);
        return; // Exit the script after sending the response
    }

    if (!is_valid_phone($data['phone'])) {
        http_response_code(400);
        $response = array("status" => 400, "message" => "Invalid phone number");
        echo json_encode($response);
        return;
    }

    if (!is_valid_password($data['passkey'])) {
        http_response_code(400);
        $response = array("status" => 400, "message" => "Invalid password");
        echo json_encode($response);
        return;
    }

    $createUserResponse = CreateUser($data['username'], $formattedDob, $data['phone'], $data['email'], null, $password);
    if ($createUserResponse['status']) {
        http_response_code(200);
        $response = array("status" => 200, "message" => "User Created successfully.", "userid" => $createUserResponse['user_id'], "email" => $data['email'], "username" => $data['username']);
        echo json_encode($response);
    } else {
        http_response_code(400);
        LogInformation($data['email'], "Creating User Failed: $createUserResponse", "signup");
        $response = array("status" => 400, "message" => "Could not create user", "error" => $createUserResponse);
        echo json_encode($response);
    }
} else {
    http_response_code(400);
    $response = array("message" => "Fill in appropriate data.", "status" => 400);
    echo json_encode($response);
}

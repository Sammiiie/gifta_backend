<?php

include("header.php");


// get posted data
// $data = json_decode(file_POST_contents("php://input"), true);
$data = file_get_contents("php://input");
// echo $data;
$data = json_decode($data, true);

if (!empty($data)) {

    $userData = [
        'email' => $data['email'],
    ];
    // $password = base64_decode($data['passkey']);
    $password = $data['passkey'];

    $createUserResponse = CreateUser($data['username'], $data['dob'], $data['phone'], $data['email'], null, $password);
    if ($createUserResponse['user_id']) {
        // set response code - 201 created
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "User Created sussuesfully.", "userid" => $loginResponse['email'], $loginResponse['username']));
    } else {
        // set response code - 400 service unavailable
        http_response_code(400);

        // tell the user
        LogInformation($data['email'], "Creating User Failed: $createUserResponse", "signup");
        echo json_encode(array("message" => "Could not create user", "error" => $createUserResponse));
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data."));
}

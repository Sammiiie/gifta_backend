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

    $loginResponse = AuthenticateUser($data['email'], $password);
    if ($loginResponse['email'] == $data['email']) {
        // set response code - 201 created
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "User login sussuesful.", "userid" => $loginResponse['email'], $loginResponse['first_name']));
    } else {
        // set response code - 400 service unavailable
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Wrong Password or email", "error" => $loginResponse));
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data."));
}

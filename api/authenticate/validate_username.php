<?php

include("header.php");


// get posted data
// $data = json_decode(file_POST_contents("php://input"), true);
$data = file_get_contents("php://input");
// echo $data;
$data = json_decode($data, true);

if (!empty($data)) {

    if (!ValidateUserName($data['username'])) {
        // set response code - 201 created
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "Username Okay"));
    } else {
        // set response code - 400 service unavailable
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Username found"));
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data."));
}

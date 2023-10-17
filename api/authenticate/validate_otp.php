<?php

include("header.php");

// get posted data
$data = file_get_contents("php://input");
$data = json_decode($data, true);

if (!empty($data)) {

    $result = ValidateOtp($data['contact'], $data['otp']);
    echo json_encode($result);
}else{
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data.", "status" => 400));
}

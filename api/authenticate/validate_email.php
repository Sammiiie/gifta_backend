<?php

include("header.php");


// get posted data
// $data = json_decode(file_POST_contents("php://input"), true);
$data = file_get_contents("php://input");
// echo $data;
$data = json_decode($data, true);

if (!empty($data)) {
    if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
        if (!ValidateEmail($data['email'])) {
            // set response code - 201 created
            http_response_code(200);
    
            // tell the user
            echo json_encode(array("message" => "Email Okay", "status" => true));
        } else {
            // set response code - 400 service unavailable
            http_response_code(400);
    
            // tell the user
            echo json_encode(array("message" => "Email found", "status" => false));
        }
    }else{
        http_response_code(400);
    
            // tell the user
            echo json_encode(array("message" => "Not Valid Email address", "status" => false));
    }

    
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data.", "status" => false));
}

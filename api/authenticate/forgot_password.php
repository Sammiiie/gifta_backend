<?php

include("header.php");

// get posted data
// $data = json_decode(file_POST_contents("php://input"), true);
$data = file_get_contents("php://input");
// echo $data;
$data = json_decode($data, true);

if (!empty($data)) {


    if (!empty($data['email'])) {

        // $findUser = selectOne('users', ['email' => $data['email']]);
        $email = $data['email'];
        
        $token =  $data['token'];
        // $findtoken = selectOne('forgotten_password', ['users_id' => $findUser['id'], 'token' => $data['token'], 'status' => "Pending"]);
        $sql = "SELECT forgotten_password.* FROM forgotten_password JOIN users ON users.email = '$email' AND forgotten_password.token = $token AND `status` = 'Pending' AND users.id = forgotten_password.users_id";
        error_log("SQL: $sql");
        $findtoken = customQuery2($sql);
        $user_id = $findtoken['users_id'];
        error_log("Token: $token");

        error_log("Token data: $findtoken");
        if($findtoken){
            if (!isOver30MinutesAgo($findtoken['request_date'])) {
                $passkey = password_hash($data['new_password'], PASSWORD_DEFAULT);
                $userData = [
                    'passkey' => $passkey,
                ];
                
    
                $updateUser = update('users', $user_id, "id", $userData);
                update('forgotten_password', $findtoken['id'], "id", ["status" => "Changed"]);
                if ($updateUser) {
                    // send mail to user
    
                    // set response code - 201 created
                    http_response_code(201);
    
                    // tell the user
                    echo json_encode(array("message" => "Password Updated succesfully.", "Password update Status" => "$updateUser"));
                } else {
                    // set response code - 503 service unavailable
                    $error = mysqli_error($connection);
                    http_response_code(400);
    
                    // tell the user
                    if ($error != "")
                        echo json_encode(array("message" => "Could not edit user details", "error" => $error));
                    else
                        echo json_encode(array("message" => "Could not edit user details", "error" => "Can't use old password"));
                }
            } else {
                // set response code - 503 service unavailable
                http_response_code(400);
    
                // tell the user
                echo json_encode(array("message" => "Token has expired"));
            }
        }else {
            // set response code - 503 service unavailable
            http_response_code(400);

            // tell the user
            echo json_encode(array("message" => "Token not valid"));
        }
    } else {
        // set response code - 503 service unavailable
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Email cannot be empty"));
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data."));
}

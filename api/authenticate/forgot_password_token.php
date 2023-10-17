<?php

include("header.php");

// get posted data
// $data = json_decode(file_POST_contents("php://input"), true);
$data = file_get_contents("php://input");
// echo $data;
$data = json_decode($data, true);

if (!empty($data)) {


    // $password = generateRandomString(10);
    // $password = "Password25@";
    // $passkey = password_hash($password, PASSWORD_DEFAULT);
    if (!empty($data['email'])) {

        $findUser = selectOne('users', ['email' => $data['email']]);
        if ($findUser) {

            $token = generateRandomNumber(6);

            $user_id = $findUser['id'];
            $userData = [
                'token' => $token,
                'status' => "Pending",
                'users_id' => $user_id
            ];


            $updateUser = insert('forgotten_password', $userData);
            if ($updateUser) {
                $subject = "Password Reset";
                $username = $findUser['username'];
                $email = $data['email'];
                $message = "
                    <html> 
                        <body> 
                            <p style=\"text-align:center;height:120px;background-color:#abc;border:1px solid #456;border-radius:3px;padding:10px;\">
                                Hi there $username, your password reset token is 
                                <br/><br/><br/><b>$token</b>.
                                <br/><br/>Token is only valid for 30 minutes. If you did not request this kindly contact support.
                            </p>
                            
                        </body>
                    </html>";
                sendmail($email, $subject, $message);
                // send mail to user

                // set response code - 201 created
                http_response_code(201);

                // tell the user
                echo json_encode(array("message" => "Reset Token sent succesfully.", "user" => "$email", "status" => 200));
            } else {
                // set response code - 503 service unavailable
                $error = mysqli_error($connection);
                http_response_code(400);

                // tell the user
                if ($error != "")
                    echo json_encode(array("message" => "Could not send token", "error" => $error, "status" => 400));
                else
                    echo json_encode(array("message" => "Could not send token", "error" => "Something went wrong", "status" => 400));
            }
        } else {
            // set response code - 503 service unavailable
            http_response_code(400);

            // tell the user
            echo json_encode(array("message" => "No user exists with this email", "status" => 400));
        }
    } else {
        // set response code - 503 service unavailable
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Email cannot be empty", "status" => 400));
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data.", "status" => 400));
}

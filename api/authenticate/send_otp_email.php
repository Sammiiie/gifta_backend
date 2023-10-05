<?php

include("header.php");


// get posted data
// $data = json_decode(file_POST_contents("php://input"), true);
$data = file_get_contents("php://input");
// echo $data;
$data = json_decode($data, true);

if (!empty($data)) {


    $otpResponse = GenerateOtpEmail($data['email']);
    if ($otpResponse) {
        // send mail to user
        $subject = "Welcome | Gifta";
        $email = $data['email'];
        $message = "
    <html> 
        <body> 
            <p style=\"text-align:center;height:120px;background-color:#abc;border:1px solid #456;border-radius:3px;padding:10px;\">
                Hi dear, your otp is: $otpResponse do not share with anoyone.
            </p>
            
        </body>
    </html>";
        sendmail($email, $subject, $message);
        // set response code - 201 created
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "OTP sent", "status" => "Success"));
    } else {
        // set response code - 400 service unavailable
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Failed to send Otp", "status" => false));
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Fill in appropraite data."));
}

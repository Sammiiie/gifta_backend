<?php

include("header.php");


if (!empty($_FILES["file"]["name"])) {

    $name = $_FILES["file"]["name"];
    list($txt, $ext) = explode(".", $name);
    $file_name =  time() . "-" . $name;
    $tmp = $_FILES["file"]["tmp_name"];
    $uploaded_at = date("Y-m-d H:i:s");
    if (move_uploaded_file($tmp, "uploads/" . $file_name)) {
        $path = "uploads/" . $file_name;
        $fp = fopen($path, "rb");
        $size = filesize($path);
    }


    // $uri = $_SERVER['REQUEST_URI'];
    // // echo $uri; // Outputs: URI

    // // echo "<br>";
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    // $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // echo $url; // Outputs: Full URL
    $url = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) ."/" . $path;
    
    if ($url) {
        // set response code - 200 service unavailable
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "success", "file_url" => $url, "file_extention" => $ext, "file_size" => $size));
    } else {
        $error = curl_error($curl);
        // set response code - 503 service unavailable
        http_response_code(400);

        // tell the user
        echo json_encode(array("message" => "Could not store file to cloud", "error" => $error));
    }
    // unlink($path);
} else {
    // set response code - 503 service unavailable
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Please upload file"));
}
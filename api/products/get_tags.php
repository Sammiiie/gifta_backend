<?php

include("header.php");


$findTags = selectAll('tags',);

if ($findTags) {
    http_response_code(200);
    $response = array("status" => 200, "payload" => $findTags);
    echo json_encode($response);
} else {
    http_response_code(200);
    $error = mysqli_error($connection);
    $response = array("status" => 200, "message" => "Could not find tags", "error" => $error);
    echo json_encode($response);
}
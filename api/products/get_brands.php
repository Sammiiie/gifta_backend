<?php

include 'header.php';

$findBrands = selectAll('brand');

if ($findBrands) {
    http_response_code(200);
    $response = ['status' => 200, 'payload' => $findBrands];
    echo json_encode($response);
} else {
    http_response_code(200);
    $error = mysqli_error($connection);
    $response = [
        'status' => 200,
        'message' => 'Could not find Brands',
        'error' => $error,
    ];
    echo json_encode($response);
}

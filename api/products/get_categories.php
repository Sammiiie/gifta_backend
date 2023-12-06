<?php

include 'header.php';

$findCategories = selectAll('categories', ['status' => 'APPROVED']);

if ($findCategories) {
    http_response_code(200);
    $response = ['status' => 200, 'payload' => $findCategories];
    echo json_encode($response);
} else {
    http_response_code(200);
    $error = mysqli_error($connection);
    $response = [
        'status' => 200,
        'message' => 'Could not find categories',
        'error' => $error,
    ];
    echo json_encode($response);
}

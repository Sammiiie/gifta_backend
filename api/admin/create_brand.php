<?php

include 'header.php';

// get posted data
$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (!empty($data['brand_name'])) {
    $catdata = [
        'brand_name' => test_input($data['name']),
        'contact_email' => test_input($data['contact_email']),
        'contact_phone' => test_input($data['contact_phone']),
        'brand_logo' => test_input($data['brand_logo']),
    ];
    $result = insert('brand', $catdata);
    if ($result) {
        http_response_code(200);
        $response = [
            'status' => 200,
            'message' => 'Brand Successfully created',
            'id' => $result,
        ];
        echo json_encode($response);
    } else {
        http_response_code(200);
        $error = mysqli_error($connection);
        $response = [
            'status' => 200,
            'message' => 'Could not create Brand',
            'error' => $error,
        ];
        echo json_encode($response);
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode([
        'message' => 'Name cannot be empty.',
        'status' => 400,
    ]);
}

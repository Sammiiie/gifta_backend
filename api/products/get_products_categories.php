<?php

include 'header.php';

// get posted data
$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (!empty($data['categories'])) {
    $result = selectAll('products', ['category' => $data['categories']]);
    if ($result) {
        http_response_code(200);
        $response = ['status' => 200, 'payload' => $result];
        echo json_encode($response);
    } else {
        http_response_code(200);
        $error = mysqli_error($connection);
        $response = [
            'status' => 200,
            'message' => 'Could not find Products',
            'error' => $error,
        ];
        echo json_encode($response);
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode([
        'message' => 'Fill in appropraite data.',
        'status' => 400,
    ]);
}

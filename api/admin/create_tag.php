<?php

include 'header.php';

// get posted data
$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (!empty($data['title'])) {
    $catdata = [
        'title' => test_input($data['title']),
        'tag_category' => test_input($data['tag_category']),
    ];
    $result = insert('tags', $catdata);
    if ($result) {
        http_response_code(200);
        $response = [
            'status' => 200,
            'message' => 'Tags Successfully created',
            'id' => $result,
        ];
        echo json_encode($response);
    } else {
        http_response_code(200);
        $error = mysqli_error($connection);
        $response = [
            'status' => 200,
            'message' => 'Could not create Tags',
            'error' => $error,
        ];
        echo json_encode($response);
    }
} else {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode([
        'message' => 'Title cannot be empty.',
        'status' => 400,
    ]);
}

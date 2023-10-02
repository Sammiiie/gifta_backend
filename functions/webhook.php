<?php
// webhook.php

// Secret key for verifying webhook requests (set this to a secret value)
$secret = 'Z9xP8A3rGtQ7w2YK';

// Get the webhook payload
$payload = file_get_contents("php://input");

// Validate the GitHub webhook signature
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
if (hash_equals('sha1=' . hash_hmac('sha1', $payload, $secret), $signature)) {
    // Signature is valid, proceed with processing the payload

    // Decode the JSON payload
    $data = json_decode($payload, true);

    // Check if the event is a push to the main branch
    if ($data['ref'] === 'refs/heads/main') {
        // Git pull command
        $gitPullCommand = "git pull origin main";

        // Deployment script command
        $deployScriptCommand = "./deploy.sh";

        // Execute Git pull and deployment script
        exec("$gitPullCommand && $deployScriptCommand", $output, $return_var);

        if ($return_var === 0) {
            // Git pull and deployment script executed successfully
            // $output contains the command's outputs
            http_response_code(200);
            echo "Git pull and deployment completed successfully.\n";
        } else {
            // Git pull or deployment script encountered an error
            // $output may contain error messages
            http_response_code(400);
            echo "Error executing Git pull and deployment.\n";
            print_r($output);
        }
    } else {
        http_response_code(400); // Bad request for non-main branch pushes
    }
} else {
    http_response_code(403); // Forbidden for invalid signature
}

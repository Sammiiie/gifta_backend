<?php

include('connect.php');

$today = date('Y-m-d H:i:s');

# User management
function CreateUser($username, $dob, $phone, $email, $address, $password)
{
    global $connection;
    $passkey = password_hash($password, PASSWORD_DEFAULT);
    $userData = [
        'username' => $username,
        'dob' => $dob,
        'phone' => $phone,
        'email' => $email,
        'user_type' => "CUSTOMER",
        'home_address' => $address,
        'passkey' => $passkey
    ];

    $createUser = insert('users', $userData);
    if ($createUser) {
        $createUserResponse = [
            'user_id' => $createUser,
            'username' => $username,
            'email' => $email,
        ];
        return $createUserResponse;
    } else {
        return mysqli_error($connection);
    }
}

function SignUpSocial($username, $dob, $phone, $email, $channel)
{
    global $connection;

    $findUser = selectOne('users_social', ['email' => $email]);
    if(!$findUser){
        $userData = [
            'username' => $username,
            'dob' => $dob,
            'phone' => $phone,
            'email' => $email,
            'user_type' => "CUSTOMER",
        ];
    
        $createUser = insert('users_social', $userData);
        if ($createUser) {
            insert('users', $userData);
            $createUserResponse = [
                'user_id' => $createUser,
                'username' => $username,
                'email' => $email,
            ];
            return $createUserResponse;
        } else {
            return mysqli_error($connection);
        }
    }
    $UserResponse = [
        'user_id' => $findUser['id'],
        'username' => $findUser['username'],
        'email' => $email['email'],
    ];
    return $UserResponse;
    
}

function EditUser($username, $phone, $email, $dob, $address)
{
    global $connection;
    $userData = [
        'username' => $username,
        'dob' => $dob,
        'phone' => $phone,
        'email' => $email,
        'home_address' => $address,
    ];

    $editUser = update('users', $email, 'email', $userData);
    if ($editUser) {
        return $editUser;
    } else {
        return mysqli_error($connection);
    }
}

function ValidateUserName($username)
{
    global $connection;
    $username = test_input($username);
    $findUser = customQuery2("SELECT username, email From users WHERE username = '$username'");
    if ($findUser) {
        return true;
    }
    return false;
}

function ValidateEmail($email)
{
    global $connection;
    $email = test_input($email);
    $findUser = customQuery2("SELECT username, email From users WHERE email = '$email'");
    if ($findUser) {
        return true;
    }
    return false;
}

function AuthenticateUser($email, $password)
{
    global $today;
    $findUser = customQuery2("Select id, email, passkey, username, firstname FROM users WHERE email = '$email' AND user_type = 'CUSTOMER'");
    if (password_verify($password, $findUser['passkey'])) {
        update('users', $email, 'email', ['last_login' => $today]);
        return $findUser;
    }
    return "Invalid Login";
}

function LogInformation($email, $activity, $method_name)
{
    global $connection;

    $activityData = [
        'email' => $email,
        'activity' => $activity,
        'method_name' => $method_name
    ];

    $recordActitvity =  insert('actitvity_logs', $activityData);
    if ($recordActitvity) {
        return $recordActitvity;
    }
    return mysqli_error($connection);
}

function GenerateOtpEmail($email){
    global $connection;

    $otp = generateRandomNumber(6);

    $recordOtp = insert('otp', ['email' => $email, 'otp' => $otp]);
    if ($recordOtp) {
        return $recordOtp;
    }
    return mysqli_error($connection);
}


function GenerateOtpPhone($phone){
    global $connection;

    $otp = generateRandomNumber(6);

    $recordOtp = insert('otp', ['phone' => $phone, 'otp' => $otp]);
    if ($recordOtp) {
        return $otp;
    }
    return mysqli_error($connection);
}
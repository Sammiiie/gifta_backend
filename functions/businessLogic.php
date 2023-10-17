<?php

include('connect.php');

$today = date('Y-m-d H:i:s');

// Regular expression to validate an email address
$pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

$PhonePattern = "/^0[7-9][0-9]{9}$/";

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

    try {
        $createUser = insert('users', $userData);
        if ($createUser) {
            $createUserResponse = [
                'status' => true,
                'user_id' => $createUser,
                'username' => $username,
                'email' => $email,
            ];
            return $createUserResponse;
        } else {
            // Handle other errors here if needed
            return "User creation failed.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            // Duplicate entry error (username or email already exists)
            return array("status"=> false, "message" => "Duplicate entry error: " . $e->getMessage());
        } else {
            // Handle other database errors here if needed
            return array("status"=> false, "message" =>"Database error: " . $e->getMessage());
        }
    }
}

function SignUpSocial($username, $dob, $phone, $email, $channel)
{
    global $connection;

    $findUser = selectOne('users_social', ['email' => $email]);
    if (!$findUser) {
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

    $recordActitvity =  insert('activity_logs', $activityData);
    if ($recordActitvity) {
        return $recordActitvity;
    }
    return mysqli_error($connection);
}

function GenerateOtpEmail($email)
{
    global $connection;

    $otp = generateRandomNumber(6);
    $otpData = [
        'email' => $email,
        'otp' => $otp
    ];

    $recordOtp = insert('otp', $otpData);
    if ($recordOtp) {
        return $otp;
    }
    return mysqli_error($connection);
}


function GenerateOtpPhone($phone)
{
    global $connection;

    $otp = generateRandomNumber(6);

    $recordOtp = insert('otp', ['phone' => $phone, 'otp' => $otp]);
    if ($recordOtp) {
        return $otp;
    }
    return mysqli_error($connection);
}

function ValidateOtp($contactused, $otp)
{
    global $connection;
    global $today;

    // Prepare a SQL query to fetch the OTP record
    $sql = "SELECT id, otp, status, date_created FROM otp WHERE (email = ? OR phone = ?) AND otp = ? LIMIT 1";
    $data =  [$contactused, $contactused, $otp];

    $result = executeDynamicQuery($sql, $data);

    if ($result) {

        $id = $result['id'];
        $storedOtp = $result['otp'];
        $status = $result['status'];
        $dateCreated = new DateTime($result['date_created']);

        // Check if the provided OTP is numeric and has 6 digits
        if (is_numeric($otp) && strlen($otp) === 6) {
            // Check if status is pending and datetime is not greater than 10 minutes compared to date_created
            if ($status === 'PENDING') {
                $now = new DateTime();
                error_log("Datetime: $today");
                $interval = $now->diff($dateCreated);
                $minutesDiff = $interval->i;

                if ($minutesDiff <= 10) {
                    update('otp', $id, 'id', ['status' => "USED"]);
                    return array('status' => 200, 'message' => 'OTP is valid');
                } else {
                    return array('status' => 400, 'message' => 'OTP has expired');
                }
            } else {
                return array('status' => 400, 'message' => 'OTP status is not pending');
            }
        } else {
            return array('status' => 400, 'message' => 'Invalid OTP format');
        }
    } else {
        return array('status' => 400, 'message' => 'No matching record found');
    }
}


function CreateCategories($name, $description)
{
    global $connection;

    $createCategories =  insert('categories', [$name, $description]);
    if ($createCategories) {
        return $createCategories;
    }
    return mysqli_error($connection);
}

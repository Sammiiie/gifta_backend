<?php

include('connect.php');

$today = date('Y-m-d H:i:s');

# User management
function CreateUser($fullname, $phone, $email, $designation, $address, $password)
{
    global $connection;
    $passkey = password_hash($password, PASSWORD_DEFAULT);
    $userData = [
        'fullname' => $fullname,
        'phone' => $phone,
        'email' => $email,
        'designation' => $designation,
        'home_address' => $address,
        'passkey' => $passkey
    ];

    $createUser = insert('users', $userData);
    if ($createUser) {
        return $createUser;
    } else {
        return mysqli_error($connection);
    }
}

function EditUser($fullname, $phone, $email, $designation, $address)
{
    global $connection;
    $userData = [
        'fullname' => $fullname,
        'phone' => $phone,
        'email' => $email,
        'designation' => $designation,
        'home_address' => $address
    ];

    $editUser = update('users', $email, 'email', $userData);
    if ($editUser) {
        return $editUser;
    } else {
        return mysqli_error($connection);
    }
}

function CreateUserPermission($user_id, $users, $departments, $documents_management, $upload_files, $read_files, $edit_files)
{
    global $connection;
    $userPermissionData = [
        'users_id' => $user_id,
        'users_management' => $users,
        'departments_management' => $departments,
        'documents_management' => $documents_management,
        'upload_files' => $upload_files,
        'read_files' => $read_files,
        'edit_files' => $edit_files
    ];

    $createPermission = insert('permissions', $userPermissionData);
    if ($createPermission) {
        return $createPermission;
    } else {
        return mysqli_error($connection);
    }
}

function editUserPermission($user_id, $users, $departments, $documents_management, $upload_files, $read_files, $edit_files)
{
    global $connection;
    $userPermissionData = [
        'users_id' => $user_id,
        'users_management' => $users,
        'departments_management' => $departments,
        'documents_management' => $documents_management,
        'upload_files' => $upload_files,
        'read_files' => $read_files,
        'edit_files' => $edit_files
    ];

    $editPermission = update('permissions', $user_id, 'users_id', $userPermissionData);
    if ($editPermission) {
        return $editPermission;
    } else {
        return mysqli_error($connection);
    }
}

function AuthenticateUser($email, $password)
{
    global $today;
    $findUser = customQuery2("Select users.*, permissions.* FROM users JOIN permissions ON users.id = permissions.users_id AND users.email = '$email'");
    if (password_verify($password, $findUser['passkey'])) {
        update('users', $email, 'email', ['last_login' => $today]);
        return $findUser;
    } else {
        return "Invalid Login";
    }
}

function LogInformation($user_id, $activity, $method_name){
    global $connection;

    $activityData = [
        'users_id' => $user_id,
        'activity' => $activity,
        'method_name' => $method_name
    ];

    $recordActitvity =  insert('actitvity_logs', $activityData);
    if ($recordActitvity) {
        return $recordActitvity;
    } else {
        return mysqli_error($connection);
    }
}

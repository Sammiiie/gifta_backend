<?php
define('DB_SERVER', '162.144.39.52:3306');
define('DB_USERNAME', 'aberdeen');
define('DB_PASSWORD', '097ji+Js+TWrF5');
define('DB_CHARSET', 'utf-8');
define('DB_NAME', 'aberdeen_efacs');

// connect to the database with the defined values
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// if there's an error
if (!$connection) {
    echo "Failed to connect database" . die(mysqli_error($connection));
}
$dbselect = mysqli_select_db($connection, DB_NAME);
if (!$dbselect) {
    echo "Failed to select database" . die(mysqli_error($connection));
}
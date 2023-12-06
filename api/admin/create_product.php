<?php

include 'header.php';

// get posted data
$data = file_get_contents('php://input');
$data = json_decode($data, true);
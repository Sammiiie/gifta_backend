<?php
require("../vendor/autoload.php");

$openapi = \OpenApi\Generator::scan([$_SERVER['DOCUMENT_ROOT'].'/gifta_backend/models']);

header('Content-Type: application/json');
echo $openapi->toJSON();
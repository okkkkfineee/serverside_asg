<?php
require_once __DIR__ . '/../src/env_load.php';

$env = loadEnv(__DIR__ . '/../src/.env');

//server database connection
$server = $env['DB_SERVER'];
$user = $env['DB_USER'];
$password = $env['DB_PASSWORD'];
$database = $env['DB_DATABASE'];

$conn = new mysqli($server, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
return $conn;
?>

<?php
require_once __DIR__ . '/../src/env_load.php';

$env = loadEnv(__DIR__ . '/../src/.env');

//server database connection
$server = $env['DB_SERVER'];
$user = $env['DB_USER'];
$password = "";
$database = $env['DB_DATABASE'];

$conn = mysqli_connect(hostname: $server, username: $user, password: $password, database: $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

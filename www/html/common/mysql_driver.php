<?php
$mysqli = new mysqli($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"], $_ENV["DB_NAME"]);
if ($mysqli->connect_error) {
    exit("Error While connecting to DataBase");
}
$mysqli->set_charset("utf8mb4");
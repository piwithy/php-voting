<?php

$db_address = $_ENV["DB_ADDRESS"];
$db_user = $_ENV["DB_USER"];
$db_pass = $_ENV["DB_PASS"];
$db_root_pass= $_ENV["DB_ROOT_PASS"];
$db_name = $_ENV["DB_NAME"];
$default_admin_password = $_ENV["DEFAULT_ADMIN_PASSWORD"];


$mysqli = new mysqli($db_address, "root", "P4VKmb3TyzNH2k4Y");
if ($mysqli->connect_error) {
    exit("Error While connecting to DataBase");
}
$mysqli->set_charset("utf8mb4");


$mysqli->query("CREATE USER IF NOT EXISTS '$db_user'@'172.0.0.0/255.0.0.0' IDENTIFIED BY '$db_pass';");

$mysqli->query("CREATE DATABASE IF NOT EXISTS $db_name;");

$mysqli->query("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'172.0.0.0/255.0.0.0' IDENTIFIED BY '$db_pass';");

$mysqli->query("FLUSH PRIVILEGES;");

$mysqli->close();

$mysqli = new mysqli($db_address, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    exit("Error While connecting to DataBase");
}
$mysqli->set_charset("utf8mb4");

$mysqli->query("CREATE TABLE IF NOT EXISTS votes(id INTEGER PRIMARY KEY AUTO_INCREMENT, vote_target VARCHAR(255), ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP);");

$mysqli->query(" CREATE table accounts(id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, username VARCHAR(50) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, is_admin int(11) NOT NULL DEFAULT 0, active int(11) NOT NULL DEFAULT 1);");

$mysqli->query("CREATE TABLE IF NOT EXISTS custom_fields(field_id INTEGER PRIMARY KEY AUTO_INCREMENT, field_name VARCHAR(255), field_value VARCHAR(1024));");

$user = "admin";
$pass = password_hash($default_admin_password, PASSWORD_BCRYPT);

$mysqli->query("INSERT INTO accounts (username, password) SELECT '$user', '$pass' WHERE NOT EXISTS(SELECT * FROM accounts);");
$mysqli->query("UPDATE accounts SET is_admin=1, active=1 WHERE username='admin'");


$mysqli->query("INSERT INTO custom_fields(field_name, field_value) VALUES('index_title', 'Exemple Title')");
$mysqli->query("INSERT INTO custom_fields(field_name, field_value) VALUES('index_quote', 'Exemple Quote')");


if(!unlink("setup.php")){
    echo("Error during Setup!");
}else{
    echo("OK");
}
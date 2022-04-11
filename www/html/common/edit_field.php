<?php

session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}


if (!isset($_POST['field_id'], $_POST['field_name'], $_POST['field_value'])) {
    header("Location: ../admin/config.php?field_missing");
}


$stmt = $con->prepare("UPDATE custom_fields SET field_value=? WHERE field_id=?;");
$stmt->bind_param("si", $_POST["field_value"], $_POST["field_id"]);
if ($stmt->execute()) {
    header("Location: ../admin/config.php?edit_success&field_name=" . $_POST["field_name"]);
} else {
    header("Location: ../admin/config.php?edit_failed&field_name=" . $_POST["field_name"]);
}

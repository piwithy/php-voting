<?php

session_start();

$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}

if (!isset($_SESSION['loggedin'], $_SESSION['is_admin'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}

// Try Connection
$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}

if (!isset($_POST['formID'])) {
    header("Location: ../admin/users.php?form_error");
}

if ($_POST['formID'] == 'add_user') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        header("Location: ../admin/user.php?form_field_error=add");
    }
    if (isset($_POST['username'], $_POST['password'])) {
        $stmt = $con->prepare("SELECT username, password FROM accounts WHERE username=?");
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            header("Location: ../admin/users.php?user_add_error=already_exists");
        }
        $stmt->close();
        $admin = isset($_POST['admin']) ? 1 : 0;
        $stmt = $con->prepare("INSERT INTO accounts (username, password, is_admin, active) VALUES (?,?,?,1);");
        $hash = password_hash(htmlspecialchars($_POST["password"]), PASSWORD_BCRYPT);
        $stmt->bind_param('ssi', $_POST["username"], $hash, $admin);
        if ($result = $stmt->execute()) {
            header("Location: ../admin/users.php?user_add_success");
        } else {
            header("Location: ../admin/users.php?user_add_error=unknown");
        }
    } else {
        header("Location: ../admin/users.php?form_field_error=add");
    }
}

if ($_POST['formID'] == 'edit_user') {
    $admin = isset($_POST['admin']) ? 1 : 0;
    $active = isset($_POST['active']) ? 1 : 0;

    if(empty($_POST['password'])){
        $stmt = $con->prepare("UPDATE accounts SET is_admin=?,active=? WHERE id=?");
        $stmt->bind_param('iii', $admin, $active, $_POST['user_id']);
    }else{
        $hash = password_hash(htmlspecialchars($_POST["password"]), PASSWORD_BCRYPT);
        $stmt = $con->prepare("UPDATE accounts SET is_admin=?,active=?,password=? WHERE id=?");
        $stmt->bind_param("iisi", $admin, $active, $hash, $_POST['user_id']);
    }
    if($result = $stmt->execute()){
        header("Location: ../admin/users.php?user_edit_success");
    }else{
        header("Location: ../admin/users.php?user_edit_error");
    }
}


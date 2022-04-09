<?php
session_start();


// Try Connection
$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}


if (!isset($_POST['username'], $_POST['password'])) {
    exit('Please fill both Username & Password!');
}

if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username=?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if (password_verify($_POST['password'], $password)) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: ../admin/vote.php');
        } else {
            header('Location: ../admin/index.php?login_error');
        }
    } else {
        header('Location: ../admin/index.php?login_error');
    }

    $stmt->close();
}

$con->close();
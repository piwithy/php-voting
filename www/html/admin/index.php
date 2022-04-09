<?php
session_start();
if (isset($_SESSION['loggedin'])) {
    header('Location: vote.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title> Piwithy's Voting App | Login</title>
    <link rel="icon" href="../img/piwithy_logo_black.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="login">
    <h1>Login</h1>
    <h2 id="login_message">
        <?php
        if (isset($_GET['login_error'])) {
            echo "<span> Invalid Username and/or Password! </span>";
        }
        ?>
    </h2>

    <form action="../common/authenticate.php" method="post">
        <label for="username">
            <i class="fa fa-user"></i>
        </label>
        <input type="text" name="username" placeholder="Username" id="username" required>
        <label for="password">
            <i class="fa fa-lock"></i>
        </label>
        <input type="password" name="password" placeholder="Password" id="password" required>
        <input type="submit" value="Login">
    </form>

</div>
</body>
</html>

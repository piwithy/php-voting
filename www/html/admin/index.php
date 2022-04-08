<?php
include '../common/mysql_driver.php';
session_start();
$currentUser = null;
$badPass = false;
$found = false;
if (isset($_GET['logout']) && $_GET['logout']) {
    if (isset($_SESSION['user'])) {
        unset($_SESSION['user']);
    }
}
if (isset($_SESSION['user'])) {
    $usr = htmlspecialchars($_SESSION['user']);
    $query = $mysqli->prepare("SELECT * FROM users WHERE username= ? ;");
    $query->bind_param("s", $usr);
    $query->execute();
    $result = $query->get_result();
    if ($result) {
        $row = $result->fetch_row();
        if($row[3] == 1) $currentUser = $row[1];
        $result->close();
    }
} else if (isset($_POST['username']) && isset($_POST['password'])) {
    $usr = htmlspecialchars($_POST['username']);
    $query = $mysqli->prepare("SELECT * FROM users WHERE username=?;");
    $query->bind_param("s", $usr);
    $query->execute();
    $result = $query->get_result();
    if ($result && $row = $result->fetch_row()) {
        $pass = htmlspecialchars($_POST['password']);
        if (password_verify($pass, $row[2]) && $row[3] == 1) {
            $currentUser = $row[1];
            $_SESSION['user'] = $currentUser;
        } else {
            $found = true;
            $badPass = true;
        }
    } else {
        $found = true;
        $badPass = true;
    }
} else {
    $found = true;
}
if ($currentUser != null) {
    $mysqli->close();
    $_SESSION['user'] = $currentUser;
    header('Location: voting.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Foy'z Voting - V1.0 | Login</title>
    <link rel="stylesheet" href="../css/index.css"/>
    <link rel="icon" href="../images/icon.ico"/>
</head>
<body>
<div class="wrapper">
    <div class="Contents">
        <!--<div class="Logo"><img src="images/logoRacingTeam.png" alt="icon"></div>-->
        <h1>Foy'z Voting - V1.0</h1>
        <form class="Login" action="index.php" method="post">
            <?php
            if ($badPass || !$found) {
                echo("<div class=\"BadPass\"> <h2> Bad Username or Password </h2> </div>");
            }
            ?>
            <p><label> Nom d'utilisateur:<br> <input type="text" name="username" value="" maxlength="256"/></label></p>
            <p><label> Mot de Passe:<br> <input type="password" name="password" value=""/></label></p>
            <p><input type="submit" value="login"/></p>
        </form>
    </div>
</div>
</body>
</html>


<?php
session_start();

if (!isset($_SESSION['loggedin'], $_SESSION['is_admin'])) {
    header('Location: index.php');
    exit;
}

$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}
$qry = $con->prepare("SELECT id, active FROM accounts where id=?");
$qry->bind_param('i', $_SESSION['id']);
$qry->execute();
$qry->store_result();
if ($qry->num_rows > 0) {
    $qry->bind_result($uid, $active);
    $qry->fetch();
    if ($active == 0) header("Location: logout.php");
} else {
    header("Location: logout.php");
}
$qry->close();

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Piwithy's Voting App | Contact Page</title>
    <link rel="icon" href="../img/piwithy_logo_black.png">
    <script src="https://kit.fontawesome.com/09d79beec4.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("a.reset").click(function (e) {
                if (!confirm('Are you sure you want to RESET ALL Votes ?')) {
                    e.preventDefault();
                    return false;
                }
                return true;
            })
        })

    </script>

</head>
<body class="loggedin">
<nav class="navtop">
    <div>
        <h1><a href="/">Piwithy's Voting App</a></h1>
        <?php if ($_SESSION['is_admin'] == 1) echo "<a href='users.php'><i class='fa-solid fa-user-group'></i>Users</a>" ?>
        <a href="config.php"><i class="fa-solid fa-gear"></i>App Config</a>
        <?php if($_SESSION['is_admin']==1) echo "<a class='reset' href='../common/edit_vote.php?reset=true' style='background-color: red'><i class='fa-solid fa-trash-can'></i>Clear Votes</a>"?>
        <a href="contact.php"><i class="fa-solid fa-address-book"></i>Contact</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<div class="content">
    <div class="contact_info">
        <h2><i class="fa-solid fa-address-book"></i> Contact</h2>
        <p>Voting App Developped By: Pierre-Yves JÉZÉGOU (FIPA 2021)</p>
        <p><i class="fa-solid fa-envelope"></i> Email: <a href="mailto://pierre-yves.jezegou@ensta-bretagne.org">pierre-yves.jezegou@ensta-bretagne.org</a></p>
        <p><i class="fa-solid fa-envelope"></i> Email: <a href="mailto://jezegoup@gmail.com">jezegoup@gmail.com</a></p>
        <!--<p><i class="fa-brands fa-github"></i> Github: <a href="https://github.com/piwithy">piwithy</a></p>-->
    </div>

    <div class="footer">Copyright &copy; Pierre-Yves Jézégou 2021 -
        <script>document.write((new Date().getFullYear()).toString())</script>
    </div>

</div>

</body>
</html>

<?php
include "mysqli_connect.php";
session_start();
$isAdmin = false;
if (isset($_SESSION['user'])) {
    $usr = htmlspecialchars($_SESSION['user']);
    $query = $mysqli->prepare("SELECT * FROM users WHERE username= ? ;");
    $query->bind_param("s", $usr);
    $query->execute();
    $result = $query->get_result();
    if ($result) {
        $row = $result->fetch_row();
        $currentUser = $row[1];
        $result->close();
    }
} else {
    $mysqli->close();
    header('Location: index.php');
    exit();
}

$query = $mysqli->prepare("TRUNCATE votes;");
$query->execute();
$result = $query->get_result();
//header("Location: https://voting.piwithy.fr/admin.php");
?>


<script type="text/javascript">
    window.location.replace("https://voting.piwithy.fr/admin.php");
</script>
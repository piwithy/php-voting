<?php
include "common/mysql_driver.php";
$page = $_SERVER['PHP_SELF'];
$sec = "30";
$dt = new DateTime();
$result = $mysqli->query("SELECT COUNT(*) as totalEntries from votes;");
$count = 0;
if ($result) {
    $row = $result->fetch_assoc();
    $count = $row['totalEntries'];
    $result->close();
}
$title = "Qui serait le meilleur survivant de l'Apocalypse ?";
$sub_title = "";


$result = $mysqli->query("SELECT vote_target,COUNT(*) AS count FROM votes GROUP BY vote_target ORDER BY count DESC;");
$candidate = $result->num_rows;
$result = $mysqli->query("SELECT vote_target,COUNT(*) AS count FROM votes GROUP BY vote_target ORDER BY count DESC LIMIT 20;");


?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Foy'z Voting - V1.0 | Index</title>
    <link rel="stylesheet" href="css/index.css"/>
    <link rel="stylesheet" href="css/table.css"/>
    <link rel="icon" href="images/icon.ico"/>
    <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
</head>

<body>
<div class="wrapper">
    <div class="caption">
        <span class="goal"></span>

    </div>
    <div class="table"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.goal').load("common/get_title.php");
            $('.table').load("common/get_ranking.php?count=5");
            setInterval(function () {
                $('.table').load("common/get_ranking.php?count=5");
                $('.goal').load("common/get_title.php");
            }, 10000);

        });
    </script>
    <div class="footer">
    </div>
</div>
</body>

</html>
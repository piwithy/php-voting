<?php
include "common/mysqli_connect.php";
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
    <link rel="stylesheet" href="../html/css/index.css"/>
    <link rel="stylesheet" href="../html/css/table.css"/>
    <link rel="icon" href="images/icon.ico"/>
    <script type="text/javascript" src='../html/js/jquery-3.4.1.js'></script>
</head>

<body>
<div class="wrapper">
    <div class="caption">
        <span class="goal"><?php echo $title ;?><br><?php echo $sub_title?></span>

    </div>
    <div class="table"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            function make_table(){
                
            }


            $('.table').load("common/request_vote.php");
            setInterval(function () {
                $('.table').load("common/request_vote.php")
            }, 10000);
        });
    </script>
    <div class="footer">
    </div>
</div>
</body>

</html>




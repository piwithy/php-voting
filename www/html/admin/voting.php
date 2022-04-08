<?php
include '../common/mysql_driver.php';
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
$voteError = false;
$voted = false;
if(isset($_POST['corr'])){
    $query=$mysqli->prepare("DELETE FROM votes WHERE vote_target=? LIMIT 1");
    $vote=htmlspecialchars($_POST['voteTarget']);
    $query->bind_param("s", $vote);
    if(!$result=$query->execute()){
        $voteError=true;
    }
}else {
    if (isset($_POST['voteTarget'])) {
        $voted = true;
        $query = $mysqli->prepare("INSERT INTO votes(vote_target,active) VALUES (?, 1)");
        $vote = htmlspecialchars($_POST['voteTarget']);
        $query->bind_param("s", $vote);
        if (!$result = $query->execute()) {
            $voteError = true;
        }
    }
}
$result = $mysqli->query("SELECT vote_target,COUNT(*) AS count FROM votes WHERE active=1 GROUP BY vote_target ORDER BY count DESC, vote_target ASC;");

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Foy'z Voting - V1.0 | Admin</title>
    <link rel="stylesheet" href="../css/index.css"/>
    <link rel="stylesheet" href="../css/table.css"/>
    <link rel="icon" href="https://foyz.fr/img/favicon.png"/>
    <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("a.delete").click(function(e){
                if(!confirm('Etes vous sur de vouloir reinitialiser les votes?')){
                    e.preventDefault();
                    return false;
                }
                return true;
            });
        });
    </script>
</head>
<body>
<div class="wrapper">
    <div class="menu">
        <nav>
            <ul>
                <li><span class="menuItem"><a href="../index.php"> Acceuil </a> </span></li>
                <li><span class="menuItem"><a href="user_management.php"> Gestion des Utilisateurs </a></span></li>
                <li><span class="menuItem"><a href="configuration.php"> Configuration </a></span></li>
                <li><span class="menuItem"><a href="voting.php">Gestion des Votes</a></span> </li>
                <li><span class="menuItem"><a href="../common/reset_vote.php" class="delete" style="background-color: red">Reset Votes</a></span></li>
                <li><span class="menuItem"><a href="index.php?logout=true">Déconnexion</a></span></li>
            </ul>
        </nav>
    </div>
    <div class="Contents">
        <form action="voting.php" method="post">

            <table>
                <caption>
                    <div id="vote_result">
                        <?php
                        if ($voted == true) {
                            if ($voteError == false) {
                                echo '<span class="success"> A voté </span></br>';
                            } else {
                                echo '<span class="error"> Voting Error! </span></br>';
                            }
                        }
                        ?>
                    </div>
                    Vote Manuel
                </caption>
                <thead>
                <tr>
                    <th scope="col">Candidat</th>
                    <th scope="col">Vote</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td data-label="Candidat"><input type="text" name="voteTarget" placeholder="c qui?"></td>
                    <td data-label="Vote"><input type="submit" value="Vote!"></td>
                </tr>
                </tbody>
                <script>
                    $(document).ready(function () {
                        setTimeout(function () {
                            $('#vote_result').fadeOut('slow');

                        }, 5000);
                    });
                </script>
            </table>
        </form>
    </div>
    <div class="quickAdd">
        <table>
            <caption>Vote Rapide</caption>
            <thead>
            <tr>
                <th scope="col">Rang</th>
                <th scope="col">Candidat</th>
                <th scope="col">Nombre de Votes</th>
                <th scope="col">Quick Vote</th>
                <th scope="col">Correction</th>
            </tr>
            </thead>
            <tbody>
            <?php

            if ($result) {
                $rank = 1;
                while ($row = $result->fetch_row()) {
                    $escaped_row = array();
                    foreach ($row as $elem) $escaped_row[] = htmlspecialchars($elem)
                    ?>
                    <tr>
                        <td data-label="Rang"><?php echo $rank ?></td>
                        <td data-label="Candidat"><?php echo $escaped_row[0] ?></td>
                        <td data-label="Nombre de Votes"><?php echo number_format($escaped_row[1]) ?></td>
                        <td data-label="Quick Vote">
                            <form action="voting.php" method="post"><input type="hidden"
                                                                           name="voteTarget" <?php echo('value="' . $escaped_row[0] . '"'); ?>>
                                <input type="submit" value="Vote!" name="quicky"></form>
                        </td>
                        <td data-label="Quick Vote">
                            <form action="voting.php" method="post"><input type="hidden"
                                                                           name="voteTarget" <?php echo('value="' . $escaped_row[0] . '"'); ?>>
                                <input type="submit" value="Retirer" name="corr"></form>
                        </td>
                    </tr>
                    <?php
                    $rank = $rank + 1;
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

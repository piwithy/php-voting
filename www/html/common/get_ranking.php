<?php
include "mysql_driver.php";

$page = $_SERVER['PHP_SELF'];
$sec = "30";
$dt = new DateTime();
$dt->setTimezone(new DateTimeZone('Europe/Paris'));
$result = $mysqli->query("SELECT COUNT(*) as totalEntries from votes WHERE active=1;");
$count = 0;
if ($result) {
    $row = $result->fetch_assoc();
    $count = $row['totalEntries'];
    $result->close();
}

$result = $mysqli->query("SELECT COUNT(DISTINCT vote_target) as candidates from votes WHERE active=1;");
$candidate = 0;
if($result){
    $row = $result->fetch_assoc();
    $candidate = $row['candidates'];
    $result->close();
}


if (isset($_GET['count'])) {
    $query = $mysqli->prepare("SELECT vote_target, COUNT(*) AS vote_count FROM votes WHERE active=1 GROUP BY vote_target ORDER BY vote_count DESC, vote_target ASC LIMIT ?;");
    $query->bind_param("i", $_GET['count']);
    $query->execute();
    $result = $query->get_result();
}else{
    $query = $mysqli->prepare("SELECT vote_target, COUNT(*) AS vote_count FROM votes WHERE active=1 GROUP BY vote_target ORDER BY vote_count DESC, vote_target ASC ;");
    //$query->bind_param("i", $_GET['COUNT']);
    $query->execute();
    $result = $query->get_result();
}

?>

<table>
    <thead>
    <tr>
        <th scope="col">Rang</th>
        <th scope="col">Candidat</th>
        <th scope="col">Popularité</th>
        <th scope="col">Diff</th>
    </tr>
    </thead>
    <tbody>
    <?php

    if ($result) {
        $rank = 1;
        $p1 = 1;
        $p1_vote = 0;
        while ($row = $result->fetch_row()) {
            $relative_vote = ($row[1] / $count) * 100;

            if ($rank == 1) {
                $p1 = 100 / $relative_vote;
                $p1_vote = $row[1];
            }
            $popularity = $relative_vote * $p1;
            $left = $p1_vote - $row[1];
            ?>
            <tr>
                <td data-label="Rang"><?php echo $rank ?></td>
                <td data-label="Candidat"><?php echo htmlspecialchars($row[0]) ?></td>
                <td data-label="Popularité">
                    <progress max="100"
                              value=<?php echo '"' . number_format($popularity, 2) . '"'; ?>> <?php echo number_format($relative_vote, 2); ?>
                        %
                    </progress>
                </td>
                <td data-label="Diff">
                    <?php
                    if ($left ==0){
                        echo "--";
                    }else{
                        echo "-".$left;
                    } ?> </td>
            </tr>
            <?php
            $rank = $rank + 1;
        }
    }
    ?>
    </tbody>
</table>
<div class="info">
    <span class="elem">Nombre de votes: <?php echo $count ?></span>
    <span class="elem"><?php echo $candidate ?> Candidats</span>
    <span class="elem">Dernier rafraichissement: <?php echo $dt->format("d-m-Y H:i:s"); ?></span>
</div>

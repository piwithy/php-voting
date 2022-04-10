<?php

$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}

if (isset($_GET['caption'])) {
    $title = "Test Title";
    $subtitle = "Test Subtitle";

    $result = $con->query("SELECT field_value FROM custom_fields WHERE field_name='index_title';");
    $row = $result->fetch_assoc();
    $title= $row["field_value"];

    $result = $con->query("SELECT field_value FROM custom_fields WHERE field_name='index_quote';");
    $row = $result->fetch_assoc();
    $subtitle= $row["field_value"];

    echo "<p><h2>$title</h2><br><span>$subtitle</span></p>";

} elseif (isset($_GET['ranking'])) {

    //TODO Fetch RANKING DATA & REMOVE STATIC
    $result = $con->query("SELECT COUNT(*) as totalEntries from votes WHERE active=1");
    $vote_count = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        $vote_count = $row['totalEntries'];
        $result->close();
    }
    $result = $con->query("SELECT COUNT(DISTINCT vote_target) as candidates from votes WHERE active=1;");
    $candidates_count = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        $candidates_count = $row['candidates'];
        $result->close();
    }

    if (!isset($_GET['count']) || $_GET['count'] == 0) {
        $stmt = $con->prepare("SELECT vote_target, COUNT(*) AS vote_count FROM votes WHERE active=1 GROUP BY vote_target ORDER BY vote_count DESC, vote_target ASC LIMIT ?;");
        $stmt->bind_param("i", $_GET['count']);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $stmt = $con->prepare("SELECT vote_target, COUNT(*) AS vote_count FROM votes WHERE active=1 GROUP BY vote_target ORDER BY vote_count DESC, vote_target ASC;");
        //$stmt->bind_param("i", $_GET['count']);
        $stmt->execute();
        $result = $stmt->get_result();
    }


    $winner_votes = 5;
    $dt = new DateTime();
    $dt->setTimezone(new DateTimeZone('Europe/Paris'));
    ?>

    <table>
        <thead>
        <tr>
            <th scope="col">Rank</th>
            <th scope="col">Candidate</th>
            <th scope="col">Popularity</th>
            <th scope="col">Dif</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            if ($rank == 1) $winner_votes = $row['vote_count'];
            ?>
            <tr>
                <td data-label="Rank"><?php echo $rank ?></td>
                <td data-label="Candidate"><?php echo $row['vote_target']?></td>
                <td data-label="Popularity">
                    <progress max="1" value="<?php echo $row['vote_count'] / $vote_count ?>"></progress>
                </td>
                <td data-label="Dif">
                    <?php
                    if ($winner_votes - $row['vote_count'] == 0) echo "--";
                    else echo "-" . ($winner_votes - $row['vote_count']);
                    ?>
                </td>
            </tr>
            <?php
            $rank++;
        }
        ?>

        </tbody>
    </table>
    <div class="info">
        <span class="elem"> Vote Count: <?php echo $vote_count ?></span>
        <span class="elem"> <?php echo $candidates_count ?> Candidate<?php if ($candidates_count > 1) echo "s" ?> </span>
        <span class="elem"> Last refresh: <?php echo $dt->format('d-m-Y H:i:s') ?></span>
    </div>

    <?php
}


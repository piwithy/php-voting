<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}

$result = $con->query("SELECT vote_target,COUNT(*) AS count FROM votes WHERE active=1 GROUP BY vote_target ORDER BY count DESC, vote_target ASC;");

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Piwithy's Voting App | Voting Page</title>
    <link rel="icon" href="../img/piwithy_logo_black.png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
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
        <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
        <a class="reset" href="../common/edit_vote.php?reset=true" style="background-color: red"><i class="fa fa-trash">
                Reset Votes!</i></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<div class="content">
    <div id="vote_info">
        <?php
        if (isset($_GET['form_error'])) {
            echo '<span class="error">ERROR: Please use a Form!</span>';
        } elseif (isset($_GET['vote_add_err'])) {
            $vote_added = $_GET['vote_add_err'];
            echo "<span class='error'>ERROR While adding Votes ($vote_added votes added)</span>";
        } elseif (isset($_GET['vote_success'])) {
            $vote_added = $_GET['vote_success'];
            echo "<span class='success'>$vote_added votes successfully added!</span>";
        }

        ?>
    </div>
    <div class="custom_vote">
        <h2>Custom Vote</h2>
        <form action="../common/edit_vote.php" method="post">
            <input type="hidden" id="form_id" name="form_id" value="custom_vote">
            <label for="vote_target">
                <i class="fa fa-user"></i>
            </label>
            <input type="text" name="vote_target" placeholder="Candidate" id="vote_target" required>
            <label for="vote_count">
                <i class="fa fa-hashtag"></i>
            </label>
            <select name="vote_count" id="vote_count">
                <?php
                for ($i = 0; $i < 10; $i++) {
                    echo "<option>" . ($i + 1) . "</option>";
                }
                ?>
            </select>
            <input type="submit" value="Vote!">
        </form>
    </div>

    <div class="quick_vote">
        <h2>Quick Vote</h2>
        <table>
            <thead>
            <tr>
                <th scope="col">Rank</th>
                <th scope="col">Candidate</th>
                <th scope="col">Vote Count</th>
                <th scope="col">Quick Vote</th>
                <th scope="col">Correction</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($result) {
                $rank = 1;
                while ($row = $result->fetch_assoc()) {
                    $escaped_row = array();
                    foreach ($row as $key => $value) $escaped_row[$key] = htmlspecialchars($value);
                    ?>
                    <tr>
                        <td data-label="Rank"><?php echo $rank ?></td>
                        <td data-label="Candidate"><?php echo $escaped_row["vote_target"] ?></td>
                        <td data-label="Vote Count"><?php echo $escaped_row["count"] ?></td>
                        <td data-label="Quick Vote">
                            <form action="../common/edit_vote.php" method="post">
                                <input type="hidden" id="form_id" name="form_id" value="quick_vote">
                                <input type="hidden" id="vote_target" name="vote_target"
                                       value="<?php echo $escaped_row["vote_target"] ?>">
                                <select name="vote_count" id="vote_count">
                                    <?php
                                    for ($i = 0; $i < 10; $i++) {
                                        echo "<option>" . ($i + 1) . "</option>";
                                    }
                                    ?>
                                </select>
                                <input type="submit" value="Vote!">
                            </form>
                        </td>
                        <td data-label="Correction">
                            <form action="../common/edit_vote.php" method="post">
                                <input type="hidden" id="form_id" name="form_id" value="correction">
                                <input type="hidden" id="vote_target" name="vote_target"
                                       value="<?php echo $escaped_row["vote_target"] ?>">
                                <select name="vote_count" id="vote_count">
                                    <?php
                                    for ($i = 0; $i < 10 && $i < $escaped_row["count"]; $i++) {
                                        echo "<option>" . ($i + 1) . "</option>";
                                    }
                                    ?>
                                </select>
                                <input type="submit" value="Correct">
                            </form>
                        </td>
                    </tr>
                    <?php
                    $rank++;
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

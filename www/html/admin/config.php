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

$result = $con->query("SELECT * FROM custom_fields;");

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Piwithy's Voting App | Config Page</title>
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
        <?php if($_SESSION['is_admin']==1) echo "<a href='users.php'><i class='fa-solid fa-user-group'></i>Users</a>"?>
        <a href="vote.php"><i class="fa-solid fa-person-booth"></i>Voting Page</a>
        <?php if($_SESSION['is_admin']==1) echo "<a class='reset' href='../common/edit_vote.php?reset=true' style='background-color: red'><i class='fa-solid fa-trash-can'></i>Clear Votes</a>"?>
        <a href="contact.php"><i class="fa-solid fa-address-book"></i>Contact</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<div class="content">
    <div id="vote_info" <?php if (empty($_GET)) echo 'style="visibility: hidden;"' ?>>
        <?php
        if (isset($_GET['edit_success'], $_GET['field_name'])) {
            echo "<span class='success'> field \"" . htmlspecialchars($_GET['field_name']) . "\" Successfully edited! </span>";
        } elseif (isset($_GET['edit_failed'], $_GET['field_name'])) {
            echo "<span class='error'> Error while editing field \"" . htmlspecialchars($_GET['field_name']) . "\"! </span>";
        }

        ?>
    </div>
    <div class="title_config">
        <h2>Configure Fields</h2>
        <table>
            <?php
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $escaped_row = array();
                    foreach ($row as $key => $value) $escaped_row[$key] = htmlspecialchars($value);
                    ?>
                    <tr>
                        <td><?= $escaped_row['field_id'] ?></td>
                        <td><?= $escaped_row['field_name'] ?></td>
                        <td>
                            <form action="../common/edit_field.php" method="post">
                                <input type="hidden" name="field_id" id="field_id"
                                       value="<?= $escaped_row['field_id'] ?>">
                                <input type="hidden" name="field_name" id="field_name"
                                       value="<?= $escaped_row['field_name'] ?>">
                                <label>
                                    <input type="text" name="field_value" id="field_value"
                                           value="<?= $escaped_row['field_value'] ?>">
                                </label>
                                <input type="submit" value="Edit!">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            }

            ?>
        </table>
    </div>


    <div class="footer">Copyright &copy; Pierre-Yves Jézégou 2021 -
        <script>document.write((new Date().getFullYear()).toString())</script>
    </div>

</div>
</body>
</html>

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
$edit_error = false;
$edited = false;
$created = false;
if (isset($_POST["editFieldID"])) {

    if ($_POST["editValue"]) {
        $field_id = $_POST["editFieldID"];
        $field_value = $_POST["editValue"];
        $query = $mysqli->prepare("UPDATE custom_fields SET field_value=? WHERE field_id=?;");
        $query->bind_param("si", $field_value, $field_id);
        if ($query->execute()) {
            $edited = true;
        } else {
            $edit_error = true;
        }
    }else{
        $field_id = $_POST["editFieldID"];
        $field_value = "";
        $query = $mysqli->prepare("UPDATE custom_fields SET field_value=? WHERE field_id=?;");
        $query->bind_param("si", $field_value, $field_id);
        if ($query->execute()) {
            $edited = true;
        } else {
            $edit_error = true;
        }
    }
}



$result = $mysqli->query("SELECT * FROM custom_fields;");

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
        $(document).ready(function () {
            $("a.delete").click(function (e) {
                if (!confirm('Etes vous sur de vouloir reinitialiser les votes?')) {
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
                <li><span class="menuItem"><a href="voting.php">Gestion des Votes</a></span></li>
                <li><span class="menuItem"><a href="../common/reset_vote.php" class="delete"
                                              style="background-color: red">Reset Votes</a></span></li>
                <li><span class="menuItem"><a href="index.php?logout=true">Déconnexion</a></span></li>
            </ul>
        </nav>
    </div>
    <div class="Contents">
    </div>
    <div class="quickAdd">
        <table>
            <caption>

                Modification
            </caption>
            <thead>
            <tr>
                <th scope="col">Field ID</th>
                <th scope="col">Field Name</th>
                <th scope="col">Field Value</th>
                <th scope="col">Edit</th>
            </tr>
            </thead>
            <tbody>
            <?php

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $escaped_row = array();
                    foreach ($row as $key=>$value) $escaped_row[$key] = htmlspecialchars($value);
                    ?>

                    <tr>
                        <td data-label="Field ID">
                            <form id="<?php echo "edit_" . $escaped_row["field_id"] ?>" method="post"
                                  action="configuration.php">
                                <input type="hidden" name="editFieldID" value="<?php echo $escaped_row["field_id"] ?>">
                            </form>
                            <?php echo $escaped_row["field_id"] ?>
                        </td>
                        <td data-label="Nom d'utilisateur"><?php echo $escaped_row["field_name"] ?></td>
                        <td data-label="Mot de Passe">
                            <input form="<?php echo "edit_" . $escaped_row["field_id"] ?>" type="text"
                                   value="<?php echo $escaped_row["field_value"] ?>"
                                   name="editValue">
                        </td>
                        <td data-label="Modifier">
                            <input form="<?php echo "edit_" . $escaped_row["field_id"] ?>"
                                   type="submit" value="Modifier" name="">
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>


</div>
<script>
    $(document).ready(function () {
        setTimeout(function () {
            $('#vote_result').fadeOut('slow');

        }, 5000);
    });
</script>
</body>
</html>

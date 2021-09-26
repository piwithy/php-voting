<?php
include '../common/mysql_driver.php';
session_start();
$isAdmin = false;
$prod_deploy = false;
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
if (isset($_POST["editUserID"])) {

    if ($_POST["editPassword"]) {
        $user_id = $_POST["editUserID"];
        $hashed_password = password_hash($_POST["editPassword"], PASSWORD_BCRYPT);
        $query = $mysqli->prepare("UPDATE users SET hash=? WHERE user_id=?;");
        $query->bind_param("si", $hashed_password, $user_id);
        if ($query->execute()) {
            $edited = true;
        } else {
            $edit_error = true;
        }
    }
}
if (isset($_POST["createUser"])) {
    if ($_POST["newUserName"] && $_POST["newUserPassword"]) {
        $n_user = $_POST["newUserName"];
        $n_password = password_hash($_POST["newUserPassword"], PASSWORD_BCRYPT);
        $query = $mysqli->prepare("INSERT INTO users(username, hash) VALUES(?,?)");
        $query->bind_param("ss", $n_user, $n_password);
        if ($result = $query->execute()) {
            $created = true;
        } else {
            $edit_error = true;
        }
    }
}


$result = $mysqli->query("SELECT user_id, username, hash FROM users;");

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Foy'z Voting - V1.0 | Admin</title>
    <link rel="stylesheet" href="../css/index.css"/>
    <link rel="stylesheet" href="../css/table.css"/>
    <link rel="icon" href="../images/icon.ico"/>
    <script type="text/javascript" src="../js/jquery-3.4.1.js"></script>
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
        <form id="add_user" action="user_management.php" method="post"></form>
        <table>
            <caption>
                <div id="vote_result">
                    <?php
                    if ($edit_error) {
                        echo '<span class="error"> Erreur lors de la modification des utilisateurs</span></br>';
                    } elseif ($edited) {
                        echo '<span class="success"> Utilisateurs Modifiés </span></br>';
                    } elseif ($created) {
                        echo '<span class="success"> Utilisateur Créé </span></br>';
                    }
                    ?>
                </div>
                Ajout d'utilisateur
            </caption>
            <thead>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Mot De Passe</th>
                <th>Ajouter</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td data-label="Nom d'utilisateur">
                    <input form="add_user" name="newUserName" type="text" placeholder="Nom d'utilisateur">
                </td>
                <td data-label="Mot de Passe">
                    <input form="add_user" name="newUserPassword" type="password" placeholder="Mot de passe">
                </td>
                <td data-label="Modifier">
                    <input form="add_user" type="hidden" name="createUser" value="">
                    <input form="add_user" type="submit" value="Creer" name="">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="quickAdd">
        <table>
            <caption>

                Modification
            </caption>
            <thead>
            <tr>
                <th scope="col">User ID</th>
                <th scope="col">Nom d'utilisateur</th>
                <th scope="col">Mot de Passe</th>
                <th scope="col">Modifier</th>
            </tr>
            </thead>
            <tbody>
            <?php

            if ($result) {
                while ($row = $result->fetch_assoc()) {

                    ?>

                    <tr>
                        <td data-label="User ID">
                            <form id="<?php echo "edit_" . $row["user_id"] ?>" method="post"
                                  action="user_management.php">
                                <input type="hidden" name="editUserID" value="<?php echo $row["user_id"] ?>">
                            </form>
                            <?php echo $row["user_id"] ?>
                        </td>
                        <td data-label="Nom d'utilisateur"><?php echo $row["username"] ?></td>
                        <td data-label="Mot de Passe">
                            <input form="<?php echo "edit_" . $row["user_id"] ?>" type="password"
                                   name="editPassword">
                        </td>
                        <td data-label="Modifier">
                            <input form="<?php echo "edit_" . $row["user_id"] ?>"
                                   type="submit" value="Modifier"
                                   name="" <?php if ($prod_deploy && $row["username"] == "admin") echo "disabled"; ?>>
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

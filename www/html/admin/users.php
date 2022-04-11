<?php
session_start();

if (!isset($_SESSION['loggedin'], $_SESSION['is_admin'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['is_admin'] != 1) {
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
    <title>Piwithy's Voting App | User Management</title>
    <link rel="icon" href="../img/piwithy_logo_black.png">
    <script src="https://kit.fontawesome.com/09d79beec4.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/slider_switch.css">
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
        <a href="config.php"><i class="fa-solid fa-gear"></i>App Config</a>
        <a href="vote.php"><i class="fa-solid fa-person-booth"></i>Voting Page</a>
        <?php if ($_SESSION['is_admin'] == 1) echo "<a class='reset' href='../common/edit_vote.php?reset=true' style='background-color: red'><i class='fa-solid fa-trash-can'></i>Clear Votes</a>" ?>
        <a href="contact.php"><i class="fa-solid fa-address-book"></i>Contact</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<div class="content">
    <div id="vote_info" <?php if (empty($_GET)) echo 'style="visibility: hidden;"' ?>>
        <?php
        if (isset($_GET['user_add_success'])) {
            echo "<span class='success'> User successfully added! </span>";
        } elseif (isset($_GET['user_add_error'])) {
            switch ($_GET['user_add_error']) {
                case 'already_exists':
                    echo "<span class='error'>User Already Exists !</span>";
                    break;
                case 'unknown':
                    echo "<span class='error'> Unknown Error while Adding User</span>";
                    break;
            }
            //echo "<span class='error'> Error while editing field \"" . $_GET['field_name'] . "\"! </span>";
        } elseif (isset($_GET['form_field_error'])) {
            switch ($_GET['form_field_error']) {
                case 'add':
                    echo "<span class='error'>Please fill all fields of the Add User Form</span>";
                    break;
            }
        } elseif (isset($_GET['user_edit_success'])) {
            echo "<span class='success'> User successfully edited! </span>";
        } elseif (isset($_GET['user_edit_error'])) {
            echo "<span class='error'> Unknown Error while Editing User </span>";
        }

        ?>
    </div>
    <div class="add_user">
        <h2>Add User</h2>

        <form action="../common/edit_users.php" method="post" autocomplete="off">
            <input type="hidden" name="formID" id="formID" value="add_user">
            <label><i class="fa-solid fa-user"></i>
                <input type="text" name="username" id="username" placeholder="Username" required/>
            </label>
            <label><i class="fa-solid fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required/>
            </label>
            Admin ?
            <label class="switch">
                <input type="checkbox" name="admin" id="admin">
                <span class="slider round"></span>
            </label>
            <input type="submit" value="Add!">
        </form>

    </div>

    <div class="edit_user">
        <h2>Edit Users</h2>
        <?php
        $result = $con->query("SELECT * FROM accounts;");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $escaped_row = array();
                foreach ($row as $key => $value) $escaped_row[$key] = htmlspecialchars($value);
                ?>
                <div class="user_<?= $escaped_row['username'] ?> user_card">
                    <h3>User&nbsp;:&nbsp;<?= $escaped_row['username'] ?></h3>
                    <form action="../common/edit_users.php" method="post">
                        <input type="hidden" name="formID" id="formID" value="edit_user">
                        <input type="hidden" name="user_id" id="user_id" value="<?= $escaped_row['id'] ?>"><br/>
                        <label>
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="Password"/>
                        </label><br/>
                        Admin&nbsp;:&nbsp;
                        <label class="switch">
                            <input type="checkbox" name="admin"
                                   id="admin" <?php if ($escaped_row['is_admin'] == 1) echo "checked" ?>>
                            <span class="slider round"></span>
                        </label><br/>
                        Active&nbsp;&nbsp;:&nbsp;
                        <label class="switch">
                            <input type="checkbox" name="active"
                                   id="active" <?php if ($escaped_row['active'] == 1) echo "checked" ?>>
                            <span class="slider round"></span>
                        </label><br/>
                        <input type="submit" value="Update"  <?php if($escaped_row['username'] == 'admin' && $_SESSION['name'] != 'admin' ) echo "disabled";?>>
                    </form>

                </div>
                <?php
            }
        }
        ?>
    </div>


    <div class="footer">Copyright &copy; Pierre-Yves Jézégou 2021 -
        <script>document.write((new Date().getFullYear()).toString())</script>
    </div>

</div>
</body>
</html>

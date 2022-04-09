<?php

session_start();

$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}

if (!isset($_SESSION['loggedin'])) {
    header('Location: ../admin/index.php');
    exit;
}

// Try Connection
$con = mysqli_connect($_ENV["DB_ADDRESS"], $_ENV["DB_USER"], $_ENV["DB_PASS"], $_ENV["DB_NAME"]);
if (mysqli_connect_errno()) {
    exit('Failed to connect MySQL: ' . mysqli_connect_error());
}

if (!isset($_POST['form_id']) && !isset($_GET['reset'])) {
    header("Location: ../admin/vote.php?form_error");
}

if ($_GET['reset'] == 'true') {
    $result = $con->query("UPDATE votes SET active=0 WHERE active=1");
    if ($result) {
        header("Location: ../admin/vote.php?reset_success");
    } else {
        header("Location: ../admin/vote.php?reset_error");
    }
} elseif ($_POST['form_id'] == "custom_vote" || $_POST['form_id'] == "quick_vote") {
    if (isset($_POST['vote_target'], $_POST['vote_count'])) {
        for ($i = 0; $i < $_POST['vote_count']; $i++) {
            $stmt = $con->prepare("INSERT INTO votes (vote_target) VALUES (?)");
            $vote_target = htmlspecialchars($_POST['vote_target']);
            $stmt->bind_param("s", $vote_target);
            if (!$result = $stmt->execute()) {
                header("Location: ../admin/vote.php?vote_add_err=$i");
                return;
            }
        }
        header("Location: ../admin/vote.php?vote_success=$i");
    } else {
        header("Location: ../admin/vote.php?form_field_error=vote");
    }

} elseif ($_POST['form_id'] == "correction") {
    if (isset($_POST['vote_target'], $_POST['vote_count'])) {
        $stmt = $con->prepare("DELETE FROM votes WHERE vote_target=? LIMIT ?");
        $vote_target = htmlspecialchars($_POST['vote_target']);
        $stmt->bind_param("si", $vote_target, $_POST['vote_count']);
        if (!$result = $stmt->execute()) {
            header("Location: ../admin/vote.php?vote_rm_err");
            return;
        }
        header("Location: ../admin/vote.php?vote_rm_success=" . $_POST['vote_count']);
    } else {
        header("Location: ../admin/vote.php?form_field_error=correct");
    }
}
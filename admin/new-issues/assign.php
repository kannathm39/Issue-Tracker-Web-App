<?php
//=========================================================================================
//-------------------------------------ADMIN PAGE------------------------------------------
//=========================================================================================
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    header('Location: ../../index.php');
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_NAME'];
$usernameSelect = $_ENV['DB_SELECT_USERNAME'];
$passwordSelect = $_ENV['DB_SELECT_PASSWORD'];
$usernameUpdate = $_ENV['DB_UPDATE_USERNAME'];
$passwordUpdate = $_ENV['DB_UPDATE_PASSWORD'];

//Issue ID
$current_issue = $_GET["id"];

//MAKE SURE ID BELONGS TO THE USER.
////////////////////////////////////

// Create connection
$conn = null;
try {
    $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
}
catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Manage issue</title>
        <link href="../../style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include '../../nav.php'; ?>
        <div class="body-container">
            <button class="goBack" onclick="history.back()">Go Back</button>
            <div>
                <?php

                // Get issue information
                $conn = null;
                try {
                    $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                    $conn->query("SET time_zone = 'Europe/London'");
                    $sql = 'SELECT * FROM issues WHERE issue_id = ?';
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $current_issue);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    $conn->close();

                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        $issue_id = $row['issue_id'];
                        $title = $row['title'];
                        $category = $row['category'];
                        $description = $row['description'];
                        $user_id = $row['user_id'];
                        $admin_uid = $row['admin_uid'];
                        $status = $row['status'];
                        $created_time = $row['created_time'];
                        $last_updated = $row['last_updated'];
                        $is_deleted = $row['is_deleted'];

                        echo '<h1><a href="index.php">Assign Admins</a> >> <span style="color:#6cbcee">' . $title . '</span></h1>';

                        //Make table showing issue information
                        echo '<table class="vertical-table">';
                        echo '<tr><th>Issue ID</th><td>' . $issue_id . '</td></tr>';
                        echo '<tr><th>Title</th><td>' . $title . '</td></tr>';
                        echo '<tr><th>Category</th><td>' . $category . '</td></tr>';
                        echo '<tr><th>Description</th><td>' . $description . '</td></tr>';

                        if ($admin_uid == 0) {
                            echo '<tr><th>Assigned Admin</th><td>Unassigned</td></tr>';
                        } else {
                            //Get username and name
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = 'SELECT * FROM users WHERE user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $admin_uid);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $stmt->close();
                                $conn->close();

                                $row = $result->fetch_assoc();
                                $username = htmlspecialchars($row["username"]);
                                $firstname = htmlspecialchars($row["firstname"]);
                                $surname = htmlspecialchars($row["surname"]);

                                echo '<tr><th>Assigned Admin</th><td><a href="../manage-users/manage.php?id=' . $user_id . '">' . $username . '</a></td></tr>';

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }
                        echo '<tr><th>Status</th><td>' . $status . '</td></tr>';
                        echo '<tr><th>Created Time</th><td>' . $created_time . '</td></tr>';
                        echo '<tr><th>Last Updated</th><td>' . $last_updated . '</td></tr>';
                        echo '<tr><th>Deletion Status</th><td>' . $is_deleted . '</td></tr>';
                        echo '</table><br>';

                        //Get all admin IDs, usernames, firstnames, and surnames.
                        $conn = null;
                        try {
                            $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                            $conn->query("SET time_zone = 'Europe/London'");
                            $sql = 'SELECT * FROM users WHERE admin = 1';
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();
                            $conn->close();

                            echo '<form action="" method="post" target="_self"><label for="adminInput">Assign Admin:</label><select id="adminInput" name="adminInput">';
                            echo '<option value="" disabled selected>Select admin...</option>';
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $username = htmlspecialchars($row["username"]);
                                    $firstname = htmlspecialchars($row["firstname"]);
                                    $surname = htmlspecialchars($row["surname"]);
                                    $uid = htmlspecialchars($row["user_id"]);

                                    echo '<option value="' . $uid . '">' . $username . ' - ' . $firstname . ' ' . $surname . '</option>';
                                }
                            }
                            echo '</select><input type="submit" name="submit" value="Save"></form>';

                            //Save assignment
                            if (isset($_POST['submit'])) {
                                echo '<p>Test</p>';


                                //Update database
                                $conn = null;
                                try {
                                    $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                    $conn->query("SET time_zone = 'Europe/London'");


                                    $sql = 'UPDATE issues SET admin_uid = ?, status = ?, last_updated = CURRENT_TIMESTAMP(6) WHERE issue_id = ?';
                                    $stmt = $conn->prepare($sql);
                                    $new_status = 'Open';
                                    $stmt->bind_param('isi', $_POST['adminInput'], $new_status, $issue_id);
                                    $stmt->execute();
                                    $stmt->close();
                                    $conn->close();
                                    header('Location: index.php');

                                } catch (mysqli_sql_exception $e) {
                                    echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                                }
                            }
                        } catch (mysqli_sql_exception $e) {
                            echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                        }

                    } else {
                        echo '<a href="index.php"><< Back</a><br><br>';
                        echo 'This issue does not exist, or you cannot access it.';
                    }

                } catch (mysqli_sql_exception $e) {
                    echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                }
                ?>

            </div>

            <br>


        </div>

        <?php include '../../footer.php'; ?>
    </body>
    <script src="../../script.js"></script>

</html>

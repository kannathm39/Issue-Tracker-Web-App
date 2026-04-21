<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
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
$usernameDelete = $_ENV['DB_DELETE_USERNAME'];
$passwordDelete = $_ENV['DB_DELETE_PASSWORD'];

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
        <link href="../style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include '../nav.php'; ?>
        <div class="body-container">
            <div>
                <?php

                // Get issue information
                $conn = null;
                try {
                    $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                    $sql = 'SELECT * FROM issues WHERE issue_id = ? AND user_id = ?';
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ii', $current_issue, $_SESSION['user_id']);
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
                        $timestamp = $row['timestamp'];

                        echo '<h1><a href="index.php">My Issues</a> >> <span style="color:#9f89f1">' . $title . '</span></h1>';

                        //Make table showing issue information
                        echo '<table class="vertical-table">';
                        echo '<tr><th>Issue ID</th><td>' . $issue_id . '</td></tr>';
                        echo '<tr><th>Title</th><td>' . $title . '</td></tr>';
                        echo '<tr><th>Category</th><td>' . $category . '</td></tr>';
                        echo '<tr><th>Description</th><td>' . $description . '</td></tr>';
                        echo '<tr><th>Assigned Admin</th><td>' . $admin_uid . '</td></tr>';
                        echo '<tr><th>Status</th><td>' . $status . '</td></tr>';
                        echo '<tr><th>Timestamp</th><td>' . $timestamp . '</td></tr>';
                        echo '</table><br>';

                        //Show edit button
                        echo '<form action="" method="post" target="_self" class="edit-button-form">';
                        echo '<input type="submit" name="edit" value="Edit" class="edit-button"></form>
                        <form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to delete this issue?\nClick OK to confirm.\');">
                        <input type="submit" name="delete" value="Delete" class="delete-button">
                        </form>';

                        //Edit form
                        if (isset($_POST['edit'])) {
                            echo '<p>Done</p>';
                            echo '
                            <form action="" method="post" target="_self" class="form-db">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" value="' . $title . '" minlength="8" maxlength="20" required><br><br>
                                <label for="category">Category</label>
                                <input type="text" id="category" name="category" value="' . $category . '" required><br><br>
                                <label for="description"> Issue Description</label>
                                <textarea id="description" name="description" rows="5" cols="50">' . $description . '</textarea><br><br>
                                <input type="submit" name="submit-edit" value="Submit">
                            </form>';
                        }

                        //Submit edits
                        if (isset($_POST['submit-edit'])) {

                            //Update database
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                

                                $sql = 'UPDATE issues SET title = ?, category = ?, description = ? WHERE issue_id = ? AND user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('sssii', $_POST['title'], $_POST['category'], $_POST['description'], $issue_id, $_SESSION['user_id']);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();
                                header('Location: manage.php?id=' . $issue_id);

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Delete
                        if (isset($_POST['delete'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameDelete, $passwordDelete, $database);
                                $sql = 'DELETE FROM issues WHERE issue_id = ? AND user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('ii', $issue_id, $_SESSION['user_id']);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();
                                header('Location: index.php');

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Show any admin replies
                        echo '<h2>Updates</h2>';

                        //Show add reply option
                        echo '<h2>Add response</h2>';

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

        <?php include '../footer.php'; ?>
    </body>

</html>

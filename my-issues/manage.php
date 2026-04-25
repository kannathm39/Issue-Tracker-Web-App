<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
} else if ($_SESSION['is_approved'] != 1) {
    header('Location: ../approval-notice.php');
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_NAME'];
$usernameSelect = $_ENV['DB_SELECT_USERNAME'];
$passwordSelect = $_ENV['DB_SELECT_PASSWORD'];
$usernameInsert = $_ENV['DB_INSERT_USERNAME'];
$passwordInsert = $_ENV['DB_INSERT_PASSWORD'];
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
                    $conn->query("SET time_zone = 'Europe/London'");
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
                        $created_time = $row['created_time'];
                        $last_updated = $row['last_updated'];

                        echo '<h1><a href="index.php">My Issues</a> >> <span style="color:#6cbcee">' . $title . '</span></h1>';

                        //Make table showing issue information
                        echo '<table class="vertical-table">';
                        echo '<tr><th>Issue ID</th><td>' . $issue_id . '</td></tr>';
                        echo '<tr><th>Title</th><td>' . $title . '</td></tr>';
                        echo '<tr><th>Category</th><td>' . $category . '</td></tr>';
                        echo '<tr><th>Description</th><td>' . $description . '</td></tr>';
                        echo '<tr><th>Assigned Admin</th><td>' . $admin_uid . '</td></tr>';
                        echo '<tr><th>Status</th><td>' . $status . '</td></tr>';
                        echo '<tr><th>Created Time</th><td>' . $created_time . '</td></tr>';
                        echo '<tr><th>Last Updated</th><td>' . $last_updated . '</td></tr>';
                        echo '</table><br>';

                        echo '<div class="issueButtons">';
                        //Show edit button
                        echo '<form action="" method="post" target="_self" class="edit-button-form">';
                        echo '<input type="submit" name="edit" value="Edit" class="edit-button"></form>
                        <form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to delete this issue?\nClick OK to confirm.\');">
                        <input type="submit" name="delete" value="Delete" class="delete-button">
                        </form>';
                        echo '</div>';

                        //Edit form
                        if (isset($_POST['edit'])) {
                            echo '
                            <form action="" method="post" target="_self" class="form-db user-form">
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
                                $conn->query("SET time_zone = 'Europe/London'");

                                $sql = 'UPDATE issues SET title = ?, category = ?, description = ?, last_updated = CURRENT_TIMESTAMP(6) WHERE issue_id = ? AND user_id = ?';
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

                        //Mark as Delete
                        if (isset($_POST['delete'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = 'UPDATE issues SET is_deleted = 1 WHERE issue_id = ? AND user_id = ?';
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

                        //Add response
                        if (isset($_POST['submit-response'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameInsert, $passwordInsert, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $poster_uid = $_SESSION['user_id'];

                                //Escape special characters
                                $response = $conn->real_escape_string($_POST['response']);

                                //Insert response into comments table
                                $sql = "INSERT INTO comments (issue_id, response, user_notif, admin_notif, poster_uid, timestamp) VALUES ('$issue_id', '$response', '0', '1', '$poster_uid', CURRENT_TIMESTAMP(6))";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();

                                //Update issue last_updated timestamp
                                $conn = null;
                                try {
                                    $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                    $conn->query("SET time_zone = 'Europe/London'");
                                    $sql = "UPDATE issues SET last_updated = CURRENT_TIMESTAMP(6) WHERE issue_id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param('i', $issue_id);
                                    $stmt->execute();
                                    $stmt->close();
                                    $conn->close();
                                    echo '<script type="text/javascript"> redirect(\"manage.php?id=\"' . $issue_id . '); </script>';

                                } catch (mysqli_sql_exception $e) {
                                    echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                                }

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Show any responses
                        echo '<h2>Updates</h2>';

                        $conn = null;
                        try {
                            $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                            $conn->query("SET time_zone = 'Europe/London'");
                            $sql = "SELECT * FROM comments WHERE issue_id = ? ORDER BY timestamp ASC";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('i', $issue_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();
                            $conn->close();
                            $checking_new = true;

                            if ($result->num_rows > 0) {
                                echo '<div class="response-container">';
                                while ($row = $result->fetch_assoc()) {
                                    $poster_uid = htmlspecialchars($row['poster_uid']);
                                    $response_timestamp = htmlspecialchars($row['timestamp']);
                                    $user_notif = htmlspecialchars($row['user_notif']);

                                    $conn = null;
                                    try {
                                        $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                                        $conn->query("SET time_zone = 'Europe/London'");
                                        $sql = "SELECT * FROM users WHERE user_id = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param('i', $poster_uid);
                                        $stmt->execute();
                                        $poster_results = $stmt->get_result();
                                        $stmt->close();
                                        $conn->close();

                                        while ($p_row = $poster_results->fetch_assoc()) {
                                            $poster_fname = htmlspecialchars($p_row['firstname']);
                                            $poster_sname = htmlspecialchars($p_row['surname']);
                                            $poster_username = htmlspecialchars($p_row['username']);
                                            $poster_admin = htmlspecialchars($p_row['admin']);
                                        }

                                        //If new response, separate
                                        if ($checking_new = true) {
                                            if ($user_notif == 1) {
                                                echo '<p>--- NEW RESPONSES ---</p>';
                                                $checking_new = false;
                                            }
                                        }

                                        //Check if poster is admin or user
                                        if ($poster_admin == 1) {
                                            echo '<div class="incoming-bubble"><div class="bubble-contents"><p class="bubble-poster">★ ' . $poster_fname . ' ' . $poster_sname . '<span style="font-size: 13px; color:#676767;"> || @' . $poster_username . '</span></p><p>' . $response_timestamp . '</p><p>' . htmlspecialchars($row['response']) . '</p></div></div><br>';
                                        } else {
                                            echo '<div class="outgoing-bubble"><div class="bubble-contents"><p class="bubble-poster">' . $poster_fname . ' ' . $poster_sname . '<span style="font-size: 13px; color:#676767;"> || @' . $poster_username . '</span></p><p>' . $response_timestamp . '</p><p>' . htmlspecialchars($row['response']) . '</p></div></div><br>';
                                        }


                                    } catch (mysqli_sql_exception $e) {
                                        echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                                    }

                                }
                                echo '</div>';
                            } else {
                                echo "<p>No responses yet.</p>";
                            }

                        } catch (mysqli_sql_exception $e) {
                            echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                        }

                        //Set user notif field to 0, as the updates have been seen now
                        $conn = null;
                        try {
                            $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                            $conn->query("SET time_zone = 'Europe/London'");
                            $sql = "UPDATE comments SET user_notif = 0 WHERE issue_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('i', $issue_id);
                            $stmt->execute();
                            $stmt->close();
                            $conn->close();
                        } catch (mysqli_sql_exception $e) {
                            echo '<div role="alert" class="alert">Unable to access responses. Please try again later.</div>';
                        }

                        //Show add reply option
                        echo '<h2>Add response</h2>';
                        echo '
                            <form action="" method="post" target="_self" class="form-db user-form">
                                <label for="response"> Please write your response below:</label>
                                <textarea id="response" name="response" rows="5" cols="50"></textarea><br><br>
                                <input type="submit" name="submit-response" value="Submit">
                            </form>';

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
    <script src="../../script.js"></script>

</html>

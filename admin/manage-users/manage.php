<?php
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
$usernameDelete = $_ENV['DB_DELETE_USERNAME'];
$passwordDelete = $_ENV['DB_DELETE_PASSWORD'];

//User ID
//Issue ID
$current_user = $_GET["id"];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create issue</title>
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
                    $sql = 'SELECT * FROM users WHERE user_id = ?';
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $current_user);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    $conn->close();

                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        $user_id = $row['user_id'];
                        $username = $row['username'];
                        $firstname = $row['firstname'];
                        $surname = $row['surname'];
                        $email = $row['email'];
                        $admin = $row['admin'];
                        $is_approved = $row['is_approved'];
                        $is_deleted = $row['is_deleted'];

                        echo '<h1><a href="index.php">Manage Users</a> >> <span style="color:#6cbcee">' . $username . '</span></h1>';

                        echo '<table class="vertical-table">';
                        echo '<tr><th>User ID</th><td>' . $user_id . '</td></tr>';
                        echo '<tr><th>Username</th><td>' . $username . '</td></tr>';
                        echo '<tr><th>First name</th><td>' . $firstname . '</td></tr>';
                        echo '<tr><th>Surname</th><td>' . $surname . '</td></tr>';
                        echo '<tr><th>Email</th><td>' . $email . '</td></tr>';
                        echo '<tr><th>Admin Status</th><td>' . $admin . '</td></tr>';
                        echo '<tr><th>Approval Status</th><td>' . $is_approved . '</td></tr>';
                        echo '<tr><th>Deletion Status</th><td>' . $is_deleted . '</td></tr>';
                        echo '</table><br>';

                        echo '<div class="manageUserButtons">';

                        //Edit button
                        echo '<form action="" method="post" target="_self" class="edit-button-form">';
                        echo '<input type="submit" name="edit" value="Edit Details" class="edit-button"></form>';

                        //Grant admin
                        echo '<form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to make this user an administrator?\nClick OK to confirm.\');">
                        <input type="submit" name="grant-admin" value="★ Grant admin" class="edit-button">
                        </form>';

                        //Revoke admin
                        echo '<form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to revoke administrative privileges from this user?\nClick OK to confirm.\');">
                        <input type="submit" name="revoke-admin" value="★ Revoke admin" class="edit-button">
                        </form>';

                        //Mark as delete button
                        echo '<form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to mark this user as deleted?\nClick OK to confirm.\');">
                        <input type="submit" name="delete" value="Mark as deleted" class="m-delete-button">
                        </form>';

                        //Mark as active
                        echo '<form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to mark this user as active?\nThey will be able to login and use their account.\nClick OK to confirm.\');">
                        <input type="submit" name="activate" value="Mark as active" class="m-delete-button">
                        </form>';

                        //Permanently delete button
                        echo '<form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to PERMANENTLY delete this user?\nClick OK to confirm.\');">
                        <input type="submit" name="perm-delete" value="⚠ Permanent delete" class="delete-button">
                        </form>';
                        echo '</div>';

                        //Edit form
                        if (isset($_POST['edit'])) {
                            echo '
                            <form action="" method="post" target="_self" class="form-db user-form">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" value="' . $username . '" minlength="8" maxlength="20" required><br><br>
                                <label for="firstname">First name</label>
                                <input type="text" id="firstname" name="firstname" value="' . $firstname . '" required><br><br>
                                <label for="surname">Surname</label>
                                <input type="text" id="surname" name="surname" value="' . $surname . '" required><br><br>
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


                                $sql = 'UPDATE users SET username = ?, firstname = ?, surname = ? WHERE user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('sssi', $_POST['username'], $_POST['firstname'], $_POST['surname'], $user_id);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();

                                header('Location: manage.php?id=' . $user_id);

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Grant admin
                        if (isset($_POST['grant-admin'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = 'UPDATE users SET admin = 1 WHERE user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $user_id);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();
                                header('Location: manage.php?id=' . $user_id);

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Revoke admin
                        if (isset($_POST['revoke-admin'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = 'UPDATE users SET admin = 0 WHERE user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $user_id);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();
                                header('Location: manage.php?id=' . $user_id);

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Mark as deleted
                        if (isset($_POST['delete'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = 'UPDATE users SET is_deleted = 1 WHERE user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $user_id);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();
                                header('Location: manage.php?id=' . $user_id);

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Mark as active
                        if (isset($_POST['activate'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = 'UPDATE users SET is_deleted = 0 WHERE user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $user_id);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();
                                header('Location: manage.php?id=' . $user_id);

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
                        }

                        //Permanently Delete
                        if (isset($_POST['perm-delete'])) {
                            $conn = null;
                            try {
                                $conn = new mysqli($hostname, $usernameDelete, $passwordDelete, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = 'DELETE FROM users WHERE user_id = ?';
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $user_id);
                                $stmt->execute();
                                $stmt->close();
                                $conn->close();
                                header('Location: index.php');

                            } catch (mysqli_sql_exception $e) {
                                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                            }
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

        </div>

        <?php include '../../footer.php'; ?>
    </body>

</html>

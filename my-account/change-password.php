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

//User ID
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$firstname = $_SESSION['firstname'];
$surname = $_SESSION['surname'];
$email = $_SESSION['email'];

$conn = null;
try {
    $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
    $conn->query("SET time_zone = 'Europe/London'");
    $sql = 'SELECT is_approved FROM users WHERE user_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $is_approved = htmlspecialchars($row['is_approved']);
        }
    } else {
        $is_approved = $_SESSION['is_approved'];
    }
} catch (mysqli_sql_exception $e) {
    $is_approved = $_SESSION['is_approved'];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create issue</title>
        <link href="../style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include '../nav.php'; ?>
        <div class="body-container">
            <div>
                <h1>Change Password</h1>
                <?php

                //Edit form
                if (isset($_POST['password']) && isset($_POST['passwordconf']) && isset($_POST['new_password']) && isset($_POST['new_passwordconf'])) {

                    //Check if passwords match
                    if ($_POST['password'] == $_POST['passwordconf']) {
                        //Check if current password is correct
                        $conn = null;
                        try {
                            $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                            $conn->query("SET time_zone = 'Europe/London'");
                            $password = $conn->real_escape_string($_POST['password']);

                            $sql = 'SELECT * FROM users WHERE user_id = ? AND is_deleted = 0';
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('s', $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $stmt->close();
                            $conn->close();

                            if ($result->num_rows == 1) {
                                $row = $result->fetch_assoc();
                                if (password_verify($_POST['password'], $row['password'])) {
                                    //Password is correct
                                    if (isset($_POST['new_password']) && isset($_POST['new_passwordconf'])) {
                                        if ($_POST['new_password'] == $_POST['new_passwordconf']) {
                                            if (strlen($_POST['new_password']) >= 8 && strlen($_POST['new_password']) <= 25) {
                                                if (preg_match('/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}/', $_POST['new_password'])) {
                                                    //Hash password
                                                    $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                                                    $conn = null;
                                                    try {
                                                        $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                                                        $conn->query("SET time_zone = 'Europe/London'");

                                                        $sql = 'UPDATE users SET password = ? WHERE user_id = ?';
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->bind_param('si', $hashed_password, $_SESSION['user_id']);
                                                        $stmt->execute();
                                                        $stmt->close();
                                                        $conn->close();
                                                        header('Location: index.php');

                                                    } catch (mysqli_sql_exception $e) {
                                                        echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                                                    }

                                                } else {
                                                    echo '<div role="alert" class="alert">Your password is invalid - Please make sure you have a minimum of 8 characters, including one uppercase, one lowercase, one number and one special character.</div>';
                                                }
                                            } else {
                                                echo '<div role="alert" class="alert">Password is invalid - Please make sure it is at least 8 characters long.</div>';
                                            }
                                        } else {
                                            echo '<div role="alert" class="alert">Your passwords do not match. Please try again.</div>';
                                        }
                                    }

                                } else {
                                    //Incorrect password
                                    echo '<div role="alert" class="alert">Your current password is incorrect.</div>';
                                }
                            }
                        } catch (mysqli_sql_exception $e) {
                            echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                        }

                    } else {
                        echo '<div role="alert" class="alert">Your passwords do not match. Please try again.</div>';
                    }
                }

                //Ask for current password
                echo '<p>Please enter your current password, and the password you wish to use.</p>';
                echo '
                    <form action="" method="post" target="_self" class="form-db user-form">
                        <label for="password">Current password:</label>
                        <input type="password" id="password" name="password" minlength="8" maxlength="25" title="Enter your current password." required><br><br>
                        <label for="passwordconf">Confirm current password:</label>
                        <input type="password" id="passwordconf" name="passwordconf" minlength="8" maxlength="25" required><br><br>
                        <label for="new_password">New password:</label>
                        <input type="password" id="new_password" name="new_password" minlength="8" maxlength="25" pattern="?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}" title="Password must have a minimum of 12 characters, one uppercase, one lowercase, one digit and one special character." required><br><br>
                        <label for="new_passwordconf">Confirm new password:</label>
                        <input type="password" id="new_passwordconf" name="new_passwordconf" minlength="8" maxlength="25" required><br><br>

                        <input type="submit" name="change-pass" value="Submit">
                    </form>';
                ?>
            </div>

        </div>

        <?php include '../footer.php'; ?>
    </body>

</html>

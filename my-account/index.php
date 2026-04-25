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
                <h1>My Account</h1>
                <?php
                echo '<table class="vertical-table">';
                echo '<tr><th>User ID</th><td>' . $user_id . '</td></tr>';
                echo '<tr><th>Username</th><td>' . $username . '</td></tr>';
                echo '<tr><th>First name</th><td>' . $firstname . '</td></tr>';
                echo '<tr><th>Surname</th><td>' . $surname . '</td></tr>';
                echo '<tr><th>Email</th><td>' . $email . '</td></tr>';
                echo '</table><br>';

                if ($is_approved != 1) {
                    echo '<p style="color:red;">Your account has not been approved yet. Please wait for an administrator to approve your account before using this application.<br>
                        Once you have been approved, you may edit your account details.</p>';
                    echo '<div class="profileButtons">';
                } else {
                    echo '<div class="profileButtons">';
                    //Edit button
                    echo '<form action="" method="post" target="_self" class="edit-button-form">';
                    echo '<input type="submit" name="edit" value="Edit Details" class="edit-button"></form>';

                    //Change password button
                    echo '<form action="" method="post" target="_self" class="edit-button-form">';
                    echo '<input type="submit" name="changepass" value="Change Password" class="edit-button"></form>';
                }

                //Delete button
                echo '<form action="" method="post" target="_self" class="edit-button-form" onsubmit="return confirm(\'Are you sure you want to delete your account?\nClick OK to confirm.\');">
                        <input type="submit" name="delete" value="⚠ Delete Account" class="delete-button">
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
                        $stmt->bind_param('sssi', $_POST['username'], $_POST['firstname'], $_POST['surname'], $_SESSION['user_id']);
                        $stmt->execute();
                        $stmt->close();
                        $conn->close();

                        $_SESSION['username'] = $_POST['username'];
                        $_SESSION['firstname'] = $_POST['firstname'];
                        $_SESSION['surname'] = $_POST['surname'];

                        header('Location: index.php');

                    } catch (mysqli_sql_exception $e) {
                        echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                    }
                }

                //Soft Delete
                if (isset($_POST['delete'])) {
                    $conn = null;
                    try {
                        $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);
                        $conn->query("SET time_zone = 'Europe/London'");
                        $sql = 'UPDATE users SET is_deleted = 1 WHERE user_id = ?';
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $_SESSION['user_id']);
                        $stmt->execute();
                        $stmt->close();
                        $conn->close();
                        header('Location: ../logout.php');

                    } catch (mysqli_sql_exception $e) {
                        echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                    }
                }
                ?>
            </div>

        </div>

        <?php include '../footer.php'; ?>
    </body>

</html>

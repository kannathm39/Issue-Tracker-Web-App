<?php
session_start();
if (isset($_SESSION['user_id'])) {
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
        <title>Login</title>
        <link href="../style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include '../nav.php'; ?>
        <div class="body-container">

            <div class="center-container">
                <h1>Log in</h1>

                <?php
                // Validate registered values

                if (isset($_POST['username']) && isset($_POST['password'])) {
                    $conn = null;
                    try {
                        $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);

                        $username = $conn->real_escape_string($_POST['username']);
                        $password = $conn->real_escape_string($_POST['password']);

                        $sql = 'SELECT * FROM users WHERE username = ? AND is_deleted = 0';
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $username);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $stmt->close();
                        $conn->close();

                        if ($result->num_rows == 1) {

                            $row = $result->fetch_assoc();
                            if (password_verify($_POST['password'], $row['password'])) {
                                //Sessions are set here.
                                echo '<div role="alert" class="g-alert">Logged in successfully! Please wait.</div>';
                                $_SESSION['user_id'] = $row['user_id'];
                                $_SESSION['username'] = $row['username'];
                                $_SESSION['firstname'] = $row['firstname'];
                                $_SESSION['surname'] = $row['surname'];
                                $_SESSION['email'] = $row['email'];
                                $_SESSION['admin'] = $row['admin'];
                                header('Location: ../index.php');
                                exit;
                            } else {
                                //Incorrect password - !<tbc> implement a lockout here too that is a counter added to the database for the user to lock them out until the admin re-approves them - however, this is not an assesssment requirement.
                                echo '<div role="alert" class="alert">The username or password is incorrect.</div>';
                            }

                        } else {
                            echo '<div role="alert" class="alert">The username or password is incorrect.</div>';
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                    }
                }
                ?>
                <br>

                <form action="./index.php" method="post" target="_self" class="form-log">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" maxlength="20" required><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" maxlength="25" required><br><br>

                    <input type="submit" value="Submit">
                </form>

                <br>
                <div>
                    <span>Don't have an account? <a href="http://localhost:9090/signup/index.php">Sign up now.</a></span>
                </div>

            </div>
            <br>

        </div>

        <?php include '../footer.php'; ?>
    </body>

</html>

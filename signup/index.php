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
$usernameInsert = $_ENV['DB_INSERT_USERNAME'];
$passwordInsert = $_ENV['DB_INSERT_PASSWORD'];

//Fake USER ID for now
$user_id = 1;

// Create connection
$conn = null;
try {
    $conn = new mysqli($hostname, $usernameInsert, $passwordInsert, $database);
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
        <title>Sign up</title>
        <link href="../style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include '../nav.php'; ?>
        <div class="body-container">
            <div class="center-container">
                <h1>Sign up</h1>

                <?php
                // Validate registered values
                if (isset($_POST['username']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['passwordconf'])) {

                    //Username length
                    if (strlen($_POST['username']) >= 3 && strlen($_POST['username']) <= 20) {
                        //Firstname length
                        if (strlen($_POST['fname']) >= 1 && strlen($_POST['fname']) <= 20) {
                            //Lastname length
                            if (strlen($_POST['lname']) >= 1 && strlen($_POST['lname']) <= 20) {
                                //Password length
                                if (strlen($_POST['password']) >= 8 && strlen($_POST['password']) <= 25) {
                                    //Password match
                                    if ($_POST['password'] == $_POST['passwordconf']) {
                                        //Password requirements
                                        if (preg_match('/(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}/', $_POST['password'])) {
                                            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                                                //Hash password
                                                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                                                //Insert into database
                                                $conn = null;
                                                try {
                                                    $conn = new mysqli($hostname, $usernameInsert, $passwordInsert, $database);

                                                    $username = $conn->real_escape_string($_POST['username']);
                                                    $firstname = $conn->real_escape_string($_POST['fname']);
                                                    $surname = $conn->real_escape_string($_POST['lname']);
                                                    $email = $conn->real_escape_string($_POST['email']);

                                                    $sql = "INSERT INTO `users` (`user_id`, `username`, `firstname`, `surname`, `email`, `password`, `is_deleted`) VALUES (NULL, '$username', '$firstname', '$surname', '$email', '$hashed_password', '0');";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->execute();
                                                    $stmt->close();
                                                    $conn->close();

                                                    echo '<div role="alert" class="g-alert">Account created successfully!</div>';

                                                } catch (mysqli_sql_exception $e) {
                                                    echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                                                }

                                            } else {
                                                echo '<div role="alert" class="alert">Please enter a valid email address.</div>';
                                            }
                                        } else {
                                            echo '<div role="alert" class="alert">Your password is invalid - Please make sure you have a minimum of 8 characters, including one uppercase, one lowercase, one number and one special character.</div>';
                                        }
                                    } else {
                                        echo '<div role="alert" class="alert">Your passwords do not match.</div>';
                                    }
                                } else {
                                    echo '<div role="alert" class="alert">Password is invalid - Please make sure it is at least 8 characters long.</div>';
                                }
                            } else {
                                echo '<div role="alert" class="alert">Last name is invalid - does not meet minimum requirements.</div>';
                            }
                        } else {
                            echo '<div role="alert" class="alert">First name is invalid - does not meet minimum requirements.</div>';
                        }
                    } else {
                        echo '<div role="alert" class="alert"> Username is invalid - does not meet minimum requirements.</div>';
                    }
                }

                ?>
                <br>

                <form action="./index.php" method="post" target="_self" class="form-log">

                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" minlength="3" maxlength="20" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES) : ''; ?>"><br><br>
                        <label for="fname">First name:</label>
                        <input type="text" id="fname" name="fname" minlength="1" maxlength="20" required value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname'], ENT_QUOTES) : ''; ?>"><br><br>
                        <label for="lname">Last name:</label>
                        <input type="text" id="lname" name="lname" minlength="1" maxlength="20" required value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname'], ENT_QUOTES) : ''; ?>"><br><br>
                        <label for="email">Email address:</label>
                        <input type="email" id="email" name="email" minlength="5" maxlength="50" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : ''; ?>"><br><br>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" minlength="8" maxlength="25" pattern="?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}" title="Password must have a minimum of 12 characters, one uppercase, one lowercase, one digit and one special character." required value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES) : ''; ?>"><br><br>
                        <label for="passwordconf">Confirm password:</label>
                        <input type="password" id="passwordconf" name="passwordconf" minlength="8" maxlength="25" required value="<?php echo isset($_POST['passwordconf']) ? htmlspecialchars($_POST['passwordconf'], ENT_QUOTES) : ''; ?>"><br><br>

                        <input type="submit" value="Submit">

                </form>



                <br>
                <div>
                    <span>Already have an account? <a href="http://localhost:9090/login/index.php">Log in.</a></span>
                </div>
            </div>
            <br>

        </div>



        <?php include '../footer.php'; ?>
    </body>

</html>

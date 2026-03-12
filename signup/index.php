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
                <form action="./index.php" method="post" target="_self">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" minlength="8" maxlength="20" required><br><br>
                    <label for="fname">First name:</label>
                    <input type="text" id="fname" name="fname" minlength="1" maxlength="20" required><br>
                    <label for="lname">Last name:</label>
                    <input type="text" id="lname" name="lname" minlength="1" maxlength="20" required><br><br>
                    <label for="email">Email address:</label>
                    <input type="text" id="email" name="email" minlength="5" maxlength="20" required><br><br>
                    <label for="password">Password:</label>
                    <input type="text" id="password" name="password" minlength="8" maxlength="25" required><br>
                    <label for="passwordconf">Confirm password:</label>
                    <input type="text" id="passwordconf" name="passwordconf" minlength="8" maxlength="25" required>

                    <input type="submit" value="Submit">
                </form>

                <br>
                <div>
                    <span>Already have an account? <a href="http://localhost:9090/login/index.php">Log in.</a></span>
                </div>
            </div>
            <br>

        </div>

        <?php
        // Validate registered values
        //Username length
        if (strlen($_POST['username']) >= 8 && strlen($_POST['username']) <= 20) {
            //Firstname length
            if (strlen($_POST['fname']) >= 1 && strlen($_POST['username']) <= 20) {
                //Lastname length
                if (strlen($_POST['lname']) >= 1 && strlen($_POST['username']) <= 20) {
                    //Password length
                    if (strlen($_POST['password']) >= 8 && strlen($_POST['username']) <= 25) {
                        //Password match
                        if (strlen($_POST['password']) == strlen($_POST['passwordconf'])) {
                            //password length, password match, password requirements
                        } else {
                            echo '<div role="alert">Passwords do not match.</div>';
                        }
                    } else {
                        echo '<div role="alert">Password is invalid - does not meet minimum requirements.</div>';
                    }
                } else {
                    echo '<div role="alert">Last name is invalid - does not meet minimum requirements.</div>';
                }
            } else {
                echo '<div role="alert">First name is invalid - does not meet minimum requirements.</div>';
            }
        } else {
            echo '<div role="alert"> Username is invalid - does not meet minimum requirements.</div>';
        }

        $username = $_POST['username'];
        $firstname = $_POST['fname'];
        $surname = $_POST['lname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_conf = $_POST['passwordconf'];




        echo '<div class="alert alert-primary" role="alert">Username: ' . htmlspecialchars($username) . '</div>';
        ?>

        <?php include '../footer.php'; ?>
    </body>

</html>

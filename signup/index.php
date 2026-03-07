<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sign up</title>
        <link href="../style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <div class="body-container">
            <?php include '../nav.php'; ?>


            <div class="center-container">
                <h1>Sign up</h1>
                <form action="/action_page.php" method="post" target="_blank">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username"><br><br>
                    <label for="fname">First name:</label>
                    <input type="text" id="fname" name="fname"><br>
                    <label for="lname">Last name:</label>
                    <input type="text" id="lname" name="lname"><br><br>
                    <label for="email">Email address:</label>
                    <input type="text" id="email" name="email"><br><br>
                    <label for="password">Password:</label>
                    <input type="text" id="password" name="password"><br>
                    <label for="passwordconf">Confirm password:</label>
                    <input type="text" id="passwordconf" name="passwordconf">

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

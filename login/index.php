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
                <form action="/index.php" method="post" target="_blank" class="form-log">
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

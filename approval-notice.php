<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home</title>
        <link href="/style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include './nav.php'; ?>
        <div class="body-container">

            <h1>Your account has not been approved yet.</h1>
            <p>Please wait for an administrator to approve your account.<br>
                Once you have been approved, you can use this application.
            </p>

        </div>
        <?php include './footer.php'; ?>
    </body>

</html>

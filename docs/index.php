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
        <?php include '../nav.php'; ?>
        <div class="body-container">

            <h1>Documentation</h1>
            <ol>
                <li>Creating an issue</li>
                <li>Viewing your issues</li>
                <li>Editing an issue</li>
                <li>Adding comments</li>
                <li>Deleting an issue</li>
                <li>Profile</li>
                <li>Updating your account details</li>
            </ol>
            <p>This application allows you to log, respond to, categorise, and manage issues.
            <br> Users can log new issues, view previously submitted issues, and add comments to existing issues.<br>
                Admins can respond to users through threads and update the status of issues.
            </p>

        </div>
        <?php include '../footer.php'; ?>
    </body>

</html>

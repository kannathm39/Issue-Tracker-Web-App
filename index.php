<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
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
        <title>Home</title>
        <link href="/style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include './nav.php'; ?>
        <div class="body-container">

            <h1>Home</h1>
            <p>This application allows you to log, respond to, categorise, and manage issues.
            <br> Users can log new issues, view previously submitted issues, and add comments to existing issues.<br>
                Admins can respond to users through threads and update the status of issues.
            </p>

        </div>
        <?php include './footer.php'; ?>
    </body>

</html>

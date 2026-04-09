<?php
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

            <?php
            // SELECT * FROM table and print the result
            $sql = "SELECT * FROM users";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "User ID: " . htmlspecialchars($row["user_id"]) . '<br>';
                    echo "Username: " . htmlspecialchars($row["username"]) . '<br>';
                    echo "Firstname: " . htmlspecialchars($row["firstname"]) . '<br>';
                    echo "Surname: " . htmlspecialchars($row["surname"]) . ' <br>';
                    echo "Email Address: " . htmlspecialchars($row["email"]) . ' <br>';
                    echo "-----------------------------------" . '<br>';
                }
            } else {
                echo "0 results";
            }

            $conn->close();
            ?>

        </div>
        <?php include './footer.php'; ?>
    </body>

</html>

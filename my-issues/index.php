<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_NAME'];
$usernameSelect = $_ENV['DB_SELECT_USERNAME'];
$passwordSelect = $_ENV['DB_SELECT_PASSWORD'];

//Fake USER ID for now
$user_id = 1;

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
        <title>Create issue</title>
        <link href="../style.css" media="all" rel="stylesheet">
    </head>

    <body>
        <?php include '../nav.php'; ?>
        <div class="body-container">
            <div>
                <h1>My Issues</h1>
                <p>View and edit your issues here.</p>
            </div>
            <br>

            <?php
            //Make table headings
            $table_content = "<table>";

            $table_content .= "<tr>";
            $table_content .= "<th>Issue ID</th>";
            $table_content .= "<th>Title</th>";
            $table_content .= "<th>Category</th>";
            $table_content .= "<th>Description</th>";
            $table_content .= "<th>User ID</th>";
            $table_content .= "<th>Assigned Admin</th>";
            $table_content .= "<th>Status</th>";
            $table_content .= "<th>Timestamp</th>";
            $table_content .= "<th></th>";
            $table_content .= "</tr>";

            // SELECT * FROM table and print the result
            $sql = "SELECT * FROM issues";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $table_content .= "<tr>";
                    $table_content .= "<td>" . htmlspecialchars($row["issue_id"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["title"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["category"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["description"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["admin_uid"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["status"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["timestamp"]) . "</td>";
                    $table_content .= "<td><button>Edit</button></td>";
                    $table_content .= "</tr>";
                }
            } else {
                echo "0 results";
            }
            $conn->close();

            $table_content .= "</table>";
            echo $table_content;
            ?>


        </div>

        <?php include '../footer.php'; ?>
    </body>

</html>

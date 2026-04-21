<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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
            <div class="table-container">
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
                $table_content .= "<th>Last Updated</th>";
                $table_content .= "<th>Created Time</th>";
                $table_content .= "<th></th>";
                $table_content .= "</tr>";

                // SELECT * FROM table and print the result
                $conn = null;
                try {
                    $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                    $sql = 'SELECT * FROM issues WHERE user_id = ?';
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    $conn->close();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $table_content .= "<tr>";
                            $table_content .= "<td>" . htmlspecialchars($row["issue_id"]) . "</td>";
                            $table_content .= "<td>" . htmlspecialchars($row["title"]) . "</td>";
                            $table_content .= "<td>" . htmlspecialchars($row["category"]) . "</td>";
                            $table_content .= "<td><div class='truncate'>" . htmlspecialchars($row["description"]) . "</div></td>";
                            $table_content .= "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                            $table_content .= "<td>" . htmlspecialchars($row["admin_uid"]) . "</td>";
                            $table_content .= "<td>" . htmlspecialchars($row["status"]) . "</td>";
                            $table_content .= "<td>" . htmlspecialchars($row["last_updated"]) . "</td>";
                            $table_content .= "<td>" . htmlspecialchars($row["created_time"]) . "</td>";
                            $table_content .= "<td><a href='manage.php?id=" . $row['issue_id'] . "'><button>View & Edit</button></a></td>";
                            $table_content .= "</tr>";
                        }
                    } else {
                        echo "0 results";
                    }

                    $table_content .= "</table><br>";
                    echo $table_content;
                } catch (mysqli_sql_exception $e) {
                    echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                }
                ?>
            </div>


        </div>

        <?php include '../footer.php'; ?>
    </body>

</html>

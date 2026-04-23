<?php
//=========================================================================================
//-------------------------------------ADMIN PAGE------------------------------------------
//=========================================================================================
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    header('Location: ../../index.php');
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_NAME'];
$usernameSelect = $_ENV['DB_SELECT_USERNAME'];
$passwordSelect = $_ENV['DB_SELECT_PASSWORD'];
$usernameUpdate = $_ENV['DB_UPDATE_USERNAME'];
$passwordUpdate = $_ENV['DB_UPDATE_PASSWORD'];
$usernameDelete = $_ENV['DB_DELETE_USERNAME'];
$passwordDelete = $_ENV['DB_DELETE_PASSWORD'];

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
    <link href="../../style.css" media="all" rel="stylesheet">
</head>

<body>
<?php include '../../nav.php'; ?>
<div class="body-container">
    <div>
        <h1>Issue Updates</h1>
        <p>Issues that have been updated or require admin response.</p>
    </div>
    <br>

    <h2>Unassigned Issues</h2>
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
        $table_content .= "<th>Deletion Status</th>";
        $table_content .= "<th></th>";
        $table_content .= "</tr>";

        // SELECT * FROM table and print the result
        $conn = null;
        try {
            $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
            $sql = 'SELECT * FROM issues WHERE admin_uid = 0 ORDER BY last_updated DESC';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $conn->close();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if (htmlspecialchars($row['is_deleted']) == 1) {
                        $table_content .= "<tr class='deleted-row'>";
                    } else {
                        $table_content .= "<tr>";
                    }
                    $table_content .= "<td>" . htmlspecialchars($row["issue_id"]) . "</td>";
                    $table_content .= "<td><b>" . htmlspecialchars($row["title"]) . "</b></td>";
                    $table_content .= "<td>" . htmlspecialchars($row["category"]) . "</td>";
                    $table_content .= "<td><div class='truncate'>" . htmlspecialchars($row["description"]) . "</div></td>";
                    $table_content .= "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                    if (htmlspecialchars($row["admin_uid"]) == 0) {
                        $table_content .= "<td>Unassigned</td>";
                    } else {
                        $table_content .= "<td>" . htmlspecialchars($row["admin_uid"]) . "</td>";
                    }
                    $table_content .= "<td>" . htmlspecialchars($row["status"]) . "</td>";
                    $trimmed_ts = substr(htmlspecialchars($row["last_updated"]), 0, 16);
                    $trim_date = substr($trimmed_ts, 0, 10);
                    $trim_time = substr($trimmed_ts, 10, 6);
                    $table_content .= "<td>" . $trim_date . "<span style='color:#5c62b0;'><b>" . $trim_time . "</b></span></td>";
                    $trimmed_ts = substr(htmlspecialchars($row["created_time"]), 0, 16);
                    $trim_date = substr($trimmed_ts, 0, 10);
                    $trim_time = substr($trimmed_ts, 10, 6);
                    $table_content .= "<td>" . $trim_date . "<span style='color:#8c8c8c;'><b>" . $trim_time . "</b></span></td>";
                    if (htmlspecialchars($row["is_deleted"]) == 1) {
                        $table_content .= "<td style='color:#d11f1f;'>Deleted</td>";
                    } else {
                        $table_content .= "<td>Active</td>";
                    }
                    $table_content .= "<td><a href='manage.php?id=" . $row['issue_id'] . "'><button>Assign Admin</button></a></td>";
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

        //Approve Account
        if (isset($_POST['approve'])) {

            //Update database
            $conn = null;
            try {
                $conn = new mysqli($hostname, $usernameUpdate, $passwordUpdate, $database);

                $sql = 'UPDATE users SET is_approved = 1 WHERE user_id = ?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $_POST['user_id']);
                $stmt->execute();
                $stmt->close();
                $conn->close();

            } catch (mysqli_sql_exception $e) {
                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
            }
            header('Location: index.php');

        }

        //Permanently Delete Account
        if (isset($_POST['delete'])) {

            //Update database
            $conn = null;
            try {
                $conn = new mysqli($hostname, $usernameDelete, $passwordDelete, $database);

                $sql = 'DELETE FROM users WHERE user_id = ?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $_POST['user_id']);
                $stmt->execute();
                $stmt->close();
                $conn->close();


            } catch (mysqli_sql_exception $e) {
                echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
            }
            header('Location: index.php');

        }
        ?>
    </div>

    <h2>Updated Issues</h2>


</div>

<?php include '../../footer.php'; ?>

</body>

</html>

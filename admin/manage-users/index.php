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
    <link href="../../style.css" media="all" rel="stylesheet">
</head>

<body>
<?php include '../../nav.php'; ?>
<div class="body-container">
    <div>
        <h1>User</h1>
        <p>View and manage all users.</p>
    </div>
    <br>
    <div class="table-container">
        <?php
        //Make table headings
        $table_content = "<table>";

        $table_content .= "<tr>";
        $table_content .= "<th>User ID</th>";
        $table_content .= "<th>Username</th>";
        $table_content .= "<th>First name</th>";
        $table_content .= "<th>Surname</th>";
        $table_content .= "<th>Email</th>";
        $table_content .= "<th>Admin Status</th>";
        $table_content .= "<th>Approval Status</th>";
        $table_content .= "<th>Deletion Status</th>";
        $table_content .= "<th></th>";
        $table_content .= "</tr>";

        // SELECT * FROM table and print the result
        $conn = null;
        try {
            $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
            $conn->query("SET time_zone = 'Europe/London'");
            $sql = 'SELECT * FROM users';
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
                    $table_content .= "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                    $table_content .= "<td><b>" . htmlspecialchars($row["username"]) . "</b></td>";
                    $table_content .= "<td>" . htmlspecialchars($row["firstname"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["surname"]) . "</td>";
                    $table_content .= "<td>" . htmlspecialchars($row["email"]) . "</td>";

                    if (htmlspecialchars($row["admin"]) == "1") {
                        $table_content .= "<td style='color:#7300ff;'><b>Admin</b></td>";
                    } else {
                        $table_content .= "<td>User</td>";
                    }

                    if (htmlspecialchars($row["is_approved"]) == "1") {
                        $table_content .= "<td><b>Approved</b></td>";
                    } else {
                        $table_content .= "<td style='color:#0788ce;'>Awaiting Approval</td>";
                    }

                    if (htmlspecialchars($row["is_deleted"]) == "1") {
                        $table_content .= "<td style='color:#d11f1f;'>Deleted</td>";
                    } else {
                        $table_content .= "<td>Active</td>";
                    }

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

<?php include '../../footer.php'; ?>

</body>

</html>

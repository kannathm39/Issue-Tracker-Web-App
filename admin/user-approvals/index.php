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
        <h1>Pending User Approvals</h1>
        <p>All user accounts that need to be approved or denied.</p>
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
        $table_content .= "<th>Deletion Status</th>";
        $table_content .= "<th></th>";
        $table_content .= "<th></th>";
        $table_content .= "</tr>";

        // SELECT * FROM table and print the result
        $conn = null;
        try {
            $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
            $sql = 'SELECT * FROM users WHERE is_approved != 1';
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

                    if (htmlspecialchars($row["is_deleted"]) == "1") {
                        $table_content .= "<td style='color:#d11f1f;'>Deleted</td>";
                    } else {
                        $table_content .= "<td>Active</td>";
                    }

                    $table_content .= '<td>
                        <form action="" method="post" target="_self">
                        <input type="hidden" name="user_id" value="' . htmlspecialchars($row["user_id"]) . '">
                        <input type="submit" name="approve" value="Approve" class="approve-button" style="width:100%;">
                        </form></td>';
                    $table_content .= '<td>
                        <form action="" method="post" target="_self">
                        <input type="hidden" name="user_id" value="' . htmlspecialchars($row["user_id"]) . '">
                        <input type="submit" name="delete" value="Delete" class="delete-button" style="width:100%;">
                        </form></td>';
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


</div>

<?php include '../../footer.php'; ?>

</body>

</html>

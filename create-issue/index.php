<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
} else if ($_SESSION['is_approved'] != 1) {
    header('Location: ../approval-notice.php');
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_NAME'];
$usernameSelect = $_ENV['DB_SELECT_USERNAME'];
$passwordSelect = $_ENV['DB_SELECT_PASSWORD'];
$usernameInsert = $_ENV['DB_INSERT_USERNAME'];
$passwordInsert = $_ENV['DB_INSERT_PASSWORD'];

//User ID
$user_id = $_SESSION['user_id'];

// Create connection
$conn = null;
try {
    $conn = new mysqli($hostname, $usernameInsert, $passwordInsert, $database);
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
                <h1>Create Issue</h1>
                <form action="" method="post" target="_self" class="form-db">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" minlength="8" maxlength="20" required><br><br>
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required><br><br>
                    <label for="description"> Issue Description</label>
                    <textarea id="description" name="description" rows="5" cols="50"></textarea><br><br>
                    <input type="submit" name="submit" value="Submit">
                </form>
            </div>
            <br>

            <!--Inserting issue into issue table-->
            <?php
            if(isset($_POST['submit'])) {
                $conn = null;
                try {
                    $conn = new mysqli($hostname, $usernameInsert, $passwordInsert, $database);

                    //Escape special characters
                    $title = $conn->real_escape_string($_POST['title']);
                    $category = $conn->real_escape_string($_POST['category']);
                    $description = $conn->real_escape_string($_POST['description']);

                    $sql = "INSERT INTO issues (issue_id, title, category, description, user_id, admin_uid, status, created_time, last_updated) VALUES (NULL, '$title', '$category', '$description', '$user_id', '000000', 'Ongoing', CURRENT_TIMESTAMP(6), CURRENT_TIMESTAMP(6));";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $stmt->close();
                    $conn->close();
                    echo "New record created successfully!";

                } catch (mysqli_sql_exception $e) {
                    echo '<div role="alert" class="alert">Something went wrong. Please try again later.</div>';
                }
            }
            ?>

        </div>

        <?php include '../footer.php'; ?>
    </body>

</html>

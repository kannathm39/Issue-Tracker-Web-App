<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_NAME'];
$usernameSelect = $_ENV['DB_INSERT_USERNAME'];
$passwordSelect = $_ENV['DB_INSERT_PASSWORD'];

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
            $issuetitle = "Test title";
            $issuecategory = "Test category";
            $issuedesc = "Test description";

            //Get values we want to insert
            $issuetitle = $_POST['title'];
            $issuecategory = $_POST['category'];
            $issuedesc = $_POST['description'];

            echo "Is this working?";
            if(isset($_POST['submit'])) {
                $sql = "INSERT INTO `issues` (`issue_id`, `title`, `category`, `description`, `user_id`, `admin_uid`, `status`, `timestamp`) VALUES (NULL, '$issuetitle', '$issuecategory', '$issuedesc', '$user_id', '000000', 'Ongoing', CURRENT_TIMESTAMP(6));";
                //$sql = "INSERT INTO issues (title, category, description, user_id) VALUES ('Test Title', 'Test Category', 'Test Description', '111111')";

                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully!";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

            }

            $conn->close();
            ?>

        </div>

        <?php include '../footer.php'; ?>
    </body>

</html>

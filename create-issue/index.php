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
                <form action="./index.php" method="post" target="_self">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" minlength="8" maxlength="20" required><br><br>
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required><br><br>
                    <label for="description"> Issue Description</label>
                    <input type="text" id="description" name="description" required><br><br>
                    <input type="submit" value="Submit">
                </form>
            </div>
            <br>

        </div>
        <?php include '../footer.php'; ?>
    </body>

</html>

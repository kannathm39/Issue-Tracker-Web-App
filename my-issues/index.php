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
            <!--Search and filter-->
            <div class="searchFilters">
                <input type="text" id="issueidInput" onkeyup="searchIssueTable()" placeholder="Search Issue IDs..." title="Search Issue ID">
                <input type="text" id="titleInput" onkeyup="searchIssueTable()" placeholder="Search Titles..." title="Search Title">
                <form action="" method="post" target="">
                    <select id="categoryInput" name="category" onchange="searchIssueTable()">
                        <option value="" disabled selected>Search Category...</option>
                        <option value="">All</option>
                        <option value="Software Issue">Software Issue</option>
                        <option value="Hardware Issue">Hardware Issue</option>
                        <option value="General IT Issue">General IT Issue</option>
                        <option value="Request">Request</option>
                        <option value="Other">Other</option>
                    </select>
                </form>
                <input type="text" id="descInput" onkeyup="searchIssueTable()" placeholder="Search Descriptions..." title="Search Description">
                <input type="text" id="adminInput" onkeyup="searchIssueTable()" placeholder="Search Assigned Admins..." title="Search Admin">
                <form action="" method="post" target="">
                    <select id="statusInput" name="status" onchange="searchIssueTable()">
                        <option value="" disabled selected>Search Status...</option>
                        <option value="">All</option>
                        <option value="Awaiting Admin">Awaiting Admin</option>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Awaiting User">Awaiting User</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                    </select>
                </form>
            </div>

            <div class="table-container">
                <?php
                //Make table headings
                $table_content = "<table id='issue_table'>";

                $table_content .= "<tr>";
                $table_content .= "<th>Issue ID</th>";
                $table_content .= "<th>Title</th>";
                $table_content .= "<th>Category</th>";
                $table_content .= "<th>Description</th>";
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
                    $conn->query("SET time_zone = 'Europe/London'");
                    $sql = 'SELECT * FROM issues WHERE user_id = ? AND is_deleted = 0 ORDER BY last_updated DESC';
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
                            $table_content .= "<td><b>" . htmlspecialchars($row["title"]) . "</b></td>";
                            $table_content .= "<td>" . htmlspecialchars($row["category"]) . "</td>";
                            $table_content .= "<td><div class='truncate'>" . htmlspecialchars($row["description"]) . "</div></td>";
                            if (htmlspecialchars($row["admin_uid"]) == 0) {
                                $table_content .= "<td>Unassigned</td>";
                            } else {
                                $table_content .= "<td>" . htmlspecialchars($row["admin_uid"]) . "</td>";
                            }

                            // Get update notifications
                            $conn = null;
                            $issue_id = htmlspecialchars($row['issue_id']);
                            try {
                                $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                                $conn->query("SET time_zone = 'Europe/London'");
                                $sql = "SELECT * FROM comments WHERE issue_id = ? AND user_notif = 1";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param('i', $issue_id);
                                $stmt->execute();
                                $updates_result = $stmt->get_result();
                                $stmt->close();
                                $conn->close();

                                if ($updates_result->num_rows > 0) {
                                    $notif_updates = $updates_result->num_rows;
                                    if ($notif_updates > 1) {
                                        $table_content .= "<td class='highlight-cell'>" . htmlspecialchars($row["status"]) . "<br><span class='highlight'>" . $notif_updates . " updates!</span></td>";
                                    } else if ($notif_updates = 1) {
                                        $table_content .= "<td class='highlight-cell'>" . htmlspecialchars($row["status"]) . "<br><span class='highlight'>" . $notif_updates . " update!</span></td>";
                                    }
                                } else {
                                    $table_content .= "<td>" . htmlspecialchars($row["status"]) . "</td>";
                                }


                            } catch (mysqli_sql_exception $e) {
                                $table_content .= "<td>" . htmlspecialchars($row["status"]) . "</td>";
                            }
                            $trimmed_ts = substr(htmlspecialchars($row["last_updated"]), 0, 16);
                            $trim_date = substr($trimmed_ts, 0, 10);
                            $trim_time = substr($trimmed_ts, 10, 6);
                            $table_content .= "<td>" . $trim_date . "<span style='color:#5c62b0;'><b>" . $trim_time . "</b></span></td>";
                            $trimmed_ts = substr(htmlspecialchars($row["created_time"]), 0, 16);
                            $trim_date = substr($trimmed_ts, 0, 10);
                            $trim_time = substr($trimmed_ts, 10, 6);
                            $table_content .= "<td>" . $trim_date . "<span style='color:#8c8c8c;'><b>" . $trim_time . "</b></span></td>";
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
    <script>
        //Search issue table
        function searchIssueTable() {
            var inputIssueID, inputTitle, inputCategory, inputDesc, inputAdmin, inputStatus,
                table, tr, td, i,
                filterIssue, filterTitle, filterCategory, filterDesc, filterAdmin, filterStatus;

            inputIssueID = document.getElementById("issueidInput");
            inputTitle = document.getElementById("titleInput")
            inputCategory = document.getElementById("categoryInput");
            inputDesc = document.getElementById("descInput");
            inputAdmin = document.getElementById("adminInput");
            inputStatus = document.getElementById("statusInput");

            filterIssue = inputIssueID.value.toUpperCase();
            filterTitle = inputTitle.value.toUpperCase();
            filterCategory = inputCategory.value.toUpperCase();
            filterDesc = inputDesc.value.toUpperCase();
            filterAdmin = inputAdmin.value.toUpperCase();
            filterStatus = inputStatus.value.toUpperCase();

            table = document.getElementById("issue_table");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                td1 = tr[i].getElementsByTagName("td")[1];
                td2 = tr[i].getElementsByTagName("td")[2];
                td3 = tr[i].getElementsByTagName("td")[3];
                td4 = tr[i].getElementsByTagName("td")[4];
                td5 = tr[i].getElementsByTagName("td")[5];

                if (td && td1 && td2 && td3 && td4 && td5) {
                    issue = (td.textContent || td.innerText).toUpperCase();
                    title = (td1.textContent || td1.innerText).toUpperCase();
                    cat = (td2.textContent || td2.innerText).toUpperCase();
                    desc = (td3.textContent || td3.innerText).toUpperCase();
                    admin = (td4.textContent || td4.innerText).toUpperCase();
                    stat = (td5.textContent || td5.innerText).toUpperCase();

                    if (
                        issue.indexOf(filterIssue) > -1 &&
                        title.indexOf(filterTitle) > -1 &&
                        cat.indexOf(filterCategory) > -1 &&
                        desc.indexOf(filterDesc) > -1 &&
                        admin.indexOf(filterAdmin) > -1 &&
                        stat.indexOf(filterStatus) > -1
                    ) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }

            }
        }

    </script>

</html>

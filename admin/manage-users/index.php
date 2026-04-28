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
    <button class="goBack" onclick="history.back()">Go Back</button>
    <div>
        <h1>Manage Users</h1>
        <p>View and manage all users.</p>
    </div>
    <br>

    <div class="searchFilters">
        <input type="text" id="useridInput" onkeyup="searchUserTable()" placeholder="Search User IDs..." title="Search User ID">
        <input type="text" id="usernameInput" onkeyup="searchUserTable()" placeholder="Search Usernames..." title="Search Username">
        <input type="text" id="firstnameInput" onkeyup="searchUserTable()" placeholder="Search First names..." title="Search Firstname">
        <input type="text" id="surnameInput" onkeyup="searchUserTable()" placeholder="Search Surnames..." title="Search Surname">
        <input type="text" id="emailInput" onkeyup="searchUserTable()" placeholder="Search Emails..." title="Search Email">
        <form action="" method="post" target="">
            <select id="adminInput" name="admins" onchange="searchUserTable()">
                <option value="" disabled selected>Search Admin Status...</option>
                <option value="">All</option>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
            </select>
        </form>
        <form action="" method="post" target="">
            <select id="approvalInput" name="approval" onchange="searchUserTable()">
                <option value="" disabled selected>Search Approval Status...</option>
                <option value="">All</option>
                <option value="Approved">Approved</option>
                <option value="Awaiting">Awaiting Approval</option>
            </select>
        </form>
        <form action="" method="post" target="">
            <select id="deletionInput" name="deletion" onchange="searchUserTable()">
                <option value="" disabled selected>Search Deletion Status...</option>
                <option value="">All</option>
                <option value="Active">Active</option>
                <option value="Deleted">Deleted</option>
            </select>
        </form>
    </div>

    <div class="table-container">
        <?php
        //Make table headings
        $table_content = "<table id='user_table'>";

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

                    $table_content .= "<td><a href='manage.php?id=" . htmlspecialchars($row['user_id']) . "'><button>View & Edit</button></a></td>";
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

<script>
    //Search user table
    function searchUserTable() {
        var inputUserID, inputUsername, inputFirstname, inputSurname, inputEmail, inputAdmin, inputApproval, inputDel,
        table, tr, td, i,
        filterUserID, filterUsername, filterFirstname, filterSurname, filterEmail, filterAdmin, filterApproval, filterDel;

        inputUserID = document.getElementById("useridInput");
        inputUsername = document.getElementById("usernameInput")
        inputFirstname = document.getElementById("firstnameInput");
        inputSurname = document.getElementById("surnameInput");
        inputEmail = document.getElementById("emailInput");
        inputAdmin = document.getElementById("adminInput");
        inputApproval = document.getElementById("approvalInput");
        inputDel = document.getElementById("deletionInput");

        filterUserID = inputUserID.value.toUpperCase();
        filterUsername = inputUsername.value.toUpperCase();
        filterFirstname = inputFirstname.value.toUpperCase();
        filterSurname = inputSurname.value.toUpperCase();
        filterEmail = inputEmail.value.toUpperCase();
        filterAdmin = inputAdmin.value.toUpperCase();
        filterApproval = inputApproval.value.toUpperCase();
        filterDel = inputDel.value.toUpperCase();

        table = document.getElementById("user_table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];

            if (td && td1 && td2 && td3 && td4 && td5 && td6 && td7) {
                userid = (td.textContent || td.innerText).toUpperCase();
                username = (td1.textContent || td1.innerText).toUpperCase();
                firstname = (td2.textContent || td2.innerText).toUpperCase();
                surname = (td3.textContent || td3.innerText).toUpperCase();
                email = (td4.textContent || td4.innerText).toUpperCase();
                admin = (td5.textContent || td5.innerText).toUpperCase();
                approval = (td6.textContent || td6.innerText).toUpperCase();
                del = (td7.textContent || td7.innerText).toUpperCase();

                if (
                    userid.indexOf(filterUserID) > -1 &&
                    username.indexOf(filterUsername) > -1 &&
                    firstname.indexOf(filterFirstname) > -1 &&
                    surname.indexOf(filterSurname) > -1 &&
                    email.indexOf(filterEmail) > -1 &&
                    admin.indexOf(filterAdmin) > -1 &&
                    approval.indexOf(filterApproval) > -1 &&
                    del.indexOf(filterDel) > -1
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

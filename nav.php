<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$database = $_ENV['DB_NAME'];
$usernameSelect = $_ENV['DB_SELECT_USERNAME'];
$passwordSelect = $_ENV['DB_SELECT_PASSWORD'];
?>

<nav>
    <link rel="icon" type="image/x-icon" href="/assets/favicon-new.png">
    <ul class="nav-bar">
        <li><a href="http://localhost:9090/index.php">Home</a></li>
        <?php
        if (isset($_SESSION['user_id']) && $_SESSION['admin'] != 1) {
            echo '<li><a href="http://localhost:9090/create-issue/index.php">Create Issue</a></li>';

            //Get updates
            $conn = null;
            try {
                $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                $conn->query("SET time_zone = 'Europe/London'");
                $sql = "SELECT * FROM comments WHERE user_notif = 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $updates_result = $stmt->get_result();
                $stmt->close();
                $conn->close();

                $notif_updates = $updates_result->num_rows;
                if ($notif_updates != 0) {
                    echo '<li><a href="http://localhost:9090/my-issues/index.php"><span class="notif-circle">' . $notif_updates . '</span> My Issues</a></li>';
                } else {
                    echo '<li><a href="http://localhost:9090/my-issues/index.php">My Issues</a></li>';
                }

            } catch (mysqli_sql_exception $e) {
                echo '<li><a href="http://localhost:9090/my-issues/index.php">My Issues</a></li>';
            }
        } else if (isset($_SESSION['user_id']) && $_SESSION['admin'] == 1) {

            $conn = null;
            //Get number of issues for this admin
            try {
                $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                $conn->query("SET time_zone = 'Europe/London'");
                $sql = 'SELECT * FROM issues WHERE admin_uid = ? ORDER BY last_updated DESC';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $conn->close();

                if ($result->num_rows > 0) {
                    $notif_updates = 0;
                    //How many comments for this issue?
                    while ($row = $result->fetch_assoc()) {
                        $conn = null;
                        $issue_id = htmlspecialchars($row['issue_id']);
                        try {
                            $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                            $conn->query("SET time_zone = 'Europe/London'");
                            $sql = "SELECT * FROM comments WHERE issue_id = ? AND admin_notif = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('i', $issue_id);
                            $stmt->execute();
                            $updates_result = $stmt->get_result();
                            $stmt->close();
                            $conn->close();

                            if ($updates_result->num_rows > 0) {
                                $notif_updates = $notif_updates + $updates_result->num_rows;
                            }
                        } catch (mysqli_sql_exception $e) {
                        }
                    }
                    if ($notif_updates > 0) {
                        echo '<li><a href="http://localhost:9090/admin/my-issues/index.php"><span class="notif-circle">' . $notif_updates . '</span> My Issues</a></li>';
                    } else {
                        echo '<li><a href="http://localhost:9090/admin/my-issues/index.php">My Issues</a></li>';
                    }

                }
            } catch (mysqli_sql_exception $e) {
                echo '<li><a href="http://localhost:9090/admin/my-issues/index.php">My Issues</a></li>';
            }
            echo '<li><a href="http://localhost:9090/admin/view-issues/index.php">All Issues</a></li>';

            //Get updates
            $conn = null;
            try {
                $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                $conn->query("SET time_zone = 'Europe/London'");
                $sql = 'SELECT * FROM issues WHERE admin_uid = 0';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $conn->close();

                $notif_updates = $result->num_rows;

                if ($notif_updates != 0) {
                    echo '<li><a href="http://localhost:9090/admin/new-issues/index.php"><span class="notif-circle">' . $notif_updates . '</span> New</a></li>';
                } else {
                    echo '<li><a href="http://localhost:9090/admin/new-issues/index.php">New</a></li>';
                }
            } catch (mysqli_sql_exception $e) {
                echo '<li><a href="http://localhost:9090/admin/new-issues/index.php">New</a></li>';
            }

            echo '<li><a href="http://localhost:9090/admin/manage-users/index.php">Manage Users</a></li>';

            //Get approvals
            $conn = null;
            try {
                $conn = new mysqli($hostname, $usernameSelect, $passwordSelect, $database);
                $conn->query("SET time_zone = 'Europe/London'");
                $sql = 'SELECT * FROM users WHERE is_approved != 1';
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $conn->close();

                $notif_approve = $result->num_rows;
                if ($notif_approve != 0) {
                    echo '<li><a href="http://localhost:9090/admin/user-approvals/index.php"><span class="notif-circle">' . $notif_approve . '</span> User Approvals</a></li>';
                } else {
                    echo '<li><a href="http://localhost:9090/admin/user-approvals/index.php">User Approvals</a></li>';
                }

            } catch (mysqli_sql_exception $e) {
                echo '<li><a href="http://localhost:9090/admin/user-approvals/index.php">User Approvals</a></li>';
            }
        }
        ?>
        <li><a href="http://localhost:9090/docs/index.php">Documentation</a></li>

        <li class="dropdown" style="float:right">
            <?php

            if (isset($_SESSION['user_id']) AND $_SESSION['admin'] == 1) {
                    echo '<a href="javascript:void(0)" class="dropbtn"><span style="font-size:17px;">Administrator Account</span> ★ ' . $_SESSION['firstname'] . '</a>';
            } else if (isset($_SESSION['user_id'])) {
                echo '<a href="javascript:void(0)" class="dropbtn">' . $_SESSION['firstname'] . '</a>';
            } else {
                echo '<a href="javascript:void(0)" class="dropbtn">Account</a>';
            }
            ?>
            <div class="dropdown-content">
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<a href="http://localhost:9090/my-account/index.php">My Account</a>';
                    echo '<a href="#">Settings</a>';
                    echo '<a href="http://localhost:9090/logout.php">Logout</a>';
                } else {
                    echo '<a href="http://localhost:9090/login/index.php">Login</a>';
                    echo '<a href="http://localhost:9090/signup/index.php">Sign up</a>';
                }
                ?>
            </div>
        </li>
    </ul>
</nav>
<nav>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.png">
    <ul class="nav-bar">
        <li><a href="http://localhost:9090/index.php">Home</a></li>
        <?php
        if (isset($_SESSION['user_id']) && $_SESSION['admin'] != 1) {
            echo '<li><a href="http://localhost:9090/create-issue/index.php">Create Issue</a></li>';
            echo '<li><a href="http://localhost:9090/my-issues/index.php">My Issues</a></li>';
        } else if (isset($_SESSION['user_id']) && $_SESSION['admin'] == 1) {
            echo '<li><a href="http://localhost:9090/admin/view-issues/index.php">View Issues</a></li>';
            echo '<li><a href="#">Manage Users</a></li>';
            echo '<li><a href="#">User Approvals</a></li>';
        }
        ?>
        <li><a href="http://localhost:9090/docs/index.php">Documentation</a></li>

        <li class="dropdown" style="float:right">
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<a href="javascript:void(0)" class="dropbtn">' . $_SESSION['firstname'] . '</a>';
            } else {
                echo '<a href="javascript:void(0)" class="dropbtn">Account</a>';
            }
            ?>
            <div class="dropdown-content">
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<a href="#">My Account</a>';
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
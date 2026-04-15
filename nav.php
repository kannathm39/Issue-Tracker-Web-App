<nav>
    <ul class="nav-bar">
        <li><a href="http://localhost:9090/index.php">Home</a></li>
        <li><a href="http://localhost:9090/create-issue/index.php">Create Issue</a></li>
        <li><a href="http://localhost:9090/my-issues/index.php">My Issues</a></li>
        <li><a href="http://localhost:9090/docs/index.php">Documentation</a></li>
        <!--<li><a href="http://localhost:9090/faq/index.php">FAQ</a></li>-->
        <li class="dropdown" style="float:right">
            <a href="javascript:void(0)" class="dropbtn">Account</a>
            <div class="dropdown-content">
                <?php
                $loggedIn = false;
                if ($loggedIn) {
                    echo '<a href="#">My Account</a>';
                    echo '<a href="#">Logout</a>';
                } else {
                    echo '<a href="http://localhost:9090/login/index.php">Login</a>';
                }
                ?>
            </div>
        </li>
    </ul>
    <?php
    if ($loggedIn) {
        echo '<ul class="issue-menu">';
        echo '<li><a href="http://localhost:9090/create-issue/index.php">Create issue</a></li>';
        echo '<li><a href="#">My issues</a></li>';
        echo '</ul>';
    }
    ?>
</nav>
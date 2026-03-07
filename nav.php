<nav>
    <ul>
        <li><a href="http://localhost:9090/index.php">Home</a></li>
        <li><a href="#news">News</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="#docs">Documentation</a></li>
        <li><a href="#faq">FAQ</a></li>
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
</nav>
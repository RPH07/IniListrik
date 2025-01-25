<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>
    <ul>
        <li class="logo"><a href="index.php">IniListrik</a></li>
        <li><a href="login.php"><button>Login</button></a></li>
    </ul>
</nav>
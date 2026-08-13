<?php
    function login_page(): string {
        return '
        <form action="welcome.php" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <input type="submit">
            <p>New here? <a href="register">Register now!</a></p>
        </form>
        ';
    }
?>

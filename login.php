<?php
    function login(): string {
        return '
        <form action="welcome.php" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <input type="submit">
            <p>New here? <a href="register">Register now!</a></p>
        </form>
        ';
    }

    function register(): string {
        // when the submit button is pressed, it should send a post request to 
        return '
        <form id="register-form" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <input type="submit" value="Register">
        ';
    }
?>

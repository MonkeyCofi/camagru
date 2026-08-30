<?php
    function login(): string {
        return '
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <form class="input-form" action="welcome.php" method="post">
                <input type="text" name="username" placeholder="Username">
                <input type="password" name="password" placeholder="Password">
                <input type="submit">
                <p>New here? <a href="register">Register now!</a></p>
            </form>
        </div>
        ';
    }
?>

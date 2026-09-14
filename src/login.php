<?php
    function login(PDO $pdo, array $body): string {
        $pdo->beginTransaction();
        try {
            $statement = $pdo->prepare('SELECT UserId, username, pass, email FROM users where username = ?');
            $statement->execute([$body['username']]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $pdo->rollBack();
            die("
            <h1>
                Error
            </h1><br>
            <p>" . $e . "</p>");
        }
        if (!$result) {
            return "<p>No user found with that username</p>";
        }
        if (!password_verify($body['password'], $result['pass'])) {
            return "403 forbidden";
        }
        $pdo->commit();
        $_SESSION['user_id'] = $result['UserId'];
        $_SESSION['username'] = $result['username'];
        return "Successfully logged in";
        // header("Location: /gallery");
    }

    function logout(): string {
        session_destroy();
        unset($_SESSION['user_id'], $_SESSION['username']);
        return "Logged out successfully";
        // header("Location: /login");
    }

    function login_page(): string {
        if (isset($_SESSION['user_id']))
            return "<p>Already logged in</p>";
        return '
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <form class="input-form" method="post">
                <input type="text" name="username" placeholder="Username">
                <input type="password" name="password" placeholder="Password">
                <input type="submit">
                <p>New here? <a href="register">Register now!</a></p>
            </form>
        </div>
        ';
    }
?>

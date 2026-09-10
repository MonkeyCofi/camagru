<?php
    function login(PDO $pdo, array $body) {
        $pdo->beginTransaction();
        try {
            $statement = $pdo->prepare('SELECT UserId, username, pass, email FROM users where username = ?');
            $statement->execute([$body['username']]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            die("
            <h1>
                Error
            </h1><br>
            <p>" . $e . "</p>");
        }
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($body['password'], $result['pass'])) {

            // return "403 forbidden";
        }
        $pdo->commit();
        print_r($result);
        $_SESSION['user_id'] = $result['UserId'];
        $_SESSION['username'] = $result['username'];
        header("Location: /gallery");
    }

    function logout() {
        session_destroy();
        header("Location: /login");
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

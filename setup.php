<?php
    $servername = "127.0.0.1";
    $dsn = "mysql:host=$servername;port=3306;";
    $username = "root";
    $password = "password";

    try {
        $conn = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        die("
        <h1>
            Error
        </h1><br>
        <p>" . $e . "</p>");
        // die("<h1>Error: " . $e . "</h1>");
    }
?>
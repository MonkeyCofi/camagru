<?php
    $servername = "127.0.0.1";
    $port = "3306";
    $dsn = "mysql:host=$servername;dbname=camagru;port=3306;";
    $username = "pipolint";
    $password = "camagru@2026";

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
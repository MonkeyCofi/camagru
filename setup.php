<?php
    $servername = 'localhost';
    $username = 'pipolint';
    $password = '';

    try {
        $conn = new PDO("mysql:host=$servername", $username, $password);
        if ($conn->connection_error) {
            echo "Connection failed";
        } else {
            echo "Connection success";
        }
    } catch (PDOException $e) {
        die("Error: " . $e);
    }
?>
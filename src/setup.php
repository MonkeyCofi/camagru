<?php

    function setup_pdo(): PDO {
        $servername = "db";
        $port = "3306";
        $username = getenv('DB_USERNAME');
        $db_name = getenv('DB_NAME');
        $password = getenv('DB_USER_PASSWORD');
        $dsn = "mysql:host=$servername;dbname=camagru;port=$port;";
        $pdo = null;
        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            // create the table
            $query = "CREATE TABLE IF NOT EXISTS users (
                    UserID INT NOT NULL AUTO_INCREMENT,
                    FirstName VARCHAR(255) NOT NULL,
                    Email VARCHAR(255) NOT NULL,
                    Username VARCHAR(30) NOT NULL,
                    Password CHAR(128) NOT NULL,
                    PRIMARY KEY(UserID)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $pdo->exec($query);
        } catch (PDOException $e) {
            die("
            <h1>
                Error
            </h1><br>
            <p>" . $e . "</p>");
            // die("<h1>Error: " . $e . "</h1>");
        }
        return $pdo;
    }
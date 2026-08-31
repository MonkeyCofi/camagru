<?php

    function setup_pdo(): PDO {
        $servername = "db";
        $port = "3306";
        $username = getenv('DB_USERNAME');
        $db_name = getenv('DB_NAME');
        $password = getenv('DB_USER_PASSWORD');
        $dsn = "mysql:host=$servername;dbname=camagru;port=$port;";
        $dso = null;
        try {
            $dso = new PDO($dsn, $username, $password, [
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            // create the table
            $query = "CREATE TABLE IF NOT EXISTS $db_name"
        } catch (PDOException $e) {
            die("
            <h1>
                Error
            </h1><br>
            <p>" . $e . "</p>");
            // die("<h1>Error: " . $e . "</h1>");
        }
        return $dso;
    }
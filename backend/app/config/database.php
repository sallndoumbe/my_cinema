<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private $host = "localhost";
    private $db_name = "my_cinema";
    private $username = "root";      // adapte selon ton MySQL
    private $password = "";          // adapte selon ton MySQL
    private $conn;

    public static function getConnection() {
        $instance = new self();
        $instance->conn = null;
        try {
            $instance->conn = new PDO(
                "mysql:host=" . $instance->host . ";dbname=" . $instance->db_name . ";charset=utf8mb4",
                $instance->username,
                $instance->password
            );
            // Exceptions PDO
            $instance->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            exit;
        }
        return $instance->conn;
    }
}

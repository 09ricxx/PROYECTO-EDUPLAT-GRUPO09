<?php
require_once __DIR__ . '/../config/conexion.php';

class Database
{
    private static ?PDO $conn = null;

    public static function get(): PDO
    {
        global $pdo;
        if (self::$conn === null) {
            self::$conn = $pdo;
        }
        return self::$conn;
    }
}

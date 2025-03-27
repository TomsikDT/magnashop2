<?php
namespace base\model;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new PDO(
                'mysql:host=localhost;dbname=magnashop2;charset=utf8mb4',
                'root', // uživatel
                '',     // heslo
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$instance;
    }
}

<?php

namespace Infrastructure\Database;

use PDO;
use PDOException;
use Infrastructure\Config\Env;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=%s",
                    Env::get('DB_HOST'),
                    Env::get('DB_NAME'),
                    Env::get('DB_CHARSET')
                );
                
                self::$instance = new PDO(
                    $dsn,
                    Env::get('DB_USER'),
                    Env::get('DB_PASS'),
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                throw new PDOException("Connection failed: " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
} 
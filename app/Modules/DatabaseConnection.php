<?php

namespace App\Modules;

use PDO;
use PDOException;
use Predis\Client;

// Класс для управления подключением к базе данных и Redis.
class DatabaseConnection
{
    private static $connection;

    // Функция получения соединение с базой данных.
    public static function getConnection()
    {
        if (self::$connection === null) {
            $config = include(__DIR__ . '/../../config/database.php');

            try {
                $dsn = "mysql:host={$config['servername']};dbname={$config['dbname']}";
                self::$connection = new PDO($dsn, $config['username'], $config['password']);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new PDOException("Ошибка подключения: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    // Функция получения клиента Redis.
    public static function getRedisClient()
    {
        return new Client([
            'scheme' => 'tcp',
            'host' => 'redis',
            'port' => 6379,
        ]);
    }
}

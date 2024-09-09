<?php
/*НА ДАННЫЙ МОМЕНТ ДАННАЯ ВЕЩЬ НЕ ИСПОЛЬЗУЕТСЯ*/
namespace Migrations;

use App\Modules\DatabaseConnection;
use PDO;
use PDOException;

class CategoriesMigration
{
    private $pdo;

    public function __construct()
    {
        // Используем метод getConnection из класса DatabaseConnection
        $this->pdo = DatabaseConnection::getConnection();
    }

    public function Up()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS categories (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL
            )";

            $this->pdo->exec($sql);
            
            echo "Миграция 'Up' выполнена успешно.\n";
        } catch (PDOException $e) {
            echo "Ошибка при выполнении миграции 'Up': " . $e->getMessage();
        }
    }

    public function Down()
    {
        try {
            $sql = "DROP TABLE IF EXISTS categories";

            $this->pdo->exec($sql);
            
            echo "Миграция 'Down' выполнена успешно.\n";
        } catch (PDOException $e) {
            echo "Ошибка при выполнении миграции 'Down': " . $e->getMessage();
        }
    }

    private function checkDatabaseConnection()
    {
        try {
            $result = $this->pdo->query('SELECT 1');
            $result->fetch(PDO::FETCH_ASSOC);
            
            echo 'Подключение к базе данных успешно установлено.';
        } catch (PDOException $e) {
            throw new PDOException('Ошибка подключения к базе данных: ' . $e->getMessage());
        }
    }
}

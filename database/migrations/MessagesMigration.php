<?php
/*НА ДАННЫЙ МОМЕНТ ДАННАЯ ВЕЩЬ НЕ ИСПОЛЬЗУЕТСЯ*/
namespace Migrations;

use App\Modules\DatabaseConnection;
use PDOException;

class MessagesMigration
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getConnection();
    }

    public function up()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS messages (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                category_id INT(6) UNSIGNED,
                username VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id)
            )";

            $this->pdo->exec($sql);

            echo "Таблица 'messages' успешно создана";
        } catch (PDOException $e) {
            echo "Ошибка при создании таблицы: " . $e->getMessage();
        }
    }

    public function down()
    {
        try {
            $sql = "DROP TABLE IF EXISTS messages";

            $this->pdo->exec($sql);

            echo "Таблица 'messages' успешно удалена";
        } catch (PDOException $e) {
            echo "Ошибка при удалении таблицы: " . $e->getMessage();
        }
    }
}

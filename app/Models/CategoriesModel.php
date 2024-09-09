<?php
namespace App\Models;

use App\Modules\DatabaseConnection;
use PDO;
use PDOException;

//Класс исключения для работы с БД
class DatabaseException extends \Exception {}

class CategoriesModel
{
    private $pdo;

    // Конструктор класса, принимает объект подключения к базе данных и инициализирует PDO
    public function __construct($dbConnection)
    {
        $this->pdo = DatabaseConnection::getConnection();
    }

    //Функция получения списка категорий из БД
    public function getCategories()
    {
        try {
            $query = "SELECT id, title FROM categories";
            //Подготовка запроса через PDO, чтобы избежать SQL-инъекций
            $statement = $this->pdo->prepare($query);
            // Выполнение подготовленного запроса
            $statement->execute();
            // Извлечение всех результатов в виде ассоциативного массива
            $categories = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $categories;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
}
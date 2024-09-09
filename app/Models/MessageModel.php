<?php

namespace App\Models;

use PDO;
use PDOException;

//Модель для работы с сообщениями.
class MessageModel
{
    private $dbConnection;

    // Конструктор класса инициализирует объект подключения к БД
    public function __construct(PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    // Сохраняет сообщений в БД.
    public function saveMessage($categoryId, $username, $messageText, $createdAt)
    {
        try {
            // Сохранение сообщений в БД
            $this->insertMessageToDatabase($categoryId, $username, $messageText, $createdAt);

            echo 'Сообщение успешно сохранено!';
        } catch (PDOException $e) {
            echo 'Ошибка при сохранении сообщения: ' . $e->getMessage();
        }
    }

    // Получает сообщений из БД.
    public function getMessagesByCategory($categoryId)
    {
        try {
            $sql = "SELECT * FROM messages WHERE category_id = :categoryId ORDER BY created_at DESC";
            //Подготовка запроса через PDO, чтобы избежать SQL-инъекций
            $statement = $this->dbConnection->prepare($sql);
            // Привязывание параметра :categoryId к значению $categoryId.
            $statement->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            // Выполнение подготовленного запроса
            $statement->execute();
            // Извлечение всех результатов в виде ассоциативного массива
            $messages = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $messages;
        } catch (PDOException $e) {
            throw new DatabaseException('Ошибка при получении сообщений по категории: ' . $e->getMessage());
        }
    }

    // Добавление сообщения в БД.
    private function insertMessageToDatabase($categoryId, $username, $messageText, $createdAt)
    {
        $sql = "INSERT INTO messages (category_id, username, content, created_at) VALUES (:categoryId, :username, :content, CONVERT_TZ(NOW(), 'UTC', 'Asia/Yekaterinburg'))";
        $statement = $this->dbConnection->prepare($sql);

        $statement->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->bindParam(':content', $messageText, PDO::PARAM_STR);

        $statement->execute();
    }
}

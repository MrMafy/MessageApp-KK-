<?php

namespace App\Controllers;

use App\Modules\DatabaseConnection;
use App\Models\MessageModel;
use PDOException;
use PDO;
class MessageController
{
    private $messageModel;

    public function __construct(MessageModel $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    //Функция отправки сообщения
    public function send($username, $categoryId, $messageText, $createdAt)
    {
        try {
            // Преобразуйте формат даты
            $createdAtFormatted = date('Y-m-d H:i:s', strtotime($createdAt));
    
            // Отправка сообщения в очередь Redis
            $redis = DatabaseConnection::getRedisClient();
            // Создание объекта для работы с очередью сообщений
            $messageQueue = new \App\Modules\MessageQueue($redis);
            // Отправка данных в очередь
            $messageQueue->sendToQueue($categoryId, $username, $messageText, $createdAtFormatted);
    
            echo 'Сообщение успешно отправлено в очередь Redis!';
        } catch (PDOException $e) {
            echo 'Ошибка при отправке сообщения в очередь Redis: ' . $e->getMessage();
        }
    }
    
    //Функция для получения сообщения
    public function get()
    {
        try {
            // Получаем ID категории из GET-запроса
            $categoryId = $_GET['categoryId'] ?? null;
            // Получаем сообщения из базы данных
            $messages = $this->messageModel->getMessagesByCategory($categoryId);


            // Отправляем сообщения в формате JSON
            header('Content-Type: application/json');
            echo json_encode($messages);
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo 'Ошибка при получении сообщений: ' . $e->getMessage();
        }
    }
}
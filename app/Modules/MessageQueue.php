<?php

namespace App\Modules;

use Predis\Client; //Клиент Redis.

//Класс для работы с очередью сообщений в Redis.

class MessageQueue
{
    private $redis;

    // Конструктор класса MessageQueue.
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    // Функция отправки сообщений в очередь Redis.
    public function sendToQueue($categoryId, $username, $messageText, $createdAt)
    {
        $messageData = json_encode([
            'categoryId' => $categoryId,
            'username' => $username,
            'messageText' => $messageText,
            'createdAt' => $createdAt,
        ]);

        // Помещаем сообщение в очередь Redis
        $this->redis->lpush('message_queue', (array)$messageData);
    }

    // Функция обработки сообщений из очереди.
    public function processQueue()
    {
        // Обработка сообщений из очереди
        while ($messageData = $this->redis->rpop('message_queue')) {
            // Обработка сообщения
            $data = json_decode($messageData, true);
            // Вызов нужных методов для сохранения сообщения в базе данных
            $this->processMessageData($data);
        }
    }

}
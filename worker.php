<?php

// Worker - скрипт для обработки сообщений из очереди Redis и сохранения их в базе данных.

require_once __DIR__ . '/vendor/autoload.php';

use App\Modules\DatabaseConnection;
use App\Modules\MessageQueue;
use App\Models\MessageModel;

// Получаем объект Redis и инициализируем очередь сообщений и модель Messages
$redis = DatabaseConnection::getRedisClient();
$messageQueue = new MessageQueue($redis);
$messageModel = new MessageModel(DatabaseConnection::getConnection());

// Бесконечный цикл обработки сообщений
while (true) {
    // Блокирующее получение сообщения из очереди, ожидание до 1 секунды
    $messageData = $redis->brpop('message_queue', 1);

    // Проверяем, получили ли мы данные
    if ($messageData !== null && isset($messageData[1])) {
        // Обработка сообщения
        $data = json_decode($messageData[1], true);

        // Проверяем, что декодирование прошло успешно и ключи существуют
        if (json_last_error() === JSON_ERROR_NONE && isset($data['categoryId'], $data['username'], $data['messageText'], $data['createdAt'])) {
            $messageModel->saveMessage($data['categoryId'], $data['username'], $data['messageText'], $data['createdAt']);
        } else {

            echo "Ошибка: Неправильный формат данных или отсутствуют необходимые поля.\n";
        }
    } else {

        echo "Ошибка: Не удалось получить данные из очереди или сообщение пусто.\n";
    }

    // Небольшая задержка, чтобы избежать постоянной загрузки CPU
    usleep(100000); // Задержка 100 мс
}

<?php
/*Главный входной файл приложения.
  В задачи файла входят:
  1) Обработка входящего запроса
  2) Определение соответствующего маршрута
  3) Вызов соответствующего контроллера и метода действия.
*/
// Автозагрузка классов из composer
require_once __DIR__ . '/../vendor/autoload.php';

use Migrations\CategoriesMigration;
use Migrations\MessagesMigration;
use App\Controllers\MessageController;
use App\Modules\DatabaseConnection;

// Получение URI текущего запроса
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = include __DIR__ . '/../router/routes.php';

//Флаг для отслеживания, был ли найден маршрут
$routeFound = false;

// Проход по всем маршрутам и поиск соответствия с URI запроса
foreach ($routes as $route => $controllerAction) {
    if ($uri === $route) {
        $routeFound = true;
        //Разбивание строки
        list($controller, $action) = explode('@', $controllerAction);

        $controllerFile = __DIR__ . '/../app/Controllers/' . $controller . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            $controllerClass = "\\App\\Controllers\\$controller";

            // Создание экземпляра MessageModel и передача в конструктор MessageController
            $messageModel = new \App\Models\MessageModel(DatabaseConnection::getConnection());
            $instance = new $controllerClass($messageModel);

            // Инициализация массива параметров запроса
            $params = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Для POST-запросов
                $params = $_POST;
            } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Для GET-запросов
                $params = $_GET;
            }

            // Проверка на существование метода и вызов с параметрами
            if (method_exists($instance, $action)) {
                // Вызываем метод с параметрами в правильном порядке
                call_user_func_array([$instance, $action], array_values($params));
            } else {
                echo 'Метод не найден';
            }

        } else {
            echo '404 Страница не найдена';
        }

        break;
    }
}
// Если ни один маршрут не подошел, вывод ошибки 404
if (!$routeFound) {
    echo '404 Страница не найдена';
}
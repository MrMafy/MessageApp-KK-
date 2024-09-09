<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Стили для оформления */
        .appName {
            max-width: 350px;
            margin: 20px auto;
        }
        .chat-container {
            max-width: 800px;
            height: 600px;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: flex;
            margin: 25px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .chat-list {
            width: 30%;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }
        .chat-list-item {
            padding: 15px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
        }
        .chat-list-item:hover {
            background-color: #f8f9fa;
        }
        .chat-list-item.active {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .chat-window {
            width: 70%;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .chat-messages {
            flex-grow: 1;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
            overflow-y: auto;
            height: 400px;
        }
        .message-input {
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <h1 class="appName mb-4"><?= $title ?></h1>

    <div class="chat-container">
        <!-- Боковая панель с категориями (список чатов) -->
        <div class="chat-list">
            <h5 class="p-3">Выберите категорию</h5>
            <div id="chat-list-items">
                <select class="form-control" id="categories">
                    <option value="" selected disabled>Выберите категорию</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Основное окно чата -->
        <div class="chat-window">
            <h4 id="chat-title">Чат</h4>
            <div id="chat" class="chat-messages">
                <!-- Здесь будут отображаться сообщения -->
            </div>

            <!-- Поле для ввода сообщения -->
            <div class="message-input">
                <input type="text" id="username" class="form-control mb-2" placeholder="Ваше имя" aria-label="Имя пользователя">
                <textarea id="message" class="form-control" rows="2" placeholder="Введите сообщение"></textarea>
                <button class="btn btn-success mt-2 w-100" onclick="sendMessage()">Отправить</button>
            </div>
        </div>
    </div>                    

<script>
    // Скрипт для загрузки сообщений при загрузке страницы и последующей динамической подгрузки
    $(document).ready(function () {
        // Сбрасываем выбранную категорию при загрузке страницы
        $('#username').val('');
        $('#message').val('');
        $('#categories').prop('selectedIndex', 0);
        
        // Обработчик изменения категории
        $('#categories').change(function () {
            loadMessages();
        });

        // Обновляем сообщения каждые 2.5 секунды (2500 миллисекунд)
        setInterval(loadMessages, 2500);
    });

    function loadMessages() {
        var categoryId = $('#categories').val();

        // Отправка запроса на сервер для получения сообщений
        $.ajax({
            url: '/get',
            method: 'GET',
            data: { categoryId: categoryId },
            success: function (response) {
                console.log("Ответ от сервера:", response);  // Для проверки данных
                displayMessages(response);
            },
            error: function (xhr, status, error) {
                console.error('Ошибка при загрузке сообщений: ' + error);
            }
        });
    }

    function displayMessages(messages) {
        var chat = $('#chat');

        // Очистка текущего содержимого чата
        chat.empty();

        // Проверка, является ли messages массивом
        if (!Array.isArray(messages)) {
            console.error("Ожидался массив сообщений, но пришло что-то другое:", messages);
            return;
        }

        // Отображение каждого сообщения
        messages.forEach(function (message) {
            var messageElement = $('<div class="message"></div>');

            var usernameElement = $('<strong></strong>').text(message.username + ': ');
            var contentElement = $('<span></span>').text(message.content);
            var timestampElement = $('<small class="text-muted"></small>').text(' (' + message.created_at + ')');

            messageElement.append(usernameElement).append(contentElement).append(timestampElement);

            chat.append(messageElement);
        });

        // Прокрутка вниз для отображения последних сообщений
        chat.scrollTop(chat[0].scrollHeight);
    }

    function sendMessage() {
        var username = $('#username').val();
        var message = $('#message').val();
        var category = $('#categories').val();

        // Проверка наличия имени пользователя и выбранной категории
        if (!username && !message && !category) {
            alert("Пожалуйста, выберите категорию и заполните поля.");
            return;
        } else if (!username) {
            alert("Пожалуйста, введите имя пользователя.");
            return;
        } else if (!message) {
            alert("Пожалуйста, введите сообщение.");
            return;
        } else if (!category) {
            alert("Пожалуйста, выберите категорию.");
            return;
        }

        // Асинхронная отправка сообщения
        $.ajax({
            url: '/send', // URL для отправки сообщения
            method: 'POST',
            data: {
                username: username,
                categoryId: category,
                messageText: message,
                createdAt: new Date().toISOString()
            },
            success: function () {
                // Очистка полей после отправки сообщения
                $('#username').val('');
                $('#message').val('');
                //$('#categories').prop('selectedIndex', 0);

                // Обновляем чат после успешной отправки сообщения
                loadMessages();
            },
            error: function (xhr, status, error) {
                console.error('Ошибка при отправке сообщения: ' + error);
            }
        });
    }
    </script>

</body>
</html>

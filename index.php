<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мародина</title>
    <style>
        @font-face { font-family: 'KOYSAN'; src: url('fonts/font.woff') format('woff'); }
        body { 
            font-family: 'KOYSAN', Arial, sans-serif; 
            background: linear-gradient(to right, #2c3e50, #3498db); 
            color: #fff; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
        }
        h1, h2 { color: #fff; text-align: center; }
        .form-container { width: 100%; max-width: 400px; margin-bottom: 20px; }
        form { 
            background: rgba(255, 255, 255, 0.1); 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); 
            margin-bottom: 20px; 
        }
        label { display: block; margin-bottom: 10px; font-weight: bold; }
        textarea { 
            width: 100%; 
            height: 80px; 
            border-radius: 8px; 
            border: none; 
            padding: 10px; 
            font-size: 16px; 
            resize: none; 
            background: rgba(255, 255, 255, 0.2); 
            color: #fff; 
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.5);
        }
        textarea:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }
        input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            border-radius: 4px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        input[type="submit"] { 
            background: #007bff; 
            color: #fff; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover { background: #0056b3; }
        .message { 
            background: rgba(255, 255, 255, 0.1); 
            border-radius: 5px; 
            padding: 10px; 
            margin: 10px 0; 
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.5); 
            max-width: 400px; 
        }
        .emoji-button { background: transparent; border: none; cursor: pointer; font-size: 20px; }
        .emoji-container { display: flex; justify-content: space-between; margin-top: 10px; }
    </style>
    <script type="text/javascript">
        function insertEmoji(emoji) { document.getElementById('message').value += emoji; }
    </script>
</head>
<body>
    <h1>Мародина</h1>

    <!-- Объединенная форма для отправки сообщений и поиска -->
    <div class="form-container">
        <form action="" method="post">
            <label for="message">Сообщение:</label>
            <textarea id="message" name="message" required placeholder="Напишите сообщение..."></textarea>
            <div class="emoji-container">
                <button type="button" class="emoji-button" onclick="insertEmoji('😊')">😊</button>
                <button type="button" class="emoji-button" onclick="insertEmoji('😂')">😂</button>
                <button type="button" class="emoji-button" onclick="insertEmoji('❤️')">❤️</button>
                <button type="button" class="emoji-button" onclick="insertEmoji('👍')">👍</button>
            </div>
            <input type="submit" value="Отправить">
        </form>

        <form action="" method="get">
            <input type="text" id="search" name="search" placeholder="Введите текст для поиска..." />
            <input type="submit" value="Поиск">
        </form>
    </div>

    <h2>Сообщения:</h2>
    <div id="messages">
        <?php
            // Проверка существования файла и чтение сообщений
            $messages = file_exists('messages.txt') ? file('messages.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

            // Проверка на наличие поискового запроса
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $filteredMessages = [];

            // Фильтрация сообщений по поисковому запросу
            foreach ($messages as $msg) {
                if (empty($searchQuery) || stripos($msg, $searchQuery) !== false) {
                    $filteredMessages[] = htmlspecialchars($msg); // Экранирование специальных символов
                }
            }

            // Вывод отфильтрованных сообщений
            foreach (array_reverse($filteredMessages) as $msg) {
                echo '<div class="message">' . $msg . '</div>';
            }

            // Обработка отправки сообщения
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
                // Получение текущей даты и времени
                $timestamp = date('Y-m-d H:i:s'); // Формат: ГГГГ-ММ-ДД ЧЧ:ММ:СС
                // Запись сообщения в файл с временной меткой
                file_put_contents('messages.txt', "[$timestamp] " . trim($_POST['message']) . PHP_EOL, FILE_APPEND);
                // Перенаправление на ту же страницу для предотвращения повторной отправки формы
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        ?>
    </div>
</body>
</html>
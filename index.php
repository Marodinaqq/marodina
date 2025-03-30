<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="https://github.com/Marodinaqq/marodina/blob/main/icon.png" type="image/x-icon">
    <title>Мародина</title>
    <script type="text/javascript">
        function insertEmoji(emoji) { document.getElementById('message').value += emoji; }
        function hide() { document.getElementById('searchForm') }
    </script>
</head>
<body>

    <input type="checkbox" id="searchbtn"/>
    <nav>
        <h1>Мародина</h1>

        <input type="checkbox" id="searchbtn"/>
        <form id="searchForm" action="" method="get">
            <input type="text" id="search" name="search" placeholder="Введите текст для поиска..." />
            <input type="submit" value="Поиск">
        </form>
        <label for="searchbtn"> 🔍︎ </label>
    </nav>

    <div id="chat">
        <div id="messages">
            <?php
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
                    file_put_contents('messages.txt', trim($_POST['message']) . PHP_EOL, FILE_APPEND);
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            ?>
        </div>
        <form id="messageForm" action="api.php" method="post">
            <textarea id="message" name="message" placeholder="Введите сообщение" rows=1 required></textarea>
            <input type="submit" value="Отправить">
            <input type="submit" value="→">
            <div class="emoji-container">
                <button type="button" class="emoji-button" onclick="insertEmoji('😊')">😊</button>
                <button type="button" class="emoji-button" onclick="insertEmoji('😂')">😂</button>
                <button type="button" class="emoji-button" onclick="insertEmoji('❤️')">❤️</button>
                <button type="button" class="emoji-button" onclick="insertEmoji('👍')">👍</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Указываем файл для хранения сообщений и логов
$filename = 'messages.txt';
$errorLogFile = 'error_log.txt';

// Функция для логирования ошибок
function logError($message) {
    global $errorLogFile;
    file_put_contents($errorLogFile, date("Y-m-d H:i:s") . " - ERROR: $message" . PHP_EOL, FILE_APPEND);
}

// Функция для обработки отправки сообщения
function handleMessage($filename) {
    // Получаем сообщение из POST-запроса с фильтрацией
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($message) {
        $dateTime = date("Y-m-d H:i:s"); // Получаем текущую дату и время
        $fullMessage = "$dateTime - $message" . PHP_EOL; // Форматируем сообщение

        // Проверяем, существует ли файл, и создаем его, если нет
        if (!file_exists($filename)) {
            if (!touch($filename)) {
                logError("Failed to create message file: $filename");
                return;
            }
        }

        // Сохраняем сообщение в файл
        if (file_put_contents($filename, $fullMessage, FILE_APPEND) === false) {
            logError("Failed to write message to file: $filename");
        }
    } else {
        logError("No message provided");
    }
}

// Обработка отправки сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleMessage($filename);
}

// Перенаправляем обратно на главную страницу после отправки сообщения
header('Location: index.php');
exit();
?>

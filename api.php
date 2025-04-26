<?php
// Конфигурация безопасности
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
// Указываем файл для хранения сообщений и логов
$filename = 'messages.txt';
$errorLogFile = 'error_log.txt';
$uploadDir = 'uploads/'; // Directory for uploaded files

// Функция для логирования ошибок
function logError($message) {
    global $errorLogFile;
    file_put_contents($errorLogFile, date("Y-m-d H:i:s") . " - ERROR: $message" . PHP_EOL, FILE_APPEND);
}

// Функция для обработки отправки сообщения
function handleMessage($filename) {
    global $uploadDir;
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $dateTime = date("Y-m-d H:i:s");
    $filePath = '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
        
        if (in_array($detectedType, $allowedTypes)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('img_') . '.' . $extension;
            $filePath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                logError("Failed to move uploaded file");
                $filePath = '';
            }
        } else {
            logError("Invalid file type: $detectedType");
        }
    }

    // Save message or file info
    if ($message || $filePath) {
        $content = $dateTime . ' - ';
        if ($message) $content .= $message;
        if ($filePath) $content .= ' [IMAGE:' . $filePath . ']';
        $content .= PHP_EOL;

        if (file_put_contents($filename, $content, FILE_APPEND) === false) {
            logError("Failed to write to file: $filename");
        }
    } else {
        logError("No content provided");
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

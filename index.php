<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="https://raw.githubusercontent.com/Marodinaqq/marodina/main/icon.png" type="image/x-icon">
    <title>Мародина</title>
<script>
// T9-подсказки
function setupT9Suggestions() {
    const messageField = document.getElementById('message');
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.id = 't9-suggestions';
    suggestionsContainer.style.display = 'none';
    messageField.parentNode.insertBefore(suggestionsContainer, messageField.nextSibling);

    // Словарь T9 (русский язык)
    const t9Dictionary = {
        '2': ['а', 'б', 'в', 'г'],
        '3': ['д', 'е', 'ж', 'з'],
        '4': ['и', 'й', 'к', 'л'],
        '5': ['м', 'н', 'о', 'п'],
        '6': ['р', 'с', 'т', 'у'],
        '7': ['ф', 'х', 'ц', 'ч'],
        '8': ['ш', 'щ', 'ъ', 'ы'],
        '9': ['ь', 'э', 'ю', 'я']
    };

    // Популярные слова для подсказок
    const popularWords = [
        'прiвет', 'окок', 'дела', 'хорошо', 'плохо', 
        'спасiбо', 'пожалуйста', 'да', 'нiт', 'окей',
        'нi', 'нехочу', 'сiсi', 'перемога', 'буде', 'кок', 'кокi',
        'люблю', 'нравiтся', 'смешно', 'грустно', 'здорово'
    ];

    messageField.addEventListener('input', function() {
        const lastWord = this.value.split(/\s+/).pop().toLowerCase();
        suggestionsContainer.innerHTML = '';
        suggestionsContainer.style.display = 'none';

        if (lastWord.length > 0) {
            // Проверяем, если ввод состоит из цифр (режим T9)
            if (/^[2-9]+$/.test(lastWord)) {
                const digits = lastWord.split('');
                let suggestions = [''];

                digits.forEach(digit => {
                    const newSuggestions = [];
                    const letters = t9Dictionary[digit] || [];
                    suggestions.forEach(suggestion => {
                        letters.forEach(letter => {
                            newSuggestions.push(suggestion + letter);
                        });
                    });
                    suggestions = newSuggestions;
                });

                if (suggestions.length > 0) {
                    showSuggestions(suggestions.slice(0, 5));
                }
            } else {
                // Обычные текстовые подсказки
                const filtered = popularWords.filter(word => 
                    word.startsWith(lastWord) && word !== lastWord
                );
                if (filtered.length > 0) {
                    showSuggestions(filtered.slice(0, 5));
                }
            }
        }
    });

    function showSuggestions(words) {
        suggestionsContainer.innerHTML = '';
        words.forEach(word => {
            const btn = document.createElement('button');
            btn.textContent = word;
            btn.type = 'button';
            btn.addEventListener('click', function() {
                const currentValue = messageField.value.split(/\s+/);
                currentValue.pop();
                currentValue.push(word);
                messageField.value = currentValue.join(' ') + ' ';
                suggestionsContainer.style.display = 'none';
                messageField.focus();
            });
            suggestionsContainer.appendChild(btn);
        });
        suggestionsContainer.style.display = 'flex';
    }
}

// Автозамена "и" на "i"
function setupAutoReplace() {
    const messageField = document.getElementById('message');
    
    messageField.addEventListener('input', function() {
        if (this.value.includes('и')) {
            // Сохраняем позицию курсора
            const start = this.selectionStart;
            const end = this.selectionEnd;
            
            // Заменяем "и" на "i"
            this.value = this.value.replace(/и/g, 'i');
            
            // Восстанавливаем позицию курсора
            this.setSelectionRange(start, end);
        }
    });
}

// Добавьте эти функции в обработчик DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // ... существующий код ...
    
    setupT9Suggestions();
    setupAutoReplace();
});
        function insertEmoji(emoji) {
            const messageField = document.getElementById('message');
            messageField.value += emoji;
            messageField.focus();
        }

        // Функция для обработки отправки формы
        function handleFormSubmit(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        }

        // Функция для преобразования текста в ссылки
        function linkify(text) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, function(url) {
                return '<a href="' + url + '" target="_blank" class="message-link">' + url + '</a>';
            });
        }

        // Назначаем обработчики событий
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('messageForm');
            form.addEventListener('submit', handleFormSubmit);
            
            // Преобразуем существующие ссылки в сообщениях
            document.querySelectorAll('.message').forEach(message => {
                message.innerHTML = linkify(message.innerHTML);
            });
        });
</script>
</head>
<body>
    <input type="checkbox" id="searchbtn" aria-hidden="true">
    <nav>
        <h1>Мародина</h1>
        <form id="searchForm" action="" method="get">
            <input type="text" id="search" name="search" placeholder="Введите текст для поиска..." 
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">🔍 Поиск</button>
        </form>
        <label for="searchbtn" aria-label="Поиск">🔍</label>
    </nav>

    <div id="chat">
        <div id="messages">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['message']) || isset($_FILES['image']))) {
                $message = isset($_POST['message']) ? trim($_POST['message']) : '';
                require_once 'api.php';
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }

            $messages = [];
            $filename = 'messages.txt';
            if (file_exists($filename)) {
                $fileContent = file_get_contents($filename);
                if (!empty($fileContent)) {
                    $messages = explode(PHP_EOL, $fileContent);
                    $messages = array_filter($messages, 'trim');
                }
            }

            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            if (!empty($searchQuery)) {
                $filteredMessages = array_filter($messages, function($msg) use ($searchQuery) {
                    return stripos($msg, $searchQuery) !== false;
                });
                $messages = $filteredMessages;
            }

            if (!empty($messages)) {
                foreach (array_reverse($messages) as $msg) {
                    if (preg_match('/\[IMAGE:(.+?)\]/', $msg, $matches)) {
                        $imagePath = $matches[1];
                        $msgText = preg_replace('/\[IMAGE:.+?\]/', '', $msg);
                        echo '<div class="message">' . htmlspecialchars($msgText) . 
                             '<br><img src="' . htmlspecialchars($imagePath) . '" style="max-width: 100%; max-height: 200px; margin-top: 10px;"></div>';
                    } else {
                        echo '<div class="message">' . htmlspecialchars($msg) . '</div>';
                    }
                }
            } else {
                echo '<div class="message">Нет сообщений для отображения</div>';
            }
            ?>
        </div>
        <form id="messageForm" method="post" enctype="multipart/form-data">
            <textarea id="message" name="message" placeholder="Введите сообщение" rows="1"></textarea>
            <input type="file" id="image" name="image" accept="image/*" style="display: none;">
            <button type="button" onclick="document.getElementById('image').click()" title="Прикрепить изображение">📷</button>
            <button type="submit" title="Отправить">→</button>
            <div class="emoji-container">
                <button type="button" onclick="insertEmoji('🐓')" title="Курица">🐓</button>
                <button type="button" onclick="insertEmoji('😂')" title="Смех">😂</button>
                <button type="button" onclick="insertEmoji('❤️')" title="Сердце">❤️</button>
                <button type="button" onclick="insertEmoji('👍')" title="Палец вверх">👍</button>
            </div>
        </form>
    </div>
</body>
</html>

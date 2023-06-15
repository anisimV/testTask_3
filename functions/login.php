<?php

// Подключаем файл с настройками базы данных
require_once '../db/database.php';

// Начинаем сессию
session_start();

// Инициализируем переменные для ошибок и успешной аутентификации
$error = '';
$success = '';

// Константа для ключа сервера SmartCaptcha


// Функция для проверки капчи
function check_captcha($token) {
    // Инициализируем cURL-сеанс
    $ch = curl_init();

    // Формируем аргументы запроса
    $args = http_build_query([
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'],
    ]);

    // Устанавливаем параметры cURL-сеанса
    curl_setopt($ch, CURLOPT_URL, "https://captcha-api.yandex.ru/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    // Выполняем запрос к серверу капчи
    $server_output = curl_exec($ch);

    // Получаем HTTP-код ответа
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Завершаем cURL-сеанс
    curl_close($ch);

    // Если HTTP-код не равен 200, то выводим сообщение об ошибке и разрешаем доступ
    if ($httpcode !== 200) {
        echo "Разрешаем доступ из-за ошибки: код=$httpcode; сообщение=$server_output\n";
        return true;
    }

    // Декодируем ответ сервера
    $resp = json_decode($server_output);

    // Возвращаем результат проверки капчи (true - успешно, false - не успешно)
    return $resp->status === "ok";
}

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем токен капчи из POST-запроса
    $token = $_POST['smart-token'];

    // Проверяем капчу
    if (check_captcha($token)) {
        // Проверяем наличие логина и пароля в POST-запросе
        if (!isset($_POST['login']) || !isset($_POST['password'])) {
            // ...
        } else {
            // Получаем логин и пароль из POST-запроса
            $login = $_POST['login'];
            $password = $_POST['password'];

            try {
                // Подготавливаем и выполняем запрос к базе данных для получения пользователя
                $stmt = $conn->prepare('SELECT * FROM users WHERE user_email = :login OR user_phone = :login');
                $stmt->execute(['login' => $login]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Если пользователь найден
                if ($user) {
                    // Проверяем соответствие пароля
                    if (password_verify($password, $user['user_password'])) {
                        // Сохраняем идентификатор пользователя в сессии
                        $_SESSION['user_id'] = $user['user_id'];

                        // Устанавливаем сообщение об успешной аутентификации
                        $success = 'Успешная аутентификация!';

                        // Перенаправляем на страницу профиля пользователя
                        header('Location: ../forms/profile.php');
                        exit(); // Останавливаем дальнейшее выполнение скрипта
                    } else {
                        // Устанавливаем сообщение об ошибке (неверный пароль)
                        $error = 'Неверный пароль';
                    }
                } else {
                    // Устанавливаем сообщение об ошибке (пользователь с такими данными не найден)
                    $error = 'Пользователь с такими данными не найден';
                }
            } catch (PDOException $e) {
                // Устанавливаем сообщение об ошибке (ошибка при выполнении запроса к базе данных)
                $error = 'Ошибка при выполнении запроса: ' . $e->getMessage();
            }
        }
    } else {
        // Устанавливаем сообщение об ошибке (подтвердите, что вы не робот)
        $error = 'Подтвердите, что вы не робот';
    }
}

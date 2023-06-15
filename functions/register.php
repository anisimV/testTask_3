<?php

require_once '../db/database.php'; // Подключение файла database.php, содержащего настройки базы данных

$error = ''; // Переменная для хранения сообщения об ошибке
$success = ''; // Переменная для сообщений об успехе

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка, что все необходимые поля были переданы в запросе
    if (!isset($_POST['names']) || !isset($_POST['phone']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
        $error = 'Некоторые поля отсутствуют в запросе';
    } else {
        $name = $_POST['names']; // Получение значения поля "names" из POST-запроса
        $phone = $_POST['phone']; // Получение значения поля "phone" из POST-запроса
        $email = $_POST['email']; // Получение значения поля "email" из POST-запроса
        $password = $_POST['password']; // Получение значения поля "password" из POST-запроса
        $confirmPassword = $_POST['confirm_password']; // Получение значения поля "confirm_password" из POST-запроса

        // Проверка совпадения паролей
        if ($password !== $confirmPassword) {
            $error = 'Пароли не совпадают';
        } elseif (strlen($password) < 8) { // Проверка, что пароль содержит не менее 8 символов
            $error = 'Пароль должен содержать не менее 8 символов';
        } else {
            try {
                $conn = new PDO('mysql:host=localhost;dbname=testTask_3', 'root', ''); // Создание подключения к базе данных
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок

                // Подготовка и выполнение запроса на выборку пользователя с указанным email или phone
                $stmt = $conn->prepare('SELECT * FROM users WHERE user_email = :email OR user_phone = :phone');
                $stmt->execute(['email' => $email, 'phone' => $phone]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC); // Получение результата выборки

                if ($user) { // Если пользователь существует, то выводим ошибку
                    $error = 'Пользователь с такими данными уже зарегистрирован';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Хеширование пароля

                    // Подготовка и выполнение запроса на добавление нового пользователя
                    $stmt = $conn->prepare('INSERT INTO users (user_name, user_phone, user_email, user_password) VALUES (:names, :phone, :email, :password)');
                    $stmt->bindParam(':names', $name);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->execute();

                    header('Location: ../forms/authorize.php'); // Перенаправление на страницу авторизации
                    exit(); // Завершение выполнения скрипта
                }
            } catch (PDOException $e) { // Обработка исключений, возникающих при выполнении запросов к базе данных
                $error = 'Ошибка при выполнении запроса: ' . $e->getMessage();
            }
        }
    }
}

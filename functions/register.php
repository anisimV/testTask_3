<?php

require_once '../db/database.php';

$error = ''; // Переменная для хранения сообщения об ошибке
$success = ''; // Переменная для сообщений об успехе

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['names']) || !isset($_POST['phone']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
        $error = 'Некоторые поля отсутствуют в запросе';
    } else {
        $name = $_POST['names'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // проверка пароля
        if ($password !== $confirmPassword) {
            $error = 'Пароли не совпадают';
        } elseif (strlen($password) < 8) {
            $error = 'Пароль должен содержать не менее 8 символов';
        } else {
            try {
                $conn = new PDO('mysql:host=localhost;dbname=testTask_3', 'root', '');
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare('SELECT * FROM users WHERE user_email = :email OR user_phone = :phone');
                $stmt->execute(['email' => $email, 'phone' => $phone]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $error = 'Пользователь с такими данными уже зарегистрирован';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare('INSERT INTO users (user_name, user_phone, user_email, user_password) VALUES (:names, :phone, :email, :password)');
                    $stmt->bindParam(':names', $name);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashedPassword);
                    $stmt->execute();

                    $success = 'Успешная регистрация!';
                }
            } catch (PDOException $e) {
                $error = 'Ошибка при выполнении запроса: ' . $e->getMessage();
            }
        }
    }
}

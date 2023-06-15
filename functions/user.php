<?php

session_start();

// Проверяем, вошел ли пользователь в систему
if (!isset($_SESSION['user_id'])) {
    header('Location: ../forms/authorize.php'); // Перенаправляем на страницу входа, если пользователь не вошел в систему
    exit();
}

// Получаем информацию о пользователе из базы данных
require_once '../db/database.php';

try {
    $stmt = $conn->prepare('SELECT user_name, user_email, user_phone, user_password FROM users WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Произошла ошибка: ' . $e->getMessage();
    exit();
}

// Обработка обновления информации пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    if (password_verify($oldPassword, $user['user_password'])) {
        // Проверка старого пароля успешна
        // Проверяем, какие поля пользователь хочет обновить
        $updateFields = [];
        if (isset($_POST['name'])) {
            $updateFields['user_name'] = $_POST['name'];
        }
        if (isset($_POST['email'])) {
            $updateFields['user_email'] = $_POST['email'];
        }
        if (isset($_POST['phone'])) {
            $updateFields['user_phone'] = $_POST['phone'];
        }
        if (!empty($newPassword)) {
            $updateFields['user_password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (!empty($updateFields)) {
            // Обновляем информацию о пользователе в базе данных
            $updateQuery = 'UPDATE users SET ';
            $params = [];
            foreach ($updateFields as $field => $value) {
                $updateQuery .= $field . ' = :' . $field . ', ';
                $params[$field] = $value;
            }
            $updateQuery = rtrim($updateQuery, ', ');
            $updateQuery .= ' WHERE user_id = :user_id';
            $params['user_id'] = $_SESSION['user_id'];

            try {
                $stmt = $conn->prepare($updateQuery);
                $stmt->execute($params);
                $success = 'Информация успешно обновлена';
                // Обновляем отображаемую информацию о пользователе
                foreach ($updateFields as $field => $value) {
                    $user[$field] = $value;
                }
            } catch (PDOException $e) {
                $error = 'Произошла ошибка при обновлении информации: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Неверный старый пароль';
    }
}

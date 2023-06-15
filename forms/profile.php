<?php 

require_once '../functions/user.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Профиль пользователя</title>
</head>
<body>
    <div class="content">
        <h1>Профиль пользователя</h1>

        <?php if (!empty($success)) : ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)) : ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" value="<?php echo $user['user_name']; ?>"><br>

            <label for="email">Почта:</label>
            <input type="email" id="email" name="email" value="<?php echo $user['user_email']; ?>"><br>

            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone" value="<?php echo $user['user_phone']; ?>"><br>

            <label for="old_password">Старый пароль:</label>
            <input type="password" id="old_password" name="old_password"><br>

            <label for="new_password">Новый пароль:</label>
            <input type="password" id="new_password" name="new_password"><br>

            <input type="submit" value="Обновить информацию">
        </form>

        <form method="POST" action="../functions/logout.php">
            <input type="submit" value="Выход">
        </form>
    </div>
</body>
</html>

<?php 

require_once '../functions/register.php';
require_once '../db/database.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Document</title>
</head>
<body>
    <div class="registr">
        <!-- HTML-форма регистрации -->
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php elseif ($success): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>
        
        <form method="POST" action="../forms/registration.php">
            <input type="text" name="names" placeholder="Имя" required><br>
            <input type="tel" name="phone" placeholder="Телефон" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Пароль" required><br>
            <input type="password" name="confirm_password" placeholder="Повторите пароль" required><br>
            <input type="submit" name="register" value="Зарегистрироваться">
        </form>
    </div>
</body>
</html>

<?php 

require_once '../functions/login.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">

    <title>Авторизация</title>
</head>
<body>
    <div class="content">

        <?php if ($error): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="../forms/authorize.php" method="POST">
            <h3>Войти в профиль:</h3>
            <input type="tel" name="login" placeholder="Телефон или email" required>
            <input type="password" id="password" name="password" required placeholder="Пароль">
            <!-- Тут должна быть капча -->

            <input type="submit" value="Войти" >
            <a href="../forms/registration.php">Зарегистрироваться</a>
        </form>

    </div>

</body>
</html>

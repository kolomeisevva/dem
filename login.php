<?php
require_once 'db.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if (strtolower($user['login']) === 'admin') {
            $_SESSION['admin'] = true;
            header("Location: admin.php");
            exit();
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            header("Location: form.php");
            exit();
        }
    } else {
        $message = "Неверный логин или пароль";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <h2>Вход в систему</h2>
        <form method="POST">
            <input type="text" name="login" placeholder="Логин" required><br>
            <input type="password" name="password" placeholder="Пароль" required><br>
            <input type="submit" value="Войти">
        </form>

        <p class="message"><?= $message ?></p>
    </div>
</body>
</html>

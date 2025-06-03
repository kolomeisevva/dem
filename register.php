<?php
require_once 'db.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];
    $fullname = $_POST["fullname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    if (!preg_match("/^[а-яё]{6,}$/iu", $login)) {
        $message = "Логин должен быть на кириллице и не менее 6 символов";
    } elseif (strlen($password) < 6) {
        $message = "Пароль должен быть не менее 6 символов";
    } elseif (!preg_match("/^[А-Яа-яЁё ]+$/u", $fullname)) {
        $message = "ФИО должно содержать только кириллицу и пробелы";
    } elseif (!preg_match("/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/", $phone)) {
        $message = "Телефон должен быть в формате +7(XXX)-XXX-XX-XX";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Некорректный формат e-mail";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $check = $conn->prepare("SELECT id FROM users WHERE login = ?");
        $check->execute([$login]);

        if ($check->rowCount() > 0) {
            $message = "Такой логин уже существует";
        } else {
            $sql = $conn->prepare("INSERT INTO users (login, password, fullname, phone, email) VALUES (?, ?, ?, ?, ?)");
            $sql->execute([$login, $hash, $fullname, $phone, $email]);
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="container">
        <h2>Регистрация</h2>
        <form method="POST">
            <input type="text" name="login" placeholder="Логин" required><br>
            <input type="password" name="password" placeholder="Пароль" required><br>
            <input type="text" name="fullname" placeholder="ФИО" required><br>
            <input type="text" name="phone" placeholder="+7(XXX)-XXX-XX-XX" required><br>
            <input type="text" name="email" placeholder="Email" required><br>
            <input type="submit" value="Зарегистрироваться">
        </form>
        <p class="message"><?= $message ?></p>
    </div>
</body>
</html>

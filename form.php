<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $weight = $_POST["weight"];
    $size = $_POST["size"];
    $cargo_type = $_POST["cargo_type"];
    $from = $_POST["from"];
    $to = $_POST["to"];
    $user_id = $_SESSION['user_id'];

    if (empty($date) || empty($weight) || empty($size) || empty($cargo_type) || empty($from) || empty($to)) {
        $message = "Заполните все поля";
    } else {
        $stmt = $conn->prepare("INSERT INTO requests (user_id, date, weight, size, cargo_type, from_address, to_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $date, $weight, $size, $cargo_type, $from, $to]);
        $message = "Заявка успешно отправлена!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Создание заявки</title>
    <link rel="stylesheet" href="css/form.css">
</head>
<body>
    <div class="container">
        <h2>Оформить перевозку</h2>
        <form method="POST">
            <label>Дата и время:</label>
            <input type="datetime-local" name="date" required>

            <label>Вес груза:</label>
            <input type="text" name="weight" required>

            <label>Габариты груза:</label>
            <input type="text" name="size" required>

            <label>Тип груза:</label>
            <select name="cargo_type" required>
                <option value="">-- Выберите --</option>
                <option value="хрупкое">Хрупкое</option>
                <option value="скоропортящееся">Скоропортящееся</option>
                <option value="требует рефрижератор">Требует рефрижератор</option>
                <option value="животные">Животные</option>
                <option value="жидкость">Жидкость</option>
                <option value="мебель">Мебель</option>
                <option value="мусор">Мусор</option>
            </select>

            <label>Адрес отправления:</label>
            <input type="text" name="from" required>

            <label>Адрес доставки:</label>
            <input type="text" name="to" required>

            <input type="submit" value="Отправить заявку">
        </form>

        <p class="message"><?= $message ?></p>
    </div>
</body>
</html>

<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Обработка отзыва
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'], $_POST['request_id'])) {
    $feedback = $_POST['feedback'];
    $request_id = $_POST['request_id'];

    $stmt = $conn->prepare("UPDATE requests SET feedback = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$feedback, $request_id, $user_id]);
    $message = "Отзыв добавлен!";
}

// Получаем заявки пользователя
$stmt = $conn->prepare("SELECT * FROM requests WHERE user_id = ?");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Мои заявки</title>
</head>
<body>
    <h2>Мои заявки</h2>
    <p style="color:green;"><?= $message ?></p>

    <?php if (count($requests) == 0): ?>
        <p>Заявок пока нет.</p>
    <?php else: ?>
        <?php foreach ($requests as $row): ?>
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <strong>Дата:</strong> <?= $row['date'] ?><br>
                <strong>Тип груза:</strong> <?= $row['cargo_type'] ?><br>
                <strong>Вес:</strong> <?= $row['weight'] ?><br>
                <strong>Габариты:</strong> <?= $row['size'] ?><br>
                <strong>Откуда:</strong> <?= $row['from_address'] ?><br>
                <strong>Куда:</strong> <?= $row['to_address'] ?><br>
                <strong>Статус:</strong> <?= $row['status'] ?><br>

                <!-- Отзыв -->
                <?php if (empty($row['feedback'])): ?>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                        <textarea name="feedback" placeholder="Оставьте отзыв..."></textarea><br>
                        <input type="submit" value="Отправить отзыв">
                    </form>
                <?php else: ?>
                    <strong>Ваш отзыв:</strong> <?= htmlspecialchars($row['feedback']) ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

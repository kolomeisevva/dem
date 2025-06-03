<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'], $_POST['request_id'])) {
    $feedback = $_POST['feedback'];
    $request_id = $_POST['request_id'];

    $stmt = $conn->prepare("UPDATE requests SET feedback = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$feedback, $request_id, $user_id]);
    $message = "Отзыв добавлен!";
}

$stmt = $conn->prepare("SELECT * FROM requests WHERE user_id = ?");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Мои заявки</title>
    <link rel="stylesheet" href="css/requests.css">
</head>
<body>
    <div class="container">
        <h2>Мои заявки</h2>
        <?php if ($message): ?>
            <p class="success"><?= $message ?></p>
        <?php endif; ?>

        <?php if (count($requests) == 0): ?>
            <p>Заявок пока нет.</p>
        <?php else: ?>
            <?php foreach ($requests as $row): ?>
                <div class="card">
                    <p><strong>Дата:</strong> <?= $row['date'] ?></p>
                    <p><strong>Тип груза:</strong> <?= $row['cargo_type'] ?></p>
                    <p><strong>Вес:</strong> <?= $row['weight'] ?></p>
                    <p><strong>Габариты:</strong> <?= $row['size'] ?></p>
                    <p><strong>Откуда:</strong> <?= $row['from_address'] ?></p>
                    <p><strong>Куда:</strong> <?= $row['to_address'] ?></p>
                    <p><strong>Статус:</strong> <?= $row['status'] ?></p>

                    <?php if (empty($row['feedback'])): ?>
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <textarea name="feedback" placeholder="Оставьте отзыв..." required></textarea><br>
                            <input type="submit" value="Отправить отзыв">
                        </form>
                    <?php else: ?>
                        <p><strong>Ваш отзыв:</strong> <?= htmlspecialchars($row['feedback']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}

$message = '';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM requests WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Заявка удалена";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'], $_POST['request_id'])) {
    $id = $_POST['request_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    $message = "Статус обновлён";
}

$stmt = $conn->query("SELECT requests.*, users.fullname FROM requests JOIN users ON requests.user_id = users.id ORDER BY requests.date DESC");
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container">
        <h2>Панель администратора</h2>
        <?php if ($message): ?>
            <p class="success"><?= $message ?></p>
        <?php endif; ?>

        <?php if (count($requests) == 0): ?>
            <p>Заявок нет.</p>
        <?php else: ?>
            <?php foreach ($requests as $row): ?>
                <div class="card">
                    <p><strong>Пользователь:</strong> <?= $row['fullname'] ?></p>
                    <p><strong>Дата:</strong> <?= $row['date'] ?></p>
                    <p><strong>Тип:</strong> <?= $row['cargo_type'] ?></p>
                    <p><strong>Вес:</strong> <?= $row['weight'] ?></p>
                    <p><strong>Габариты:</strong> <?= $row['size'] ?></p>
                    <p><strong>Откуда:</strong> <?= $row['from_address'] ?></p>
                    <p><strong>Куда:</strong> <?= $row['to_address'] ?></p>
                    <p><strong>Статус:</strong> <?= $row['status'] ?></p>
                    <p><strong>Отзыв:</strong> <?= htmlspecialchars($row['feedback']) ?></p>

                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                        <select name="status">
                            <option value="Новая" <?= $row['status'] == 'Новая' ? 'selected' : '' ?>>Новая</option>
                            <option value="В работе" <?= $row['status'] == 'В работе' ? 'selected' : '' ?>>В работе</option>
                            <option value="Отменена" <?= $row['status'] == 'Отменена' ? 'selected' : '' ?>>Отменена</option>
                        </select>
                        <input type="submit" value="Обновить статус">
                    </form>

                    <a class="delete" href="admin.php?delete=<?= $row['id'] ?>" onclick="return confirm('Удалить заявку?');">Удалить</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Комментарий: подключение к базе данных
$host = 'localhost';
$db = 'gruzovozoff';
$user = 'root';
$pass = ''; // Пароль по умолчанию в OpenServer пустой

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Включаем режим ошибок
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
?>

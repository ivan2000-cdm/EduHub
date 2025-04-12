<?php
$host = 'localhost';
$port = '5432';
$dbname = 'eduhub';
$user = 'eduhub_user';
$password = '12345';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    echo "✅ Успешное подключение к базе данных!";
} catch (PDOException $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage();
}

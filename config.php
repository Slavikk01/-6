<?php

$host = "localhost";
$dbname = "yalynkovyi_svit";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Помилка підключення до бази даних.");
}
// Логін адміністратора
define("ADMIN_USERNAME", "admin");

// Хеш пароля буде додано після його створення
define("ADMIN_PASSWORD_HASH", '$2y$12$ScUb1CaJzrAxvtmn4Ht/iudA/GilGxw/3Ts1rOB3vK9m5CTMmpe/q');
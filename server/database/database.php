<?php

require_once 'env.php';

# Configure Database
$host = $_ENV['DB_HOST'];
$db = $_ENV['DB_TITLE'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = 'utf8mb4';

$data_source_name = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($data_source_name, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
<?php

$host = 'loonyhyun.cafe24.com';
//$host = 'localhost';
$db   = 'loonyhyun';
$user = 'loonyhyun';
$pass = 'bodigad0';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$pdo = new PDO($dsn, $user, $pass, $options);
?>
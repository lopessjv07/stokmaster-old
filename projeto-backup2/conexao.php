<?php
$host = 'localhost';
$db   = 'estoque_virtual';
$user = 'root'; // ou outro usuário do seu MySQL
$pass = '';     // sua senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>

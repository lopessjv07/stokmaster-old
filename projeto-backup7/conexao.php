<?php
$conn = new mysqli("localhost", "root", "", "estoque_virtual");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>

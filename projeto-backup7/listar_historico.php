<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$id_empresa = $_SESSION['id_usuario'];
$sql = "SELECT h.*, p.nome, p.codigo 
        FROM historico h 
        JOIN produtos p ON h.produto_id = p.id 
        WHERE p.empresa_id = ? 
        ORDER BY h.data_hora DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_empresa);
$stmt->execute();
$result = $stmt->get_result();

$historico = [];
while ($row = $result->fetch_assoc()) {
    $historico[] = $row;
}

echo json_encode($historico);
$conn->close();
?>
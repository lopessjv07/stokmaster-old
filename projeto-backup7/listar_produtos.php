<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$id_empresa = $_SESSION['id_usuario'];
$sql = "SELECT p.*, GROUP_CONCAT(ip.caminho_imagem) as imagens 
        FROM produtos p 
        LEFT JOIN imagens_produto ip ON p.id = ip.produto_id 
        WHERE p.empresa_id = ? 
        GROUP BY p.id 
        ORDER BY p.data_registro DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao preparar a consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_empresa);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao executar a consulta: ' . $conn->error]);
    exit;
}

$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $row['imagens'] = $row['imagens'] ? explode(',', $row['imagens']) : [];
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>
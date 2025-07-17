<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do produto não fornecido']);
    exit;
}

$conn->begin_transaction();

try {

    $checkStmt = $conn->prepare("SELECT empresa_id FROM produtos WHERE id = ? AND empresa_id = ?");
    $checkStmt->bind_param("ii", $id, $_SESSION['id_usuario']);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows === 0) {
        throw new Exception("Produto não encontrado ou não pertence à empresa.");
    }

    $deleteImgStmt = $conn->prepare("DELETE FROM imagens_produto WHERE produto_id = ?");
    $deleteImgStmt->bind_param("i", $id);
    $deleteImgStmt->execute();

    $deleteStmt = $conn->prepare("DELETE FROM produtos WHERE id = ? AND empresa_id = ?");
    $deleteStmt->bind_param("ii", $id, $_SESSION['id_usuario']);
    $deleteStmt->execute();

    $conn->commit();
    echo json_encode(['success' => 'Produto excluído com sucesso!']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Excluindo Produto: ' . $e->getMessage()]);
}

$conn->close();
?>
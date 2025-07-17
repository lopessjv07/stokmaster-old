<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$codigo = $data['codigo'] ?? '';
$quantidade = (int)($data['quantidade'] ?? 0);
$comprador = $data['comprador'] ?? '';
$data_saida = date('Y-m-d H:i:s');

if (empty($codigo) || $quantidade <= 0 || empty($comprador)) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT id, quantidade FROM produtos WHERE codigo = ? AND empresa_id = ?");
    $stmt->bind_param("ii", $codigo, $_SESSION['id_usuario']);
    $stmt->execute();
    $stmt->bind_result($produto_id, $estoque);
    $stmt->fetch();
    $stmt->close();

    if ($estoque >= $quantidade) {
        $novoEstoque = $estoque - $quantidade;

        // Atualizar estoque
        $stmt_update = $conn->prepare("UPDATE produtos SET quantidade = ? WHERE id = ?");
        $stmt_update->bind_param("ii", $novoEstoque, $produto_id);
        $stmt_update->execute();

        // Registrar saída
        $stmt_saida = $conn->prepare("INSERT INTO saidas (produto_id, quantidade, nome_comprador, data_saida) VALUES (?, ?, ?, ?)");
        $stmt_saida->bind_param("iiss", $produto_id, $quantidade, $comprador, $data_saida);
        $stmt_saida->execute();

        // Registrar no histórico
        $tipo = 'saida';
        $stmt_hist = $conn->prepare("INSERT INTO historico (produto_id, tipo, quantidade, data_hora) VALUES (?, ?, ?, ?)");
        $stmt_hist->bind_param("issi", $produto_id, $tipo, $quantidade, $data_saida);
        $stmt_hist->execute();

        $conn->commit();
        echo json_encode(['success' => 'Saída registrada com sucesso!']);
    } else {
        $conn->rollback();
        http_response_code(400);
        echo json_encode(['error' => 'Estoque insuficiente']);
    }
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao registrar saída: ' . $e->getMessage()]);
}

$conn->close();
?>
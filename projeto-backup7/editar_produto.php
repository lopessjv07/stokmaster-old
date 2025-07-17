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

$id_empresa = $_SESSION['id_usuario'];
$id = $_POST['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do produto não fornecido']);
    exit;
}

$nome = $conn->real_escape_string($_POST['name'] ?? '');
$fornecedor = $conn->real_escape_string($_POST['fornecedor'] ?? '');
$codigo = $conn->real_escape_string($_POST['codigo'] ?? '');
$preco = (float)($_POST['preco'] ?? 0.00);
$data_entrada = $conn->real_escape_string($_POST['data_entrada'] ?? date('Y-m-d'));
$quantidade = (int)($_POST['quantidade'] ?? 0);
$descricao = $conn->real_escape_string($_POST['descricao'] ?? '');

$conn->begin_transaction();

try {

    $checkStmt = $conn->prepare("SELECT empresa_id FROM produtos WHERE id = ? AND empresa_id = ?");
    $checkStmt->bind_param("ii", $id, $id_empresa);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows === 0) {
        throw new Exception("Produto não encontrado ou não pertence à empresa.");
    }

    $updateStmt = $conn->prepare("UPDATE produtos SET nome = ?, fornecedor = ?, codigo = ?, preco = ?, data_registro = ?, quantidade = ?, descricao = ? WHERE id = ?");
    $updateStmt->bind_param("sssdsiis", $nome, $fornecedor, $codigo, $preco, $data_entrada, $quantidade, $descricao, $id);
    
    if (!$updateStmt->execute()) {
        throw new Exception("Erro ao atualizar o produto: " . $conn->error);
    }

    if (!empty($_POST['imagens_remover'])) {
        $imagens_remover = $_POST['imagens_remover'];
        foreach ($imagens_remover as $caminho) {

            $stmtDel = $conn->prepare("DELETE FROM imagens_produto WHERE produto_id = ? AND caminho_imagem = ?");
            $stmtDel->bind_param("is", $id, $caminho);
            if (!$stmtDel->execute()) {
                throw new Exception("Erro ao excluir imagem do banco: " . $conn->error);
            }

            if (file_exists($caminho)) {
                unlink($caminho);
            }
        }
    }

    if (!empty($_FILES['imagens']['name'][0])) {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

        foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['imagens']['name'][$key]);
            $file_path = $upload_dir . uniqid() . '_' . $file_name;
            if (move_uploaded_file($tmp_name, $file_path)) {
                $sql_img = "INSERT INTO imagens_produto (produto_id, caminho_imagem) VALUES (?, ?)";
                $stmt_img = $conn->prepare($sql_img);
                $stmt_img->bind_param("is", $id, $file_path);
                if (!$stmt_img->execute()) {
                    throw new Exception("Erro ao salvar imagem: " . $conn->error);
                }
            }
        }
    }

    $conn->commit();
    echo json_encode(['success' => 'Produto atualizado com sucesso!']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao atualizar o produto: ' . $e->getMessage()]);
    error_log("Erro em editar_produto.php: " . $e->getMessage()); // Log para depuração
}

$conn->close();
?>

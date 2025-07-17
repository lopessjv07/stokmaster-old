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
$nome = $conn->real_escape_string($_POST['name'] ?? '');
$fornecedor = $conn->real_escape_string($_POST['fornecedor'] ?? '');
$codigo = $conn->real_escape_string($_POST['codigo'] ?? '');
$preco = (float)($_POST['preco'] ?? 0.00);
$data_entrada = $conn->real_escape_string($_POST['data_entrada'] ?? date('Y-m-d'));
$quantidade = (int)($_POST['quantidade'] ?? 0);
$descricao = $conn->real_escape_string($_POST['descricao'] ?? '');

$conn->begin_transaction();

try {
    $sql = "INSERT INTO produtos (empresa_id, nome, fornecedor, codigo, preco, data_registro, quantidade, descricao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssdisi", $id_empresa, $nome, $fornecedor, $codigo, $preco, $data_entrada, $quantidade, $descricao);
    $stmt->execute();
    $produto_id = $conn->insert_id;

    if (!empty($_FILES['imagens']['name'][0])) {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['imagens']['name'][$key]);
            $file_path = $upload_dir . uniqid() . '_' . $file_name;
            if (move_uploaded_file($tmp_name, $file_path)) {
                $sql_img = "INSERT INTO imagens_produto (produto_id, caminho_imagem) VALUES (?, ?)";
                $stmt_img = $conn->prepare($sql_img);
                $stmt_img->bind_param("is", $produto_id, $file_path);
                $stmt_img->execute();
            }
        }
    }

    $tipo = 'entrada';
    $sql_hist = "INSERT INTO historico (produto_id, tipo, quantidade, data_hora) VALUES (?, ?, ?, NOW())";
    $stmt_hist = $conn->prepare($sql_hist);
    $stmt_hist->bind_param("isi", $produto_id, $tipo, $quantidade);
    $stmt_hist->execute();

    $conn->commit();
    echo json_encode(['success' => 'Produto salvo com sucesso!']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar o produto: ' . $e->getMessage()]);
    error_log("Erro em salvar_produtos.php: " . $e->getMessage());
}

$conn->close();
?>
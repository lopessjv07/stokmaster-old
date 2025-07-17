<?php
session_start();
require_once 'inc/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit("Não autorizado.");
}

$nome = $_POST['name'] ?? '';
$preco = $_POST['price'] ?? 0;
$descricao = $_POST['description'] ?? '';
$usuario_id = $_SESSION['usuario_id'];

// Salva o produto
$stmt = $pdo->prepare("INSERT INTO produtos (usuario_id, nome, preco, descricao) VALUES (?, ?, ?, ?)");
$stmt->execute([$usuario_id, $nome, $preco, $descricao]);

$produto_id = $pdo->lastInsertId();

// Verifica se tem imagem
if (!empty($_FILES['image']['tmp_name'])) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $nomeImagem = uniqid("img_") . "." . $ext;
    $caminho = "uploads/" . $nomeImagem;

    move_uploaded_file($_FILES['image']['tmp_name'], $caminho);

    // Salva caminho da imagem
    $stmtImg = $pdo->prepare("INSERT INTO imagens_produto (produto_id, caminho_imagem) VALUES (?, ?)");
    $stmtImg->execute([$produto_id, $caminho]);
}

echo "Produto salvo com sucesso!";

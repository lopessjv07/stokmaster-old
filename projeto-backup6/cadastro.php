<?php
// salvar_cadastro.php

// Conexão com o banco
$conn = new mysqli("localhost", "root", "", "stockmaster");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Pegar os dados do formulário
$empresa = $_POST['empresa'];
$usuario = $_POST['usuario'];
$categoria = $_POST['categoria'];
$cnpj = $_POST['cnpj'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // senha criptografada

// Inserir no banco
$sql = "INSERT INTO empresas_usuarios (nome_empresa, nome_usuario, tipo_negocio, cnpj, email, senha)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $empresa, $usuario, $categoria, $cnpj, $email, $senha);

if ($stmt->execute()) {
    echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login.php';</script>";
} else {
    echo "<script>alert('Erro ao cadastrar: " . $conn->error . "'); history.back();</script>";
}

$conn->close();
?>
<?php
session_start();
require_once 'inc/conexao.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"] ?? '';
    $email = $_POST["email"] ?? '';
    $senha = password_hash($_POST["senha"] ?? '', PASSWORD_DEFAULT);

    $verifica = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verifica->execute([$email]);

    if ($verifica->rowCount() > 0) {
        $erro = "E-mail já cadastrado.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome_usuario, email, senha_hash) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $senha]);

        $_SESSION['usuario_id'] = $pdo->lastInsertId();
        $_SESSION['nome_usuario'] = $nome;
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <style>
        body {
            background-image: linear-gradient(45deg, #ffffff4b, #00458f);
        }
        h1 {
            color: white;
        }
    </style>

    <header>
        <img src="img/3.png" alt="logo" width="200px">
        <div class="nav-index">
        <button><strong><a href="http://127.0.0.1:8080/projeto-backup1/suporte.html">Suporte <i class="fa-solid fa-headset"></i></a></button></strong>
        </div>
    </header>

    <div class="container-login">
        <h1>Cadastro</h1>

        <?php if ($erro): ?>
            <p style="color: red; background: white; padding: 10px; border-radius: 5px;"><?= $erro ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="container-input">
                <input type="text" name="nome" placeholder="usuário" required>
                <input type="email" name="email" placeholder="email" required>
                <input type="password" name="senha" placeholder="senha" required>
                <button type="submit">Cadastrar</button>
                <br>
                <p>Já possui cadastro? <strong><a href="login.php">Clique Aqui!</a></strong></p>
            </div>
        </form>
    </div>
</body>
</html>

<?php
session_start();
require_once 'inc/conexao.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $senha = $_POST["senha"] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
        header("Location: dashboard.php");
        exit;
    } else {
        $erro = "E-mail ou senha inválidos.";
    }
}
?>

<!DOCTYPE html> 
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    body {
        background-image: linear-gradient(45deg, #ffffff4b, #00458f);
    }
    h1 {
        color: white;
    }
</style>
<body>

<header>
    <img src="img/3.png" alt="logo" width="200px">
    <div class="nav-index">
    <button><strong><a href="http://127.0.0.1:8080/projeto-backup1/suporte.html">Suporte <i class="fa-solid fa-headset"></i></a></button></strong>
    </div>
</header>

<div class="container-login">
    <h1>Login</h1>

    <?php if ($erro): ?>
        <p style="color: red; background: white; padding: 10px; border-radius: 5px;"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="container-input">
            <input type="email" name="email" placeholder="email" required>
            <input type="password" name="senha" placeholder="senha" required>
            <button type="submit">Login</button><br>
            <br>
            <p>Não possui cadastro? <strong><a href="cadastro.php">Clique Aqui!</a></strong></p>
        </div>
    </form>
</div>

</body>
</html>

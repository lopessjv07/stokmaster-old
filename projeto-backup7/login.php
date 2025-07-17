<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['user'];
    $senha = $_POST['senha'];

    // Conexão com o banco
    $conn = new mysqli("localhost", "root", "", "estoque_virtual");

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, senha FROM empresas_usuarios WHERE nome_usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $senha_hash);
        $stmt->fetch();

        if (password_verify($senha, $senha_hash)) {
            $_SESSION['id_usuario'] = $id;
            $_SESSION['usuario'] = $usuario;
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-image: linear-gradient(45deg, #ffffff4b, #00458f);
        }
        h1 {
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <img src="img/3.png" alt="logo" width="200px">
        <div class="nav-index">
            <button><strong><a href="http://127.0.0.1:8080/projeto-backup6/login.php/suporte.html">Suporte <i class="fa-solid fa-headset"></i></a></strong></button>
        </div>
    </header>

    <div class="big-container-login">
        <div class="container-login-input">
            <h1>Login</h1>
            <?php if (isset($erro)) echo "<p style='color: red;'><strong>$erro</strong></p>"; ?>

            <form method="POST">
                <input type="text" name="user" placeholder="Nome de usuário" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit"><strong>Login <i class="fa-solid fa-right-to-bracket"></i></strong></button>
            </form>
            <p>Não possui cadastro?<strong><a href="cadastro.php"> Clique Aqui!</a></strong></p>
        </div>
    </div>
</body>
</html>

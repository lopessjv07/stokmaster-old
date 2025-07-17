<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome_empresa = trim($_POST['empresa']);
    $nome_usuario = trim($_POST['user']);
    $tipo_negocio = $_POST['categoria'];
    $cnpj = trim($_POST['cnpj']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($nome_empresa) || empty($nome_usuario) || empty($tipo_negocio) || empty($cnpj) || empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $conn = new mysqli("localhost", "root", "", "estoque_virtual");

        if ($conn->connect_error) {
            die("Erro na conexão: " . $conn->connect_error);
        }

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO empresas_usuarios (nome_empresa, nome_usuario, tipo_negocio, cnpj, email, senha) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome_empresa, $nome_usuario, $tipo_negocio, $cnpj, $email, $senha_hash);

        if ($stmt->execute()) {
            header("Location: login.php?cadastro=sucesso");
            exit;
        } else {
            $erro = "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro da Empresa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-image: linear-gradient(45deg, #ffffff4b, #00458f);
        
        }
        .erro {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <header>
        <img src="img/3.png" alt="logo" width="200px">
        <div class="nav-index">
            <button><strong><a href="http://127.0.0.1:8080/projeto-backup6/suporte.html">Suporte <i class="fa-solid fa-headset"></i></a></strong></button>
        </div>
    </header>

    <div class="container-login">
        <div class="container-input">
            <h1>Cadastro da Empresa</h1>

            <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>

            <form method="POST" action="">
                <input type="text" name="empresa" placeholder="Nome da empresa" required>
                <input type="text" name="user" placeholder="Nome de usuário" required>
                <select name="categoria" required>
                    <option value="">Selecione a categoria</option>
                    <option value="Comércio">Comércio</option>
                    <option value="Serviços">Serviços</option>
                    <option value="Indústria">Indústria</option>
                    <option value="Tecnologia">Tecnologia</option>
                </select>
                <input type="text" name="cnpj" placeholder="CNPJ da empresa" maxlength="18" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit"><strong>Cadastrar</strong></button>
            </form>

            <p>Já possui cadastro? <strong><a href="login.php">Clique Aqui!</a></strong></p>
        </div>
    </div>

</body>
</html>

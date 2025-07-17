<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "stockmaster");
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

$id_usuario = $_SESSION['id_usuario'];

// CAPTURA FILTROS
$codigoFiltro = $_GET['codigo'] ?? '';
$fornecedorFiltro = $_GET['fornecedor'] ?? '';
$nomeFiltro = $_GET['nome'] ?? '';

$sql = "SELECT * FROM produtos WHERE id_usuario = ?";
$params = [$id_usuario];
$types = "i";

if (!empty($codigoFiltro)) {
    $sql .= " AND codigo LIKE ?";
    $params[] = "%$codigoFiltro%";
    $types .= "s";
}
if (!empty($fornecedorFiltro)) {
    $sql .= " AND fornecedor LIKE ?";
    $params[] = "%$fornecedorFiltro%";
    $types .= "s";
}
if (!empty($nomeFiltro)) {
    $sql .= " AND nome LIKE ?";
    $params[] = "%$nomeFiltro%";
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$produtos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Dashboard de Produtos</title>
    <style>
        h1 { color: rgb(0, 33, 94); }
        h2 { color: #002750; }
        #botao-saida { width: 200px; }
        .product-card { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 10px; background: #fff; }
        .image-thumbs img { max-height: 80px; margin: 5px; border-radius: 5px; }
    </style>
</head>

<body>
    <header>
        <img src="/img/3.png" alt="logo" width="200px">
        <div class="nav-index">
            <button><strong><a href="/storage.html">Histórico <i class="fa-solid fa-database"></i></a></strong></button>
            <button><strong><a href="/login.html">Log out <i class="fa-solid fa-right-to-bracket"></i></a></strong></button>
            <button><strong><a href="/suporte.html">Suporte <i class="fa-solid fa-headset"></i></a></strong></button>
        </div>
    </header>

    <div class="big-container">
        <div class="container">
            <h1><i class="fa-solid fa-box"></i> Dashboard de Produtos</h1>

            <div class="button-group">
                <button type="button" onclick="alternarFormulario('produto')">Registro de Produtos</button>
                <button type="button" onclick="alternarFormulario('saida')">Registro de Saída</button>
            </div>

            <!-- FILTROS -->
            <form method="GET" class="filters">
                <input type="text" name="codigo" placeholder="Buscar por código" value="<?= htmlspecialchars($codigoFiltro) ?>">
                <input type="text" name="fornecedor" placeholder="Filtrar por fornecedor" value="<?= htmlspecialchars($fornecedorFiltro) ?>">
                <input type="text" name="nome" placeholder="Filtrar por nome/modelo" value="<?= htmlspecialchars($nomeFiltro) ?>">
                <button type="submit"><i class="fa fa-filter"></i> Filtrar</button>
                <a href="dashboard.php"><button type="button"><i class="fa fa-times"></i> Limpar Filtros</button></a>
            </form>

            <!-- FORMULÁRIO DE REGISTRO DE SAÍDA -->
            <div id="form-saida" style="margin-top: 20px;">
                <form action="registrar_saida.php" method="POST">
                    <input type="text" name="codigo" placeholder="Código do produto" required>
                    <input type="text" name="comprador" placeholder="Nome do comprador" required>
                    <input type="number" name="quantidade_saida" placeholder="Quantidade de saída" required>
                    <input type="date" name="data_saida" required>
                    <button id="botao-saida" type="submit"><strong>Confirmar Saída</strong></button>
                </form>
            </div>

            <h2><i class="fa-solid fa-list-ul"></i> Lista de Produtos</h2>
            <div id="product-list">
                <?php if (empty($produtos)): ?>
                    <p style="color:red;"><strong>Humm... Nenhum produto encontrado :(</strong></p>
                <?php else: ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="product-card">
                            <div class="image-thumbs">
                                <?php
                                $imgs = json_decode($produto['imagens'], true) ?? [];
                                foreach ($imgs as $img): ?>
                                    <img src="<?= $img ?>" alt="Imagem">
                                <?php endforeach; ?>
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                                <p><strong>Fornecedor:</strong> <?= htmlspecialchars($produto['fornecedor']) ?></p>
                                <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <p><strong>Código:</strong> <?= htmlspecialchars($produto['codigo']) ?></p>
                                <p><?= htmlspecialchars($produto['descricao']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
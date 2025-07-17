<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'conexao.php';
$id_usuario = $_SESSION['id_usuario'];

$codigoFiltro = $_GET['codigo'] ?? '';
$fornecedorFiltro = $_GET['fornecedor'] ?? '';
$nomeFiltro = $_GET['nome'] ?? '';

$sql = "SELECT * FROM produtos WHERE empresa_id = ?";
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
    <title>Dashboard de Produtos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        h1 { color: rgb(0, 33, 94); }
        h2 { color: #002750; }
        #botao-saida { width: 200px; }
        .product-card { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 10px; background: #fff; }
        .image-thumbs img { max-height: 80px; margin: 5px; border-radius: 5px; }
        .historico-item { border: 1px solid #ddd; padding: 10px; margin: 5px 0; border-radius: 5px; background: #f9f9f9; }
    </style>
</head>
<body>
    <header>
        <img src="img/3.png" alt="logo" width="200px">
        <div class="nav-index">
            <button><strong><a href="suporte.html">Suporte <i class="fa-solid fa-headset"></i></a></strong></button>
        </div>
    </header>

    <div class="big-container">
        <div class="container">
            <h1><i class="fa-solid fa-box"></i> Dashboard de Produtos</h1>

            <div class="button-group">
                <button type="button" onclick="alternarFormulario('produto')">Registro de Produtos</button>
                <button type="button" onclick="alternarFormulario('saida')">Registro de Saída</button>
            </div>

            <div id="form-produto">
                <form id="product-form">
                    <input type="hidden" id="editIndex" />
                    <input type="text" id="name" placeholder="Nome do produto" required />
                    <input type="text" id="fornecedor" placeholder="Fornecedor" required />
                    <input type="text" id="code-product" placeholder="Código do produto" required />
                    <div class="flex-entrada">
                        <input type="number" id="price" placeholder="Preço" step="any" required />
                        <input type="date" id="date-insert" required />
                        <input type="number" id="quant" placeholder="Quantidade" required />
                    </div>
                    <textarea id="description" placeholder="Descrição" required></textarea>
                    <div class="file">
                        <input class="input-file" type="file" id="image" accept="image/*" multiple>
                    </div>
                    <div id="image-preview" class="image-preview"></div>
                    <button><strong>Salvar Produto</strong></button>
                </form>

                <div class="filters">
                    <input type="text" id="search-code" placeholder="Buscar por código do produto" />
                    <input type="text" id="filter-fornecedor" placeholder="Filtrar por fornecedor" />
                    <input type="text" id="filter-nome" placeholder="Filtrar por nome/modelo" />
                    <button onclick="applyFilters()"><i class="fa fa-filter"></i> Filtrar</button>
                    <button onclick="clearFilters()"><i class="fa fa-times"></i> Limpar Filtros</button>
                    <button id="view-all-btn"><i class="fa fa-eye"></i> Ver Tudo</button>
                </div>
            </div>

            <div id="form-saida" style="display: none;">
                <form onsubmit="event.preventDefault(); registrarSaida();">
                    <input type="text" id="saida-codigo" placeholder="Código do produto" oninput="preencherPorCodigo()" required />
                    <input type="text" id="saida-nome" placeholder="Nome do produto" />
                    <input type="text" id="saida-fornecedor" placeholder="Fornecedor" />
                    <input type="text" id="saida-desc" placeholder="Descrição" />
                    <input type="text" id="saida-comprador" placeholder="Nome do comprador" required />
                    <div class="flex-saida">
                        <input type="text" id="saida-preco" placeholder="Preço" />
                        <input type="date" id="date" required />
                        <input type="number" id="saida-quantidade" placeholder="Quantidade de saída" required />
                    </div>
                    <button id="botao-saida" type="submit"><strong>Confirmar Saída</strong></button>
                </form>
            </div>

            <h2><i class="fa-solid fa-list-ul"></i> Lista de Produtos</h2>
            <div id="product-list"></div>

        </div>
    </div>

    <div id="image-viewer" class="viewer hidden">
        <span class="close" onclick="closeViewer()">×</span>
        <i class="fa-solid fa-chevron-left nav" id="prev-img" onclick="prevImage()"></i>
        <img id="viewer-img" src="" alt="Imagem ampliada">
        <i class="fa-solid fa-chevron-right nav" id="next-img" onclick="nextImage()"></i>
    </div>

    <script src="script/script.js"></script>
</body>
</html>

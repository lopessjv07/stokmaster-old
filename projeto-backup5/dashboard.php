<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// CONEXÃO COM O BANCO
$conn = new mysqli("localhost", "root", "", "stockmaster");
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

// PROCESSA FORMULÁRIO DE CADASTRO DE PRODUTO
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = $_SESSION['id_usuario'];
    $nome = $_POST['name'];
    $fornecedor = $_POST['fornecedor'];
    $codigo = $_POST['codigo'];
    $preco = $_POST['preco'];
    $data_entrada = $_POST['data_entrada'];
    $quantidade = $_POST['quantidade'];
    $descricao = $_POST['descricao'];

    // Upload de imagens
    $imagensSalvas = [];
    if (!empty($_FILES['imagens']['name'][0])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir);
        }

        for ($i = 0; $i < count($_FILES['imagens']['name']) && $i < 7; $i++) {
            $tmpName = $_FILES['imagens']['tmp_name'][$i];
            $name = uniqid() . "_" . $_FILES['imagens']['name'][$i];
            $destino = $uploadDir . $name;

            if (move_uploaded_file($tmpName, $destino)) {
                $imagensSalvas[] = $destino;
            }
        }
    }

    $imagensJson = json_encode($imagensSalvas);

    $stmt = $conn->prepare("INSERT INTO produtos (id_usuario, nome, fornecedor, codigo, preco, data_entrada, quantidade, descricao, imagens) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdisss", $id_usuario, $nome, $fornecedor, $codigo, $preco, $data_entrada, $quantidade, $descricao, $imagensJson);

    if ($stmt->execute()) {
        $msg = "Produto cadastrado com sucesso!";
    } else {
        $msg = "Erro ao salvar: " . $stmt->error;
    }
}
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
        h1 {
            color: rgb(0, 33, 94);
        }

        h2 {
            color: #002750;
        }

        #botao-saida {
            width: 200px;
        }
    </style>
</head>

<body>
    <header>
        <img src="/img/3.png" alt="logo" width="200px">
        <div class="nav-index">
            <button><strong><a href="/storage.html">Histórico  <i class="fa-solid fa-database"></i></a></strong></button>
            <button><strong><a href="http://127.0.0.1:8080/">Log out <i class="fa-solid fa-right-to-bracket"></i></a></strong></button>
            <button><strong><a href="http://127.0.0.1:8080/projeto-backup5/suporte.html">Suporte <i class="fa-solid fa-headset"></i></a></strong></button>
        </div>
    </header>

    <div class="big-container">
        <div class="container">
            <h1><i class="fa-solid fa-box"></i> Dashboard de Produtos</h1>

            <!-- Botões de alternância SEMPRE visíveis -->
            <div class="button-group">
                <button type="button" onclick="alternarFormulario('produto')">Registro de Produtos</button>
                <button type="button" onclick="alternarFormulario('saida')">Registro de Saída</button>
            </div>

            <!-- FORMULÁRIO DE REGISTRO -->
            <div id="form-produto">
                <form id="product-form">
                    <input type="hidden" id="editIndex" />
                    <input type="text" id="name" placeholder="Nome do produto" required />
                    <input type="text" id="fornecedor" placeholder="Fornecedor" required />
                    <input type="text" id="code-product" placeholder="Código do produto" required />
                    <div class="flex-entrada">
                        <input type="number" id="price" placeholder="Preço" step="any" required />
                        <input type="date" id="date-insert" required>
                        <input type="number" id="quant" placeholder="Quantidade" required />
                    </div>
                    <textarea id="description" placeholder="Descrição" required></textarea>
                    <div class="file">
                        <input class="input-file" type="file" id="image" accept="image/*" multiple>
                    </div>
                    <button><strong>Salvar Produto</strong></button>
                </form>

                <div class="filters">
                    <input type="text" id="search-code" placeholder="Buscar por código do produto" />
                    <input type="text" id="filter-fornecedor" placeholder="Filtrar por fornecedor" />
                    <input type="text" id="filter-nome" placeholder="Filtrar por nome/modelo" />
                    <button onclick="applyFilters()"><i class="fa fa-filter"></i> Filtrar</button>
                    <button onclick="clearFilters()"><i class="fa fa-times"></i> Limpar Filtros</button>
                </div>
            </div>

            <div id="form-saida" style="display: none;">
                <form onsubmit="event.preventDefault(); registrarSaida();">
                    <input type="text" id="saida-codigo" placeholder="Código do produto" oninput="preencherPorCodigo()"
                        required>
                    <input type="text" id="saida-nome" placeholder="Nome do produto">
                    <input type="text" id="saida-fornecedor" placeholder="Fornecedor">
                    <input type="text" id="saida-desc" placeholder="Descrição">
                    <input type="text" id="saida-comprador" placeholder="Nome do comprador" required>
                    <div class="flex-saida">
                        <input type="text" id="saida-preco" placeholder="Preço">
                        <input type="date" id="date" required />
                        <input type="number" id="saida-quantidade" placeholder="Quantidade de saída" required>
                    </div>
                    <button id="botao-saida" type="submit"><strong>Confirmar Saída</strong></button>
                </form>
            </div>

            <h2><i class="fa-solid fa-list-ul"></i> Lista de Produtos</h2>
            <div id="product-list"></div>
        </div>
    </div>

    <div id="image-viewer" class="viewer hidden">
        <span class="close" onclick="closeViewer()">&times;</span>
        <i class="fa-solid fa-chevron-left nav" id="prev-img" onclick="prevImage()"></i>
        <img id="viewer-img" src="" alt="Imagem ampliada">
        <i class="fa-solid fa-chevron-right nav" id="next-img" onclick="nextImage()"></i>
    </div>

    <script src="/script/script.js"></script>
</body>

</html>
 DATABASE estoque_virtual;
USE estoque_virtual;

CREATE TABLE empresas_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresa VARCHAR(255) NOT NULL UNIQUE,
    nome_usuario VARCHAR(100) NOT NULL UNIQUE,
    tipo_negocio VARCHAR(100) NOT NULL,
    categoria VARCHAR(100) NULL,
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nome VARCHAR(255),
    fornecedor VARCHAR(255),
    codigo VARCHAR(50) NOT NULL,
    preco DECIMAL(10,2),
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantidade INT DEFAULT 0,
    descricao TEXT,
    FOREIGN KEY (empresa_id) REFERENCES empresas_usuarios(id) ON DELETE CASCADE
);

CREATE TABLE imagens_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    caminho_imagem TEXT,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE saidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    quantidade INT,
    nome_comprador VARCHAR(255),
    data_saida DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacoes TEXT,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    tipo ENUM('entrada', 'saida') NOT NULL,
    quantidade INT,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    responsavel VARCHAR(255),
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);
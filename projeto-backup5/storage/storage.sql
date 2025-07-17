CREATE DATABASE estoque_virtual;
USE estoque_virtual;

CREATE TABLE empresas_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresa VARCHAR(255) NOT NULL UNIQUE,
    nome_usuario VARCHAR(100) NOT NULL UNIQUE,
    categoria VARCHAR(100),
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nome VARCHAR(255),
    fornecedor VARCHAR(255),
    codigo INT NOT NULL,
    preco DECIMAL(10,2),
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantidade INT,
    descricao TEXT,
    FOREIGN KEY (empresa_id) REFERENCES empresas_usuarios(id)
);

CREATE TABLE imagens_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    caminho_imagem TEXT, -- pode ser base64, ou caminho local
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE saidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    quantidade INT,
    nome_comprador VARCHAR(255),
    data_saida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT,
    tipo ENUM('entrada', 'saida') NOT NULL,
    quantidade INT,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

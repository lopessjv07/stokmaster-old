-- Criação do banco de dados
CREATE DATABASE estoque_virtual;
USE estoque_virtual;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL
);

-- Tabela de produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao TEXT,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de imagens dos produtos
CREATE TABLE imagens_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    caminho_imagem VARCHAR(255) NOT NULL,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

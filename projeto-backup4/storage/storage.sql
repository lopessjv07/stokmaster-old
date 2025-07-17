CREATE DATABASE stockmaster;

USE stockmaster;

CREATE TABLE empresas_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresa VARCHAR(255) NOT NULL UNIQUE,
    nome_usuario VARCHAR(100) NOT NULL UNIQUE,
    tipo_negocio VARCHAR(100),
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL, 
    codigo_produto VARCHAR(50) NOT NULL UNIQUE,
    preco DECIMAL(10, 2) NOT NULL,
	quantidade INT NOT NULL DEFAULT 0,
    descricao TEXT,
    FOREIGN KEY (id_fornecedor) REFERENCES fornecedores(id)
);

CREATE TABLE imagens_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_produto INT NOT NULL,
    caminho_imagem VARCHAR(255) NOT NULL, 
    nome_arquivo VARCHAR(255), 
    ordem INT DEFAULT 0, 
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE registros_saida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_produto INT NOT NULL, 
    nome_comprador VARCHAR(255) NOT NULL, 
    data_saida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	quantidade_saida INT NOT NULL, 
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
);
	
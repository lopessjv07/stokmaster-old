CREATE DATABASE stock_master;

USE stock_master;
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nm_produto VARCHAR(50) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao VARCHAR(300) NOT NULL,
    imagem LONGBLOB
);
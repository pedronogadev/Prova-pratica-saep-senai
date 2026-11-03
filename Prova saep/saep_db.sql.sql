CREATE DATABASE IF NOT EXISTS saep_clinica_veterinaria;
USE saep_clinica_veterinaria;

CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);

INSERT INTO usuario (nome, email, senha) VALUES 
('Administrador', 'admin@veterinaria.com', '$2y$10$ExemploHashSenhaAdmin123456'),
('Doutor Carlos', 'carlos@veterinaria.com', '$2y$10$ExemploHashSenhaCarlos123'),
('Atendente Ana', 'ana@veterinaria.com', '$2y$10$ExemploHashSenhaAna1234');

CREATE TABLE tutor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf TEXT NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    endereco VARCHAR(150) NOT NULL
);

INSERT INTO tutor (nome, cpf, telefone, endereco) VALUES 
('Mariana Souza', 'ENC_A1B2C3D4E5_CPF_SIMULADO', '(11) 98888-1111', 'Rua das Flores, 123'),
('Roberto Alves', 'ENC_F6G7H8I9J0_CPF_SIMULADO', '(11) 97777-2222', 'Av. Paulista, 1000'),
('Camila Lima', 'ENC_K1L2M3N4O5_CPF_SIMULADO', '(11) 96666-3333', 'Rua Augusta, 500');

CREATE TABLE pet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raca VARCHAR(50) NOT NULL,
    idade INT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutor(id) ON DELETE CASCADE
);

INSERT INTO pet (tutor_id, nome, especie, raca, idade) VALUES 
(1, 'Mel', 'Cachorro', 'Golden Retriever', 3),
(2, 'Thor', 'Gato', 'Persas', 2),
(3, 'Bob', 'Cachorro', 'Poodle', 5);

CREATE TABLE agendamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    pet_id INT NOT NULL,
    data_agendamento DATETIME NOT NULL,
    ano INT NOT NULL,
    descricao TEXT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutor(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pet(id) ON DELETE CASCADE
);

INSERT INTO agendamento (tutor_id, pet_id, data_agendamento, ano, descricao) VALUES 
(1, 1, '2025-05-10 14:00:00', 2025, 'Consulta de rotina e vacinação anual'),
(2, 2, '2025-08-15 10:30:00', 2025, 'Castração e exames de sangue'),
(3, 3, '2026-01-20 09:00:00', 2026, 'Limpeza de tártaro e check-up dental');
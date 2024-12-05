-- Creazione del database
CREATE DATABASE SkiPassManagement;
USE SkiPassManagement;

-- Tabella Users: gestione degli utenti
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('cliente', 'amministratore', 'operatore') DEFAULT 'cliente',
    date_of_birth DATE,
    phone_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella Skipasses: gestione degli skipass
CREATE TABLE Skipasses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('giornaliero', 'settimanale', 'stagionale', 'orario') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    validity_start_date DATE NOT NULL,
    validity_end_date DATE NOT NULL,
    area VARCHAR(100),
    user_id INT,
    status ENUM('attivo', 'scaduto', 'bloccato') DEFAULT 'attivo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL
);

-- Tabella Transactions: gestione delle transazioni
CREATE TABLE Transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skipass_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('carta di credito', 'PayPal', 'bonifico') NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (skipass_id) REFERENCES Skipasses(id) ON DELETE CASCADE
);

-- Tabella Reports: gestione dei report finanziari e analisi dati
CREATE TABLE Reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('finanziario', 'flusso_visitatori', 'prestazioni_impianti') NOT NULL,
    data JSON NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella FidelityPrograms: gestione dei programmi fedeltà
CREATE TABLE FidelityPrograms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points INT DEFAULT 0,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Tabella Groups: gestione dei gruppi organizzati
CREATE TABLE Groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    leader_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Tabella GroupMembers: associazione utenti-gruppi
CREATE TABLE GroupMembers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES Groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Inserimento dati iniziali di esempio
INSERT INTO Users (name, surname, email, password, role) VALUES
('Mario', 'Rossi', 'mario.rossi@example.com', 'password123', 'cliente'),
('Luca', 'Bianchi', 'luca.bianchi@example.com', 'password123', 'amministratore');

INSERT INTO Skipasses (type, price, validity_start_date, validity_end_date, area, user_id) VALUES
('giornaliero', 30.00, '2024-12-01', '2024-12-01', 'Area A', 1),
('settimanale', 150.00, '2024-12-01', '2024-12-07', 'Area B', 1);

INSERT INTO Transactions (user_id, skipass_id, amount, payment_method) VALUES
(1, 1, 30.00, 'carta di credito'),
(1, 2, 150.00, 'PayPal');

INSERT INTO Reports (type, data) VALUES
('finanziario', '{"totale_vendite": 180.00, "transazioni": 2}'),
('flusso_visitatori', '{"visitatori_totali": 120, "data": "2024-12-01"}');

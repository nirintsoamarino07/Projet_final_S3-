CREATE DATABASE ETU4084_4322_4088;
USE ETU4084_4322_4088;

CREATE TABLE region (
    id_region INT AUTO_INCREMENT PRIMARY KEY,
    nom_region VARCHAR(100) NOT NULL
);

CREATE TABLE ville (
    id_ville INT AUTO_INCREMENT PRIMARY KEY,
    nom_ville VARCHAR(100) NOT NULL,
    id_region INT,
    FOREIGN KEY (id_region) REFERENCES region(id_region)
);

CREATE TABLE type_besoin (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    nom_type VARCHAR(50) NOT NULL
);

CREATE TABLE article (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    nom_article VARCHAR(100) NOT NULL,
    unite VARCHAR(50),
    id_type INT,
    FOREIGN KEY (id_type) REFERENCES type_besoin(id_type)
);

CREATE TABLE besoin (
    id_besoin INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT,
    id_article INT,
    quantite DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville),
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

CREATE TABLE don (
    id_don INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT,
    quantite DECIMAL(10,2) NOT NULL,
    date_don DATE,
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);


CREATE TABLE stock (
    id_stock INT AUTO_INCREMENT PRIMARY KEY,
    id_article INT UNIQUE,
    quantite_stock DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

CREATE TABLE distribution (
    id_distribution INT AUTO_INCREMENT PRIMARY KEY,
    id_besoin INT,
    quantite_donnee DECIMAL(10,2) NOT NULL,
    date_distribution DATE,
    FOREIGN KEY (id_besoin) REFERENCES besoin(id_besoin)
);

-- REGION
INSERT INTO region (nom_region) VALUES
('Analamanga'),
('Atsinanana');

-- VILLE
INSERT INTO ville (nom_ville, id_region) VALUES
('Antananarivo', 1),
('Toamasina', 2);

-- TYPE BESOIN
INSERT INTO type_besoin (nom_type) VALUES
('Nature'),
('Materiaux'),
('Argent');

-- ARTICLE
INSERT INTO article (nom_article, unite, id_type) VALUES
('Riz', 'kg', 1),
('Huile', 'L', 1),
('Tole', 'piece', 2),
('Clou', 'kg', 2),
('Argent', 'Ar', 3);

-- BESOIN
INSERT INTO besoin (id_ville, id_article, quantite) VALUES
(1, 1, 100),      
(1, 5, 500000),    
(2, 1, 200),     
(2, 3, 50);  

-- DON (historique)
INSERT INTO don (id_article, quantite, date_don) VALUES
(1, 300, '2026-02-15'),
(5, 1000000, '2026-02-15'),
(3, 80, '2026-02-15');

-- STOCK (quantité actuelle disponible)
INSERT INTO stock (id_article, quantite_stock) VALUES
(1, 300),
(5, 1000000),
(3, 80);

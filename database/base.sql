CREATE DATABASE IF NOT EXISTS ETU4084_4322_4088
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ETU4084_4322_4088;

CREATE TABLE unite (
    id_unite   INT AUTO_INCREMENT PRIMARY KEY,
    libelle    VARCHAR(50)  NOT NULL,
    symbole    VARCHAR(10)  NOT NULL
);

CREATE TABLE users (
    id_users   INT          AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin', 'operateur') NOT NULL DEFAULT 'operateur',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE region (
    id_region   INT          AUTO_INCREMENT PRIMARY KEY,
    nom_region  VARCHAR(100) NOT NULL
);

CREATE TABLE ville (
    id_ville   INT          AUTO_INCREMENT PRIMARY KEY,
    nom_ville  VARCHAR(100) NOT NULL,
    id_region  INT          NOT NULL,
    FOREIGN KEY (id_region) REFERENCES region(id_region)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE type_besoin (
    id_type    INT         AUTO_INCREMENT PRIMARY KEY,
    nom_type   VARCHAR(50) NOT NULL
);

CREATE TABLE article (
    id_article  INT          AUTO_INCREMENT PRIMARY KEY,
    nom_article VARCHAR(100) NOT NULL,
    id_type     INT          NOT NULL,
    id_unite    INT          NOT NULL,
    FOREIGN KEY (id_type)   REFERENCES type_besoin(id_type)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_unite)  REFERENCES unite(id_unite)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE prix_unitaire (
    id_prix_unitaire INT            AUTO_INCREMENT PRIMARY KEY,
    id_article       INT            NOT NULL,
    prix             DECIMAL(15,2)  NOT NULL CHECK (prix >= 0),
    created_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_prix_unitaire_article (id_article),
    FOREIGN KEY (id_article) REFERENCES article(id_article)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE evenement (
    id_evenement  INT          AUTO_INCREMENT PRIMARY KEY,
    nom_evenement VARCHAR(150) NOT NULL,
    description   TEXT,
    date_debut    DATE         NOT NULL,
    date_fin      DATE,
    id_region     INT,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_region) REFERENCES region(id_region)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE besoin (
    id_besoin           INT            AUTO_INCREMENT PRIMARY KEY,
    id_ville            INT            NOT NULL,
    id_article          INT            NOT NULL,
    id_evenement        INT,
    quantite_demandee   DECIMAL(15,2)  NOT NULL CHECK (quantite_demandee > 0),
    date_saisie         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observations        TEXT,
    FOREIGN KEY (id_ville)       REFERENCES ville(id_ville)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_article)     REFERENCES article(id_article)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_evenement)   REFERENCES evenement(id_evenement)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE don (
    id_don              INT            AUTO_INCREMENT PRIMARY KEY,
    id_article          INT            NOT NULL,
    id_evenement        INT,
    quantite_totale     DECIMAL(15,2)  NOT NULL CHECK (quantite_totale > 0),
    quantite_distribuee DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
    date_reception      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    donateur            VARCHAR(150),
    source              VARCHAR(200),
    observations        TEXT,
    CONSTRAINT chk_distribuee_lte_totale
        CHECK (quantite_distribuee <= quantite_totale),
    FOREIGN KEY (id_article)     REFERENCES article(id_article)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_evenement)   REFERENCES evenement(id_evenement)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE conversion_argent (
    id_conversion        INT            AUTO_INCREMENT PRIMARY KEY,
    id_don_argent        INT            NOT NULL,
    id_article_cible     INT            NOT NULL,
    montant_utilise      DECIMAL(15,2)  NOT NULL CHECK (montant_utilise > 0),
    prix_unitaire        DECIMAL(15,2)  NOT NULL CHECK (prix_unitaire > 0),
    quantite_obtenue     DECIMAL(15,2)  NOT NULL CHECK (quantite_obtenue > 0),
    date_conversion      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversion_don_argent (id_don_argent),
    FOREIGN KEY (id_don_argent) REFERENCES don(id_don)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_article_cible) REFERENCES article(id_article)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE attribution (
    id_attribution      INT            AUTO_INCREMENT PRIMARY KEY,
    id_besoin           INT            NOT NULL,
    id_don              INT            NOT NULL,
    quantite_attribuee  DECIMAL(15,2)  NOT NULL CHECK (quantite_attribuee > 0),
    date_attribution    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observations        TEXT,
    FOREIGN KEY (id_besoin)  REFERENCES besoin(id_besoin)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_don)     REFERENCES don(id_don)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

INSERT INTO region (nom_region) VALUES
('Atsinanana'),
('Vatovavy'),
('Atsimo Atsinanana'),
('Diana'),
('Menabe');

INSERT INTO ville (nom_ville, id_region) VALUES
('Toamasina', 1),
('Mananjary', 2),
('Farafangana', 3),
('Nosy Be', 4),
('Morondava', 5);

INSERT INTO unite (libelle, symbole) VALUES
('Kilogramme', 'kg'),
('Litre', 'L'),
('Unite', 'u'),
('Ariary', 'Ar');

INSERT INTO type_besoin (nom_type) VALUES
('nature'),
('materiel'),
('argent');

INSERT INTO article (nom_article, id_type, id_unite) VALUES
('Riz', 1, 1),
('Eau', 1, 2),
('Huile', 1, 2),
('Haricots', 1, 1),
('Tôle', 2, 3),
('Bâche', 2, 3),
('Clous', 2, 1),
('Bois', 2, 3),
('groupe', 2, 3),
('Argent', 3, 4);

INSERT INTO prix_unitaire (id_article, prix) VALUES
(1, 3000),
(2, 1000),
(3, 6000),
(4, 4000),
(5, 25000),
(6, 15000),
(7, 8000),
(8, 10000),
(9, 6750000),
(10, 1);

INSERT INTO besoin (id_ville, id_article, quantite_demandee, date_saisie) VALUES
(1,1,800,'2026-02-16'),
(1,2,1500,'2026-02-15'),
(1,5,120,'2026-02-16'),
(1,6,200,'2026-02-15'),
(1,10,12000000,'2026-02-16'),
(2,1,500,'2026-02-15'),
(2,3,120,'2026-02-16'),
(2,5,80,'2026-02-15'),
(2,7,60,'2026-02-16'),
(2,10,6000000,'2026-02-15'),
(3,1,600,'2026-02-16'),
(3,2,1000,'2026-02-15'),
(3,6,150,'2026-02-16'),
(3,8,100,'2026-02-15'),
(3,10,8000000,'2026-02-16'),
(4,1,300,'2026-02-15'),
(4,4,200,'2026-02-16'),
(4,5,40,'2026-02-15'),
(4,7,30,'2026-02-16'),
(4,10,4000000,'2026-02-15'),
(5,1,700,'2026-02-16'),
(5,2,1200,'2026-02-15'),
(5,6,180,'2026-02-16'),
(5,8,150,'2026-02-15'),
(5,10,10000000,'2026-02-16'),
(1,9,3,'2026-02-15');

INSERT INTO don (id_article, quantite_totale, date_reception) VALUES
(10,5000000,'2026-02-16'),
(10,3000000,'2026-02-16'),
(10,4000000,'2026-02-17'),
(10,1500000,'2026-02-17'),
(10,6000000,'2026-02-17'),
(1,400,'2026-02-16'),
(2,600,'2026-02-16'),
(5,50,'2026-02-17'),
(6,70,'2026-02-17'),
(4,100,'2026-02-17'),
(1,2000,'2026-02-18'),
(5,300,'2026-02-18'),
(2,5000,'2026-02-18'),
(10,20000000,'2026-02-19'),
(6,500,'2026-02-19'),
(4,88,'2026-02-17');
CREATE DATABASE IF NOT EXISTS ETU4084_4322_4088
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ETU4084_4322_4088;

-- ============================================================
-- 1. TABLE : unite
--    Unités de mesure des articles (kg, litre, tôle, Ar, ...)
-- ============================================================
CREATE TABLE unite (
    id_unite   INT AUTO_INCREMENT PRIMARY KEY,
    libelle    VARCHAR(50)  NOT NULL,   -- ex: "Kilogramme"
    symbole    VARCHAR(10)  NOT NULL    -- ex: "kg"
);

-- ============================================================
-- 2. TABLE : users
--    Utilisateurs de l'application (authentification)
-- ============================================================
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
    nom_type   VARCHAR(50) NOT NULL   -- 'Nature', 'Matériaux', 'Argent'
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
    nom_evenement VARCHAR(150) NOT NULL,         -- ex: "Cyclone Freddy – Mars 2025"
    description   TEXT,
    date_debut    DATE         NOT NULL,
    date_fin      DATE,                          -- NULL si toujours actif
    id_region     INT,                           -- région principalement touchée
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_region) REFERENCES region(id_region)
        ON UPDATE CASCADE ON DELETE SET NULL
);

-- ============================================================
-- 8. TABLE : besoin
--    Besoins saisis par ville (sans identification individuelle)
--    [CORRECTION] quantite_demandee doit être > 0 (CHECK)
-- ============================================================
CREATE TABLE besoin (
    id_besoin           INT            AUTO_INCREMENT PRIMARY KEY,
    id_ville            INT            NOT NULL,
    id_article          INT            NOT NULL,
    id_evenement        INT,                          -- lien optionnel à un événement
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

-- ============================================================
-- 9. TABLE : don
--    Dons collectés (en nature, matériaux ou argent)
--    [AJOUT] quantite_disponible calculée via TRIGGER (voir bas)
--    [CORRECTION] quantite_donnee doit être > 0
-- ============================================================
CREATE TABLE don (
    id_don              INT            AUTO_INCREMENT PRIMARY KEY,
    id_article          INT            NOT NULL,
    id_evenement        INT,                          -- don lié à un événement
    quantite_totale     DECIMAL(15,2)  NOT NULL CHECK (quantite_totale > 0),
    quantite_distribuee DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
    date_reception      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    donateur            VARCHAR(150),                 -- nom du donateur (optionnel)
    source              VARCHAR(200),                 -- ONG, particulier, entreprise…
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


INSERT INTO unite (libelle, symbole) VALUES
    ('Kilogramme',  'kg'),
    ('Litre',       'L'),
    ('Pièce',       'pcs'),
    ('Ariary',      'Ar'),
    ('Tonne',       't'),
    ('Carton',      'ctn');


INSERT INTO type_besoin (nom_type) VALUES
    ('Nature'),
    ('Matériaux'),
    ('Argent');

INSERT INTO article (nom_article, id_type, id_unite) VALUES
    ('Riz',            1, 1),   -- Nature / kg
    ('Huile',          1, 2),   -- Nature / L
    ('Farine',         1, 1),   -- Nature / kg
    ('Sucre',          1, 1),   -- Nature / kg
    ('Tôle',           2, 3),   -- Matériaux / pcs
    ('Clou',           2, 1),   -- Matériaux / kg
    ('Bois de charpente', 2, 3),-- Matériaux / pcs
    ('Aide financière',3, 4);   -- Argent / Ar

-- Prix unitaires (exemples à ajuster)
-- NB: uniquement pour Nature / Matériaux (les prix ne changent pas)
INSERT INTO prix_unitaire (id_article, prix) VALUES
    (1, 4500.00),  -- Riz / kg
    (2, 12000.00), -- Huile / L
    (3, 4000.00),  -- Farine / kg
    (4, 5000.00),  -- Sucre / kg
    (5, 30000.00), -- Tôle / pcs
    (6, 15000.00), -- Clou / kg
    (7, 80000.00); -- Bois de charpente / pcs

-- Utilisateur admin par défaut (mot de passe à hacher côté application)
INSERT INTO users (nom, email, password, role) VALUES
    ('Administrateur BNGRC', 'admin@bngrc.mg', 'HASHED_PASSWORD', 'admin');

-- ============================================================
-- RÉSUMÉ DES TABLES
-- ============================================================
/*
  unite         → Unités de mesure
  users         → Authentification (admin / opérateur)
  region        → Régions géographiques
  ville         → Villes (liées à une région)
  type_besoin   → Nature | Matériaux | Argent
  article       → Articles (riz, tôle, argent…)
  evenement  ★  → Événements (cyclone, inondation…) [AJOUTÉ]
  besoin        → Besoins saisis par ville
  don           → Dons reçus
  attribution ★ → Liaison don ↔ besoin (règle de gestion)
  [TRIGGERS]    → Contrôle stock + mise à jour automatique
  [VUES]        → Dashboard + Stock dons
*/

INSERT INTO region (nom_region) VALUES
    ('Analamanga'),       -- 1
    ('Vakinankaratra'),   -- 2
    ('Itasy'),            -- 3
    ('Bongolava');

    -- 1. Analamanga (Chef-lieu : Antananarivo)
INSERT INTO ville (nom_ville, id_region) VALUES
    ('Antananarivo',    1),
    ('Ambohidratrimo',  1);
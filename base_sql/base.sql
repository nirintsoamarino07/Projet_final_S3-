CREATE DATABASE ETU4088_ETU4322;
USE ETU4088_ETU4322;

CREATE TABLE motos (
    id_moto INT AUTO_INCREMENT PRIMARY KEY,
    modele VARCHAR(100) NOT NULL,
    consommation_litre_100km DECIMAL(5,2) NOT NULL,
    pourcentage_entretien DECIMAL(5,2) NOT NULL
);

CREATE TABLE conducteur (
    id_conducteur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    pourcentage_salaire DECIMAL(5,2) NOT NULL
);
CREATE TABLE trajet (
    id_trajet INT AUTO_INCREMENT PRIMARY KEY,
    id_conducteur INT NOT NULL,
    id_moto INT NOT NULL,
    point_depart VARCHAR(100),
    point_arrivee VARCHAR(100),
    date_heure_debut DATETIME NOT NULL,
    date_heure_fin DATETIME NOT NULL,
    distance_km DECIMAL(6,2) NOT NULL,
    montant_paye DECIMAL(10,2) NOT NULL,
    valide BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_conducteur) REFERENCES conducteur(id_conducteur),
    FOREIGN KEY (id_moto) REFERENCES motos(id_moto)
); 

CREATE TABLE parametre(
    id_parametre INT AUTO_INCREMENT PRIMARY KEY,
    prix_essence DECIMAL(10,2)
);

CREATE TABLE mot_de_passe(
    mdp VARCHAR(10)
);

INSERT INTO motos (modele, consommation_litre_100km, pourcentage_entretien) VALUES
('TVS HLX', 2.0, 10),
('Bajaj Boxer', 2.0, 10),
('G6', 2.0, 10),
('Racing', 2.0, 10), 
('Honda', 1.6, 15),
('G5', 1.6, 15), 
('Milango', 1.3, 11.5),
('Royal', 1.3, 11.5),
('Hartford', 1.3, 11.5),
('Yamaha', 1.3, 11.5);

INSERT INTO conducteur (nom, pourcentage_salaire) VALUES
('Rakoto', 15),
('Marino', 15),
('Josh', 25),
('Nomena', 25), 
('Manoela', 19.5),
('Kevin', 19.5);

INSERT INTO trajet (
    id_conducteur, id_moto,
    point_depart, point_arrivee,
    date_heure_debut, date_heure_fin,
    distance_km, montant_paye, valide
) VALUES
(1, 1, 'Analakely', 'Ankorondrano',
 '2025-12-01 08:00', '2025-12-01 08:30',
 5, 10000, TRUE),

(2, 2, 'Ambohijatovo', 'Itaosy',
 '2025-12-01 09:00', '2025-12-01 09:45',
 8, 15000, TRUE); 

 INSERT INTO parametre (prix_essence)VALUES(5000);
 INSERT INTO mot_de_passe (mdp)VALUES('1234');

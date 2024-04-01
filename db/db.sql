//CAMION

CREATE TABLE camions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    plaque_immatriculation TEXT COLLATE utf8_general_ci,
    capacite INT(11),
    tournee_id INT(11)
);

INSERT INTO camions (plaque_immatriculation, capacite, tournee_id) VALUES ('ABC123', 5000, 1);

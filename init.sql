-- Création de la table 'utilisateur' adaptée pour PostgreSQL
CREATE TABLE IF NOT EXISTS utilisateur (
    id_user SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table 'task' liée à l'utilisateur
-- Inclut désormais les coordonnées du Post-it (X, Y) et sa couleur
CREATE TABLE IF NOT EXISTS task (
    id_task SERIAL PRIMARY KEY,
    id_user INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP,
    pos_x INT DEFAULT 0,
    pos_y INT DEFAULT 0,
    color VARCHAR(20) DEFAULT '#FFEB3B',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user
      FOREIGN KEY(id_user) 
	  REFERENCES utilisateur(id_user)
	  ON DELETE CASCADE
);

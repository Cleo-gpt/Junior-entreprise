-- **************************************************************
-- Schéma de la base de données pour CPNV Gestion de Matériel
-- Version MySQL complète - Prêt pour la production
-- **************************************************************


-- --------------------------------------------------------------
-- Rôles & utilisateurs
-- --------------------------------------------------------------

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(36) NOT NULL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  status ENUM('pending', 'active', 'suspended') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_roles (
  user_id VARCHAR(36) NOT NULL,
  role_id INT NOT NULL,
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, role_id),
  CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_whitelist (
  email VARCHAR(255) NOT NULL PRIMARY KEY,
  starts_at DATE DEFAULT NULL,
  ends_at DATE DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Pré-charger les rôles principaux
INSERT IGNORE INTO roles (name) VALUES
  ('admin'),
  ('responsable'),
  ('enseignant'),
  ('etudiant');

-- Ajout d'un compte administrateur par défaut (mot de passe : Admin@123)
INSERT IGNORE INTO users (id, name, email, password, status)
VALUES (
  'user_admin_default',
  'Administrateur',
  'admin@eduvaud.ch',
  '$2y$10$3132YEMYStP2VhEvbc59Z.ooxrAjnU5qZAH6/YkDqUEw7MyZkr57e',
  'active'
);

INSERT IGNORE INTO user_roles (user_id, role_id)
SELECT 'user_admin_default', id FROM roles WHERE name IN ('admin', 'responsable');

-- --------------------------------------------------------------
-- Matériel
-- --------------------------------------------------------------

CREATE TABLE IF NOT EXISTS materials (
  id VARCHAR(32) NOT NULL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  quantity_total INT NOT NULL DEFAULT 0,
  quantity_available INT NOT NULL DEFAULT 0,
  status ENUM('available', 'unavailable') NOT NULL DEFAULT 'available',
  cover_image VARCHAR(255) DEFAULT NULL,
  gallery JSON DEFAULT NULL,
  tracking_mode ENUM('generic', 'numbered') NOT NULL DEFAULT 'generic',
  identifiers JSON DEFAULT NULL,
  replacement_cost DECIMAL(10,2) DEFAULT NULL,
  damaged_items JSON DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------------
-- Réservations
-- --------------------------------------------------------------

CREATE TABLE IF NOT EXISTS reservations (
  id VARCHAR(36) NOT NULL PRIMARY KEY,
  user_id VARCHAR(36) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  comment TEXT NULL,
  status ENUM('pending', 'approved', 'checked_out', 'returned', 'cancelled') DEFAULT 'pending',
  user_extension_used BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_reservation_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservation_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id VARCHAR(36) NOT NULL,
  material_id VARCHAR(32) NOT NULL,
  quantity INT NOT NULL,
  identifiers JSON DEFAULT NULL,
  return_status VARCHAR(20) DEFAULT NULL,
  return_condition TEXT DEFAULT NULL,
  damaged_identifiers JSON DEFAULT NULL COMMENT 'Liste des identifiants spécifiques endommagés lors du retour',
  CONSTRAINT fk_reservation_item_reservation FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
  CONSTRAINT fk_reservation_item_material FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------------
-- Corbeille et historique
-- --------------------------------------------------------------

CREATE TABLE IF NOT EXISTS trash (
  id INT AUTO_INCREMENT PRIMARY KEY,
  original_id VARCHAR(36),
  type VARCHAR(50),
  data JSON,
  deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------------
-- Emails et notifications
-- --------------------------------------------------------------

CREATE TABLE IF NOT EXISTS emails (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(50),
  to_email VARCHAR(255),
  subject VARCHAR(255),
  body TEXT,
  context JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- **************************************************************
-- Fin du script
-- **************************************************************

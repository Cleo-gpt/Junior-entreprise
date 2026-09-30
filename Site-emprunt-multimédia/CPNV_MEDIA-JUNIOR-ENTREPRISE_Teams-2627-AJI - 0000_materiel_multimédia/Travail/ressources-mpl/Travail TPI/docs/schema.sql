-- -----------------------------------------------------------
-- Schéma de base pour l’application « CPNV Gestion de matériel »
-- Exécuter avec :  mysql -u <user> -p < docs/schema.sql
-- -----------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `cpnv_gestmat`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `cpnv_gestmat`;

-- -----------------------
-- Table : users
-- -----------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` VARCHAR(32) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `status` ENUM('pending','active','disabled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------
-- Table : user_roles
-- -----------------------
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` VARCHAR(32) NOT NULL,
  `role` VARCHAR(32) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role`),
  CONSTRAINT `fk_user_roles_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------
-- Table : student_whitelist
-- -----------------------
CREATE TABLE IF NOT EXISTS `student_whitelist` (
  `email` VARCHAR(255) NOT NULL,
  `starts_at` DATE DEFAULT NULL,
  `ends_at` DATE DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exemple d’import initial (facultatif) :
-- INSERT INTO student_whitelist (email, starts_at, ends_at) VALUES
--   ('alice.durant@eduvaud.ch'),
--   ('benoit.leroy@eduvaud.ch'),
--   ('carla.moreau@eduvaud.ch');

-- -----------------------
-- Table : materials
-- -----------------------
CREATE TABLE IF NOT EXISTS `materials` (
  `id` VARCHAR(64) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `quantity_total` INT NOT NULL DEFAULT 0,
  `quantity_available` INT NOT NULL DEFAULT 0,
  `status` ENUM('available','unavailable') NOT NULL DEFAULT 'available',
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `gallery` JSON DEFAULT NULL,
  `tracking_mode` ENUM('generic','numbered') NOT NULL DEFAULT 'generic',
  `identifiers` JSON DEFAULT NULL,
  `replacement_cost` DECIMAL(10,2) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------
-- Tables de réservations (préparation migration JSON -> SQL)
-- -----------------------
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` VARCHAR(36) NOT NULL,
  `user_id` VARCHAR(32) NOT NULL,
  `status` ENUM('pending','approved','picked_up','returned','cancelled') NOT NULL DEFAULT 'pending',
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `comment` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_reservations_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reservation_items` (
  `reservation_id` VARCHAR(36) NOT NULL,
  `material_id` VARCHAR(64) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`reservation_id`, `material_id`),
  CONSTRAINT `fk_reservation_items_reservation`
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_reservation_items_material`
    FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------
-- Table optionnelle : email_logs (si passage à un stockage SQL des notifications)
-- -----------------------
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(64) NOT NULL,
  `recipient` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `body` LONGTEXT NOT NULL,
  `context` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


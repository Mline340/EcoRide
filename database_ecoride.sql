-- Adminer 5.4.1 MariaDB 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `avis`;
CREATE TABLE `avis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commentaire` varchar(50) NOT NULL,
  `note` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `configuration`;
CREATE TABLE `configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `covoiturage`;
CREATE TABLE `covoiturage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_depart` date NOT NULL,
  `heure_depart` time NOT NULL,
  `lieu_depart` varchar(50) NOT NULL,
  `heure_arrivee` time NOT NULL,
  `lieu_arrivee` varchar(50) NOT NULL,
  `info` varchar(50) NOT NULL,
  `prix_personne` double NOT NULL,
  `voiture_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_28C79E89181A8BA` (`voiture_id`),
  KEY `IDX_28C79E89FB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_28C79E89181A8BA` FOREIGN KEY (`voiture_id`) REFERENCES `voiture` (`id`),
  CONSTRAINT `FK_28C79E89FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `marque`;
CREATE TABLE `marque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `parametre`;
CREATE TABLE `parametre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `propriete` varchar(50) NOT NULL,
  `valeur` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `preference`;
CREATE TABLE `preference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `passager` tinyint(1) DEFAULT NULL,
  `chauffeur` tinyint(1) DEFAULT NULL,
  `pas_chau` tinyint(1) DEFAULT NULL,
  `animaux` tinyint(1) DEFAULT NULL,
  `fumeur` tinyint(1) DEFAULT NULL,
  `nbr_place` int(11) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5D69B053FB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_5D69B053FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `reservation`;
CREATE TABLE `reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `covoiturage_id` int(11) NOT NULL,
  `passager_id` int(11) NOT NULL,
  `date_reservation` datetime NOT NULL,
  `statut` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42C8495562671590` (`covoiturage_id`),
  KEY `IDX_42C8495571A51189` (`passager_id`),
  CONSTRAINT `FK_42C8495562671590` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`id`),
  CONSTRAINT `FK_42C8495571A51189` FOREIGN KEY (`passager_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `date_naissance` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `pseudo` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL,
  `configuration_id` int(11) DEFAULT NULL,
  `code_postal` varchar(5) NOT NULL,
  `api_token` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`),
  UNIQUE KEY `UNIQ_1D1C63B373F32DD8` (`configuration_id`),
  KEY `IDX_1D1C63B3D60322AC` (`role_id`),
  CONSTRAINT `FK_1D1C63B373F32DD8` FOREIGN KEY (`configuration_id`) REFERENCES `configuration` (`id`),
  CONSTRAINT `FK_1D1C63B3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `utilisateur_avis`;
CREATE TABLE `utilisateur_avis` (
  `utilisateur_id` int(11) NOT NULL,
  `avis_id` int(11) NOT NULL,
  PRIMARY KEY (`utilisateur_id`,`avis_id`),
  KEY `IDX_4610C7CAFB88E14F` (`utilisateur_id`),
  KEY `IDX_4610C7CA197E709F` (`avis_id`),
  CONSTRAINT `FK_4610C7CA197E709F` FOREIGN KEY (`avis_id`) REFERENCES `avis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4610C7CAFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `voiture`;
CREATE TABLE `voiture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modele` varchar(50) NOT NULL,
  `immatriculation` varchar(50) NOT NULL,
  `energie` varchar(50) NOT NULL,
  `couleur` varchar(50) NOT NULL,
  `date_premiere_immatriculation` varchar(50) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `marque_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E9E2810FFB88E14F` (`utilisateur_id`),
  KEY `IDX_E9E2810F4827B9B2` (`marque_id`),
  CONSTRAINT `FK_E9E2810F4827B9B2` FOREIGN KEY (`marque_id`) REFERENCES `marque` (`id`),
  CONSTRAINT `FK_E9E2810FFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2026-03-23 16:58:18 UTC
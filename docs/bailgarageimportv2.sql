-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `bailgaragev2` DEFAULT CHARACTER SET utf8mb4;
USE `bailgaragev2`;

-- --------------------------------------------------------
-- Création des tables (sans clés étrangères)
-- --------------------------------------------------------

-- Table additionalibans
CREATE TABLE IF NOT EXISTS `additionalibans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ownerId` int NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table citys
CREATE TABLE IF NOT EXISTS `citys` (
  `id` int NOT NULL,
  `insee_code` int NOT NULL,
  `city_code` varchar(255) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(10,8) DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `department_number` varchar(10) DEFAULT NULL,
  `region_name` varchar(255) DEFAULT NULL,
  `region_geojson_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table garages
CREATE TABLE IF NOT EXISTS `garages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ownerId` int NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int NOT NULL,
  `cityName` varchar(100) NOT NULL DEFAULT '',
  `postalCode` varchar(30) NOT NULL DEFAULT '',
  `country` varchar(50) DEFAULT NULL,
  `garageNumber` int DEFAULT NULL,
  `lotNumber` int NOT NULL,
  `rentWithoutCharges` decimal(15,2) DEFAULT NULL,
  `charges` decimal(15,2) DEFAULT NULL,
  `surface` int DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `attachmentName` varchar(100) NOT NULL,
  `trustee` varchar(100) DEFAULT NULL,
  `caution` decimal(15,2) DEFAULT NULL,
  `comment` text,
  `ownerNote` text,
  `tenantId` int DEFAULT NULL,
  `additionalIbanId` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table leases
CREATE TABLE IF NOT EXISTS `leases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tenantId` int NOT NULL,
  `garageId` int NOT NULL,
  `ownerId` int NOT NULL,
  `madeThe` date DEFAULT NULL,
  `madeIn` varchar(100) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `rentAmount` decimal(15,2) DEFAULT NULL,
  `rentAmountInLetter` varchar(255) DEFAULT NULL,
  `chargesAmount` decimal(15,2) DEFAULT NULL,
  `chargesAmountInLetter` varchar(255) DEFAULT NULL,
  `totalAmountMonthly` decimal(15,2) DEFAULT NULL,
  `totalAmountMonthlyInLetter` varchar(255) DEFAULT NULL,
  `prorata` decimal(15,2) DEFAULT NULL,
  `prorataInLetter` varchar(255) DEFAULT NULL,
  `endProrata` decimal(15,2) DEFAULT NULL,
  `caution` decimal(15,2) DEFAULT NULL,
  `cautionInLetter` varchar(255) DEFAULT NULL,
  `numberOfKey` int DEFAULT NULL,
  `numberOfBeep` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `attachmentPath` varchar(255) DEFAULT NULL,
  `ownerNote` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table owners
CREATE TABLE IF NOT EXISTS `owners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int DEFAULT NULL,
  `cityName` varchar(100) DEFAULT NULL,
  `postalCode` int DEFAULT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  `attachmentPath` varchar(255) DEFAULT NULL,
  `gender` char(5) DEFAULT NULL,
  `adminNote` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table paymenthistories
CREATE TABLE IF NOT EXISTS `paymenthistories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `leasesId` int NOT NULL,
  `paymentId` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `methode` varchar(100) NOT NULL DEFAULT '0',
  `paymentDate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `leaseId` int NOT NULL,
  `monthPayment` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `amount` decimal(15,2) DEFAULT NULL,
  `methodPayment` varchar(50) DEFAULT NULL,
  `ownerNote` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

-- Table tenants
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ownerId` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int DEFAULT NULL,
  `cityName` varchar(100) DEFAULT NULL,
  `postalCode` varchar(30) DEFAULT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `landlinePhoneNumber` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rgpd` tinyint(1) DEFAULT '0',
  `attachmentPath` varchar(255) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT '0',
  `receipt` tinyint(1) DEFAULT '0',
  `ownerNote` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB;

-- Table users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `roleId` int DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `resetToken` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Insertion des données
-- --------------------------------------------------------

-- Données pour additionalibans
INSERT INTO `additionalibans` (`id`, `ownerId`, `name`, `iban`, `bic`) VALUES
    (0, 0, '', NULL, NULL),
    (14, 4, 'test', 'FR6546546546546', '654654544');

-- Données pour citys
INSERT INTO `citys` (`id`, `insee_code`, `city_code`, `zip_code`, `label`, `latitude`, `longitude`, `department_name`, `department_number`, `region_name`, `region_geojson_name`) VALUES
    (1, 68224, 'mulhouse', '68100', 'mulhouse', 47.74899615, 7.32547100, 'haut-rhin', '68', 'grand est', 'Grand Est');

-- Données pour garages
INSERT INTO `garages` (`id`, `ownerId`, `address`, `additionalAddress`, `cityId`, `cityName`, `postalCode`, `country`, `garageNumber`, `lotNumber`, `rentWithoutCharges`, `charges`, `surface`, `reference`, `attachmentName`, `trustee`, `caution`, `comment`, `ownerNote`, `tenantId`, `additionalIbanId`) VALUES
    (7, 4, '3 rue de ala betten 68290', '', 1, '', '', 'France', 50, 2, 65.00, 15.00, 15, '10fdfff', 'test.pdf', 'le sindyc des bg beau', 105.00, '', '', NULL, 0),
    (11, 4, '33 rue des test', 'troisième étage', 1, '', '', 'France', 100, 1, 100.00, 100.00, 100, '32qs1fd3f1', '321.pdf', 'bg', 654.00, '', '', NULL, 0),
    (12, 4, 'test', 'test', 1, '', '', 'etst', 654, 654, 654.00, 654.00, 654, '654', '654654.pdf', 'dd', 100000.00, '', '', NULL, 0),
    (13, 4, '3 rue de ala betten 68290', '', 1, '', '', 'France', 50, 2, 65.00, 15.00, 15, '10fdfff', 'test.pdf', 'le sindyc des bg beau', 105.00, '', '', NULL, NULL),
    (14, 4, '3 rue de ala betten 68290', '', 1, '', '', 'France', 50, 2, 65.00, 15.00, 15, '10fdfff', 'test.pdf', 'le sindyc des bg beau', 105.00, '', '', NULL, 14),
    (15, 4, '3 rue de la husmate', '', 1, 'Bourbach le haut', '68280', 'France', 654, 0, 654.00, 654.00, 654, '65', '44.fr', '5', 5.00, '', '', NULL, 0);

-- Données pour leases
INSERT INTO `leases` (`id`, `tenantId`, `garageId`, `ownerId`, `madeThe`, `madeIn`, `startDate`, `endDate`, `duration`, `rentAmount`, `rentAmountInLetter`, `chargesAmount`, `chargesAmountInLetter`, `totalAmountMonthly`, `totalAmountMonthlyInLetter`, `prorata`, `prorataInLetter`, `endProrata`, `caution`, `cautionInLetter`, `numberOfKey`, `numberOfBeep`, `status`, `attachmentPath`, `ownerNote`) VALUES
    (3, 5, 11, 4, '2025-03-27', 'Mulhouse', '2025-03-27', NULL, 13, 65.00, 'soixante-cinq', 45.00, 'quarante-cinq', 110.00, 'cent dix', 17.74, 'dix-sept virgule sept quatre', NULL, 161.00, 'cent soixante-et-un', 45, 45, 1, 'Index.pdf', ''),
    (5, 2, 7, 4, '2025-03-31', 'Mulhouse', '2025-04-15', NULL, 12, 15.00, 'quinze', 15.00, 'quinze', 30.00, 'trente', 16.00, 'seize', NULL, 1000.00, 'mille', 10, 10, 1, 'test.pdf', '');

-- Données pour owners
INSERT INTO `owners` (`id`, `userId`, `company`, `address`, `additionalAddress`, `cityId`, `cityName`, `postalCode`, `phoneNumber`, `iban`, `bic`, `attachmentPath`, `gender`, `adminNote`) VALUES
    (0, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
    (4, 2, '', '7 db des nations', '', 1, 'Mulhouse', 68100, '0687654', 'FR97654654654654', '6546546654', './signaturef.pdf', 'homme', NULL),
    (33, 110, 'mairie de roderen', '3 rue de de', '', 1, 'bourbach', 68290, '06546565', 'FR654654654654644', '65465465465', 'FR.fr', 'homme', NULL);

-- Données pour paymenthistories
INSERT INTO `paymenthistories` (`id`, `leasesId`, `paymentId`, `amount`, `methode`, `paymentDate`) VALUES
    (3, 3, NULL, 15.00, '0', '2025-04-25'),
    (6, 3, NULL, 0.00, 'Chèque', '0100-10-10');

-- Données pour payments
INSERT INTO `payments` (`id`, `leaseId`, `monthPayment`, `status`, `amount`, `methodPayment`, `ownerNote`) VALUES
    (2, 3, '2025-04-01', 1, 0.00, 'Virement', ''),
    (3, 5, '2025-04-01', 1, 10.00, 'Espèces', ''),
    (4, 5, '2025-04-01', 0, 0.00, 'Virement', '');

-- Données pour roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
    (1, 'admin', 'ce role a accès a tout les propriétaire'),
    (2, 'owner', 'chaque propriétaire a un compte');

-- Données pour tenants
INSERT INTO `tenants` (`id`, `ownerId`, `name`, `firstName`, `company`, `address`, `additionalAddress`, `cityId`, `cityName`, `postalCode`, `phoneNumber`, `landlinePhoneNumber`, `email`, `rgpd`, `attachmentPath`, `gender`, `receipt`, `ownerNote`) VALUES
    (2, 4, 'THUET', 'Delphine', NULL, '3 rue de la betten', 'mlskqsdfdfj', 1, NULL, NULL, '03 27 20 95 92', '03 69 19 23 62', 'delphine68@sfr.fr', 1, 'piecejointe.pdf', 1, 1, ''),
    (5, 4, 'THUET', 'Matthieu le bg', 'iosfosdpiodiouopiduiopfuiopuio', '3 3r', '654', 1, NULL, NULL, '654', '654', 'mattmatt.f@ff.qsdf', 0, '645', 1, 0, '');

-- Données pour users
INSERT INTO `users` (`id`, `name`, `firstName`, `email`, `password`, `roleId`, `createdAt`, `updatedAt`, `resetToken`) VALUES
    (0, 'default', 'default', 'default@bailgarage.fr', NULL, NULL, '2025-04-04 09:20:31', '2025-04-04 09:22:16', NULL),
    (1, 'THUET', 'Matthieu', 'admin@bailgarage.fr', '$argon2i$v=19$m=65536,t=4,p=1$Z09nRG9EcFo3S1lwN2V2TQ$HfRNtJHNt11satVRovHpbPDtw7D2XwkyLHS09uq2FyQ', 1, '2025-03-10 09:53:51', '2025-04-28 09:14:39', NULL),
    (2, 'PROPRIO', 'proprio', 'proprio@bailgarage.fr', '$argon2i$v=19$m=65536,t=4,p=1$MDJMYXBpczlpbmxvcjVlMQ$tOKqirPygJMRkbAt2zeaMXBg7vdQLY1TQqYzs6+7EVM', 2, '2025-03-10 15:59:44', '2025-04-27 23:05:18', NULL),
    (110, 'THUET', 'delphine', 'mattmatt.thuet@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$SHY2YUFiMkwxbWIvWTV4cA$ZF5D2najKdyI/U7cPjMuDVr2y7N4Pfile6z+CH+1DRs', 2, '2025-04-28 08:27:58', '2025-04-28 09:11:56', NULL);

-- --------------------------------------------------------
-- Création des clés étrangères
-- --------------------------------------------------------

-- Clés étrangères pour la table additionalibans
ALTER TABLE `additionalibans`
  ADD CONSTRAINT `fk_additionalIban_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`);

-- Clés étrangères pour la table garages
ALTER TABLE `garages`
  ADD CONSTRAINT `fk_garages_additionalibans` FOREIGN KEY (`additionalIbanId`) REFERENCES `additionalibans` (`id`),
  ADD CONSTRAINT `fk_garages_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`),
  ADD CONSTRAINT `fk_garages_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`);

-- Clés étrangères pour la table leases
ALTER TABLE `leases`
  ADD CONSTRAINT `fk_leases_garage` FOREIGN KEY (`garageId`) REFERENCES `garages` (`id`),
  ADD CONSTRAINT `fk_leases_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`),
  ADD CONSTRAINT `fk_leases_tenant` FOREIGN KEY (`tenantId`) REFERENCES `tenants` (`id`);

-- Clés étrangères pour la table owners
ALTER TABLE `owners`
  ADD CONSTRAINT `fk_owner_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`),
  ADD CONSTRAINT `fk_owner_user` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

-- Clés étrangères pour la table paymenthistories
ALTER TABLE `paymenthistories`
  ADD CONSTRAINT `paymenthistories_ibfk_1` FOREIGN KEY (`leasesId`) REFERENCES `leases` (`id`),
  ADD CONSTRAINT `paymenthistories_ibfk_2` FOREIGN KEY (`paymentId`) REFERENCES `payments` (`id`);

-- Clés étrangères pour la table payments
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_lease` FOREIGN KEY (`leaseId`) REFERENCES `leases` (`id`);

-- Clés étrangères pour la table tenants
ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_tenant_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`),
  ADD CONSTRAINT `fk_tenants_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`);

-- Clés étrangères pour la table users
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`);
-- Database usage
USE `bailgaragev2`;

-- TABLE CREATION SECTION --

-- Table structure for roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

-- Table structure for users
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

-- Table structure for citys
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

-- Table structure for owners
CREATE TABLE IF NOT EXISTS `owners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int DEFAULT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  `attachmentPath` varchar(255) DEFAULT NULL,
  `gender` char(5) DEFAULT NULL,
  `adminNote` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table structure for additionalibans
CREATE TABLE IF NOT EXISTS `additionalibans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ownerId` int NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Table structure for tenants
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ownerId` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int DEFAULT NULL,
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

-- Table structure for garages
CREATE TABLE IF NOT EXISTS `garages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ownerId` int NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int NOT NULL,
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

-- Table structure for leases
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

-- Table structure for payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `leaseId` int NOT NULL,
  `monthPayment` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `amount` decimal(15,2) DEFAULT NULL,
  `_date` date DEFAULT NULL,
  `methodPayment` varchar(50) DEFAULT NULL,
  `ownerNote` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- DATA INSERTION SECTION --

-- Data for table roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
	(1, 'admin', 'ce role a accès a tout les propriétaire'),
	(2, 'owner', 'chaque propriétaire a un compte');

-- Data for table users
INSERT INTO `users` (`id`, `name`, `firstName`, `email`, `password`, `roleId`, `createdAt`, `updatedAt`, `resetToken`) VALUES
	(1, 'ADMINISTRATEUT', 'Admin', 'admin@bailgarage.fr', '$2y$10$vRGg5Q/38/rNgehSj683LeFOHUbz0YKV6shsJfhiOAvrKHk0POe36', 1, '2025-03-10 09:53:51', '2025-03-10 09:53:51', NULL),
	(2, 'PROPRIO', 'proprio', 'proprio@bailgarage.fr', '$2y$10$K70bwmY.SlC5Rw9sg/SXv.WcyizmmTuOkCGeiMnK0ADHHadvFW.Ca', 2, '2025-03-10 15:59:44', '2025-03-10 16:00:51', NULL),
	(87, 'thuet', 'matthieuf', 'mattmatt.thut@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$Z0o2bjdLQkpDU0NQbTNmTQ$Tf9ejugGMDEM/FhN/Jeym4NUDm11QSz+iBnb1baVJ7E', 2, '2025-03-19 10:07:11', '2025-03-19 10:30:39', NULL),
	(88, 'mqsldkfj', 'qsdmlfkmld', 'dqsfl@f.f', '$argon2i$v=19$m=65536,t=4,p=1$TGxNbmxGZGp2V2hGMGJLdA$8R+6Uco34d8ey/0Zw+mE10HkKKwgDtsz+V1vEJLo/XM', 2, '2025-03-19 10:52:30', '2025-03-19 10:52:30', NULL),
	(89, 'thuet', 'mathieu', 'mattmatt.thuet@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$azBTS045TnJqcldoNVBnWg$UIstys+HtLPhkh4k+BXGq6/TAwWVN36ffPkse9W4njw', 2, '2025-03-20 12:09:51', '2025-03-20 12:09:51', NULL),
	(93, 'nom', 'prenom', 'mafilf@mail.com', '$argon2i$v=19$m=65536,t=4,p=1$MEJPN096VWh3OUV2Uy9OYQ$L/5He4AVPVut0kNpeya7TxHfMH8wnycEdQxUneuSJdU', 2, '2025-03-26 10:54:43', '2025-03-26 10:54:43', NULL),
	(94, 'nom', 'prenom', 'mffafilf@mail.com', '$argon2i$v=19$m=65536,t=4,p=1$N3ZOWUJSMS9wVzl1WTJwVg$EbuDPsSzy5KvYEYLVwt7/QITKDuwWQo9dyc437GJWRY', 2, '2025-03-26 10:58:44', '2025-03-26 10:58:44', NULL),
	(95, 'nom', 'prenom', 'mffqsdfafilf@mail.com', '$argon2i$v=19$m=65536,t=4,p=1$ZmVhcDN5Z3lJNDVVNUtDVg$hLpRAyznjqZUH9WH+73SLKJGEiRaB8N2N9l2gTvFTvE', 2, '2025-03-26 10:59:50', '2025-03-26 10:59:50', NULL),
	(96, 'nom', 'prenom', 'mffqsdfaqsdffilf@mail.com', '$argon2i$v=19$m=65536,t=4,p=1$YjguMUcwbjAuSk1KeDZkMw$zplcRF0XJhZ4CgSZ4GyM2fBSgUieCOtEiBcxewCYH30', 2, '2025-03-26 11:00:55', '2025-03-26 11:00:55', NULL),
	(97, 'test', 'test', 'test@test.fr', '$argon2i$v=19$m=65536,t=4,p=1$cmpSUDM3UUM0ckdrcjBDSg$LiY8dobTUCYMp5F/2PoRKBi8w4VMzaEHaP6pKj4gabs', 2, '2025-04-01 00:29:17', '2025-04-01 00:29:17', NULL),
	(98, 'test', 'test', 'test@test.ffr', '$argon2i$v=19$m=65536,t=4,p=1$ZjRLSzBjdmxoT2JSWnNWYw$TLOtc2EBdtUSKQYgS07kY4Tf65lZqhn4bFsX2yOUpnU', 2, '2025-04-01 01:48:39', '2025-04-01 01:48:39', NULL),
	(99, 'test', 'test', 'teqsdfst@test.ffr', '$argon2i$v=19$m=65536,t=4,p=1$SHEvSm5oejBzUXFrMy5iWg$XFe1qjtZcv2LGWq7EPN08WItkXAqhUMDC6oV3AFasNc', 2, '2025-04-01 01:51:34', '2025-04-01 01:51:34', NULL);

-- Data for table citys
INSERT INTO `citys` (`id`, `insee_code`, `city_code`, `zip_code`, `label`, `latitude`, `longitude`, `department_name`, `department_number`, `region_name`, `region_geojson_name`) VALUES
	(1, 68224, 'mulhouse', '68100', 'mulhouse', 47.74899615, 7.32547100, 'haut-rhin', '68', 'grand est', 'Grand Est');

-- Data for table owners
INSERT INTO `owners` (`id`, `userId`, `company`, `address`, `additionalAddress`, `cityId`, `phoneNumber`, `iban`, `bic`, `attachmentPath`, `gender`, `adminNote`) VALUES
	(4, 2, NULL, '7 db des nations', NULL, 1, '0687654', '456', '55', './signaturef.pdf', 'homme', NULL),
	(23, 89, 'crm', '3 rue de la betten', '', 1, '0627209592', 'FR7644444441111', '654654615', '6546546546544', 'homme', NULL),
	(24, 93, '', 'adresse', 'complément d\'adresse', 1, '06654', 'FR76654654654654', '65465454', 'PIECE JOINTE', 'homme', NULL),
	(25, 94, '', 'adresse', 'complément d\'adresse', 1, '06654', 'FR76654654654654', '65465454', 'PIECE JOINTE', 'homme', NULL),
	(26, 95, '', 'adresse', 'complément d\'adresse', 1, '06654', 'FR76654654654654', '65465454', 'PIECE JOINTE', 'homme', NULL),
	(27, 96, '', 'adresse', 'complément d\'adresse', 1, '06654', 'FR76654654654654', '65465454', 'PIECE JOINTE', 'homme', NULL),
	(28, 97, 'crm', '3 test testmlkj', 'mlkjlmkjmlkjmlkjml', 1, '0627209592', 'FR76654654654654', '4565464456', 'sqdf', 'homme', NULL),
	(29, 98, 'crm', '3 test testmlkj', 'mlkjlmkjmlkjmlkjml', 1, '0627209592', 'FR76654654654654', '4565464456', 'sqdf', 'homme', NULL),
	(30, 99, 'crm', '3 test testmlkj', 'mlkjlmkjmlkjmlkjml', 1, '0627209592', 'FR76654654654654', '4565464456', 'sqdf', 'homme', NULL);

-- FOREIGN KEY CONSTRAINTS SECTION --

-- Add foreign key for users table
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`);

-- Add foreign keys for owners table
ALTER TABLE `owners`
  ADD CONSTRAINT `fk_owner_user` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_owner_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`);

-- Add foreign key for additionalibans table
ALTER TABLE `additionalibans`
  ADD CONSTRAINT `fk_additionalIban_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`);

-- Add foreign keys for tenants table
ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_tenant_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`),
  ADD CONSTRAINT `fk_tenants_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`);

-- Add foreign keys for garages table
ALTER TABLE `garages`
  ADD CONSTRAINT `fk_garages_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`),
  ADD CONSTRAINT `fk_garages_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`),
  ADD CONSTRAINT `fk_garages_additionalibans` FOREIGN KEY (`additionalIbanId`) REFERENCES `additionalibans` (`id`);

-- Add foreign keys for leases table
ALTER TABLE `leases`
  ADD CONSTRAINT `fk_leases_tenant` FOREIGN KEY (`tenantId`) REFERENCES `tenants` (`id`),
  ADD CONSTRAINT `fk_leases_garage` FOREIGN KEY (`garageId`) REFERENCES `garages` (`id`),
  ADD CONSTRAINT `fk_leases_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`);

-- Add foreign key for payments table
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_lease` FOREIGN KEY (`leaseId`) REFERENCES `leases` (`id`);
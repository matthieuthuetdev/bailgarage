CREATE TABLE IF NOT EXISTS `additionaliban` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ownerId` int NOT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `leases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tenantId` int NOT NULL,
  `garageId` int NOT NULL,
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
  `status` tinyint(1) DEFAULT '0',
  `attachmentPath` varchar(255) DEFAULT NULL,
  `ownerNote` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

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

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3;

CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int NOT NULL AUTO_INCREMENT,
  'ownerId' int
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


ALTER TABLE `additionaliban` ADD CONSTRAINT `fk_additionalIban_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`);

ALTER TABLE `garages` ADD CONSTRAINT `fk_garages_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`),
ADD CONSTRAINT `fk_garages_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`);

ALTER TABLE `leases` ADD CONSTRAINT `fk_leases_tenant` FOREIGN KEY (`tenantId`) REFERENCES `tenants` (`id`),
ADD CONSTRAINT `fk_leases_garage` FOREIGN KEY (`garageId`) REFERENCES `garages` (`id`);

ALTER TABLE `owners` ADD CONSTRAINT `fk_owner_user` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
ADD CONSTRAINT `fk_owner_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`);

ALTER TABLE `payments` ADD CONSTRAINT `fk_payments_lease` FOREIGN KEY (`leaseId`) REFERENCES `leases` (`id`);

ALTER TABLE `tenants` ADD CONSTRAINT `fk_tenants_city` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`);

ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`);

ALTER TABLE `users` ADD CONSTRAINT `fk_tenant_owner` FOREIGN KEY (`ownerId`) REFERENCES `owners` (`id`);
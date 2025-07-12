-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 18 mai 2025 à 13:25
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bailgaragev2`
--

-- --------------------------------------------------------

--
-- Structure de la table `additionalibans`
--

CREATE TABLE `additionalibans` (
  `id` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `additionalibans`
--

INSERT INTO `additionalibans` (`id`, `ownerId`, `name`, `iban`, `bic`) VALUES
(1, 0, '', NULL, NULL),
(14, 4, 'test', 'FR6546546546546', '654654544');

-- --------------------------------------------------------

--
-- Structure de la table `citys`
--

CREATE TABLE `citys` (
  `id` int(11) NOT NULL,
  `insee_code` int(11) NOT NULL,
  `city_code` varchar(255) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(10,8) DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `department_number` varchar(10) DEFAULT NULL,
  `region_name` varchar(255) DEFAULT NULL,
  `region_geojson_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `citys`
--

INSERT INTO `citys` (`id`, `insee_code`, `city_code`, `zip_code`, `label`, `latitude`, `longitude`, `department_name`, `department_number`, `region_name`, `region_geojson_name`) VALUES
(1, 68224, 'mulhouse', '68100', 'mulhouse', 47.74899615, 7.32547100, 'haut-rhin', '68', 'grand est', 'Grand Est');

-- --------------------------------------------------------

--
-- Structure de la table `emailtemplate`
--

CREATE TABLE `emailtemplate` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `emailtemplate`
--

INSERT INTO `emailtemplate` (`id`, `name`, `subject`, `content`) VALUES
(1, 'newOwner', 'Informations de connexion', '<h1>Informations de connexion</h1>\r\n<h2>Bonjour {{firstName}},</h2><br>\r\n<div>Vous trouverez cidessous vos informations de connexion à votre compte Bailgarage :</div>\r\nVotre adresse Email : {{email}}<br>\r\nVotre mot de passe : {{password}}\r\n<div><b>Il est fortement recommandé de changer le mot de passe fourni, Pour ce faire connectez-vous à votre compte puis rendez-vous sur la page profil et dans le champ mot de passe et confirmer le mot de passe renseigner un nouveau mot de passe plus sécurisé.</b></div>'),
(2, 'tenantForm', 'Formulaire location', '<h1>Formulaire location</h1>\r\n<h2>Bonjour,</h2>\r\n<div>Vous trouverez ci dessous le Lien pour accéder au formulaire de location :\r\n</div><div><ahref=\"{{link}}\">Accdeder au formulaire</a></div><p>ceci est un test !</p>'),
(3, 'requestResetPassword', 'Lien de réinitialisation de votre mot de passe', '<h1>Bonjour {{firstName}},</h1>\r\n<p> Vous trouverez cidessous le lien afin de changer votre mot de passe :</p>\r\n<p><a href=\"localhost/bailgarage/index.php?pageController=user&action=resetpassword&token={{token}}\">Modifier votre mot de passe</a></p>');

-- --------------------------------------------------------

--
-- Structure de la table `garages`
--

CREATE TABLE `garages` (
  `id` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int(11) NOT NULL,
  `cityName` varchar(100) NOT NULL DEFAULT '',
  `postalCode` varchar(30) NOT NULL DEFAULT '',
  `country` varchar(50) DEFAULT NULL,
  `garageNumber` int(11) DEFAULT NULL,
  `lotNumber` int(11) NOT NULL,
  `rentWithoutCharges` decimal(15,2) DEFAULT NULL,
  `charges` decimal(15,2) DEFAULT NULL,
  `surface` int(11) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `attachmentName` varchar(100) NOT NULL,
  `trustee` varchar(100) DEFAULT NULL,
  `caution` decimal(15,2) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `ownerNote` text DEFAULT NULL,
  `tenantId` int(11) DEFAULT NULL,
  `additionalIbanId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `garages`
--

INSERT INTO `garages` (`id`, `ownerId`, `address`, `additionalAddress`, `cityId`, `cityName`, `postalCode`, `country`, `garageNumber`, `lotNumber`, `rentWithoutCharges`, `charges`, `surface`, `reference`, `attachmentName`, `trustee`, `caution`, `comment`, `ownerNote`, `tenantId`, `additionalIbanId`) VALUES
(7, 4, '3 rue de ala betten 68290', '', 1, '', '', 'France', 50, 2, 65.00, 15.00, 15, '10fdfff', 'test.pdf', 'le sindyc des bg beau', 105.00, '', '', NULL, 0),
(11, 4, '33 rue des test', 'troisième étage', 1, '', '', 'France', 100, 1, 100.00, 100.00, 100, '32qs1fd3f1', '321.pdf', 'bg', 654.00, '', '', NULL, 0),
(12, 4, 'test', 'test', 1, '', '', 'etst', 654, 654, 654.00, 654.00, 654, '654', '654654.pdf', 'dd', 100000.00, '', '', NULL, 0),
(13, 4, '3 rue de ala betten 68290', '', 1, '', '', 'France', 50, 2, 65.00, 15.00, 15, '10fdfff', 'test.pdf', 'le sindyc des bg beau', 105.00, '', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `leases`
--

CREATE TABLE `leases` (
  `id` int(11) NOT NULL,
  `tenantId` int(11) NOT NULL,
  `garageId` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `madeThe` date DEFAULT NULL,
  `madeIn` varchar(100) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
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
  `numberOfKey` int(11) DEFAULT NULL,
  `numberOfBeep` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `attachmentPath` varchar(255) DEFAULT NULL,
  `ownerNote` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int(11) DEFAULT NULL,
  `cityName` varchar(100) DEFAULT NULL,
  `postalCode` int(11) DEFAULT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `bic` varchar(11) DEFAULT NULL,
  `attachmentPath` varchar(255) DEFAULT NULL,
  `gender` char(5) DEFAULT NULL,
  `adminNote` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `owners`
--

INSERT INTO `owners` (`id`, `userId`, `company`, `address`, `additionalAddress`, `cityId`, `cityName`, `postalCode`, `phoneNumber`, `iban`, `bic`, `attachmentPath`, `gender`, `adminNote`) VALUES
(1, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, '', '7 db des nations', '', 1, 'Mulhouse', 68100, '0687654', 'FR97654654654654', '6546546654', './signaturef.pdf', 'homme', NULL),
(48, 125, '', '3', '', 1, 'Bourbach-le-bas', 68290, '0627209592', 'FR654654654654654', '65465465', '', 'homme', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `paymenthistories`
--

CREATE TABLE `paymenthistories` (
  `id` int(11) NOT NULL,
  `leasesId` int(11) NOT NULL,
  `paymentId` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `methode` varchar(100) NOT NULL DEFAULT '0',
  `paymentDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `leaseId` int(11) NOT NULL,
  `monthPayment` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `amount` decimal(15,2) DEFAULT NULL,
  `methodPayment` varchar(50) DEFAULT NULL,
  `ownerNote` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `payments`
--

INSERT INTO `payments` (`id`, `leaseId`, `monthPayment`, `status`, `amount`, `methodPayment`, `ownerNote`) VALUES
(2, 3, '2025-04-01', 1, 0.00, 'Virement', ''),
(3, 5, '2025-04-01', 1, 10.00, 'Espèces', ''),
(4, 5, '2025-04-01', 0, 0.00, 'Virement', '');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'ce role a accès a tout les propriétaire'),
(2, 'owner', 'chaque propriétaire a un compte');

-- --------------------------------------------------------

--
-- Structure de la table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `additionalAddress` varchar(255) DEFAULT NULL,
  `cityId` int(11) DEFAULT NULL,
  `cityName` varchar(100) DEFAULT NULL,
  `postalCode` varchar(30) DEFAULT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `landlinePhoneNumber` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rgpd` tinyint(1) DEFAULT 0,
  `attachmentPath` varchar(255) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT 0,
  `receipt` tinyint(1) DEFAULT 0,
  `ownerNote` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tenants`
--

INSERT INTO `tenants` (`id`, `ownerId`, `name`, `firstName`, `company`, `address`, `additionalAddress`, `cityId`, `cityName`, `postalCode`, `phoneNumber`, `landlinePhoneNumber`, `email`, `rgpd`, `attachmentPath`, `gender`, `receipt`, `ownerNote`) VALUES
(28, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mattmatt.thuet@gmail.com', 0, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `roleId` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resetToken` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `firstName`, `email`, `password`, `roleId`, `createdAt`, `updatedAt`, `resetToken`) VALUES
(1, 'THUET', 'Matthieu', 'admin@bailgarage.fr', '$argon2i$v=19$m=65536,t=4,p=1$Nmx6bVg3dVY1VjNmSGFJUw$KAK3KrzazRxDvuEpwPRiCAkcYqEriUtWL88nYR618TI', 1, '2025-03-10 09:53:51', '2025-04-30 09:27:32', NULL),
(2, 'PROPRIO', 'proprio', 'proprio@bailgarage.fr', '$argon2i$v=19$m=65536,t=4,p=1$UHFMNUR4Q0VMY3o2aUE1dg$5xAkO73m6i4QGsS6JhTmelVeBNrklhdAY1CaV2IJWN4', 2, '2025-03-10 15:59:44', '2025-05-06 16:22:32', NULL),
(5, 'default', 'default', 'default@bailgarage.fr', NULL, NULL, '2025-04-04 09:20:31', '2025-04-04 09:22:16', NULL),
(125, 'Matthieu THUET', 'Matthieu', 'mattmatt.thuet@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$eTJrS01kNi8yTkluVUVkSw$JQ5AKJKsEa13rxBlSJI1NaP9Kh/uiXgI57lomKOEWac', 2, '2025-05-12 21:43:38', '2025-05-15 21:17:42', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `additionalibans`
--
ALTER TABLE `additionalibans`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `citys`
--
ALTER TABLE `citys`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `emailtemplate`
--
ALTER TABLE `emailtemplate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `garages`
--
ALTER TABLE `garages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `leases`
--
ALTER TABLE `leases`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `paymenthistories`
--
ALTER TABLE `paymenthistories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `additionalibans`
--
ALTER TABLE `additionalibans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `emailtemplate`
--
ALTER TABLE `emailtemplate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `garages`
--
ALTER TABLE `garages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `leases`
--
ALTER TABLE `leases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT pour la table `paymenthistories`
--
ALTER TABLE `paymenthistories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

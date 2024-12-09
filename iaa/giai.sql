-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 11 nov. 2024 à 09:28
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
-- Base de données : `giai`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id_ad` int(11) NOT NULL,
  `email` varchar(212) NOT NULL,
  `mp` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id_ad`, `email`, `mp`) VALUES
(1, 'administ@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `arrive`
--

CREATE TABLE `arrive` (
  `arrive_id` int(11) NOT NULL,
  `id_p` int(11) DEFAULT NULL,
  `date_arrive` datetime NOT NULL,
   -- `date_depart` datetime NOT NULL,

  `statut` varchar(17) NOT NULL,
  `raison` varchar(50) DEFAULT NULL,
  `nb_abs` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `arrive`
--

INSERT INTO `arrive` (`arrive_id`, `id_p`, `date_arrive`, `statut`, `raison`, `nb_abs`) VALUES
(1, 1, '2024-11-10 14:43:40', '0', '', 6),
(2, 1, '2024-11-10 14:52:21', 'retard', '', 6),
(3, 1, '2024-11-10 14:54:24', 'retard', ' myladie', 6),
(4, 1, '2024-11-10 20:22:00', 'retard', ' myladiennn', 12),
(5, 1, '2024-11-10 20:32:08', 'retard', 'deuil', 12),
(6, 2, '2024-11-10 21:55:46', 'retard', 'prop', 13),
(7, 4, '2024-11-10 22:21:41', 'en retard', 'propre', 14),
(8, 2, '2024-11-10 22:22:58', 'en retard', 'lmm', 14);

-- --------------------------------------------------------

--
-- Structure de la table `personnel`
--

CREATE TABLE `personnel` (
  `id_p` int(11) NOT NULL,
  `photo` longtext NOT NULL,
  `matricule` varchar(11) NOT NULL,
  `nom` varchar(33) NOT NULL,
  `prenom` varchar(33) NOT NULL,
  `email` varchar(55) NOT NULL,
  `departement` varchar(55) NOT NULL DEFAULT 'Mme Amougou',
  `contact` int(11) NOT NULL,
  `date_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_naiss` date DEFAULT NULL,
  `sexe` enum('F','M') NOT NULL,
  `mp` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `personnel`
--

INSERT INTO `personnel` (`id_p`, `photo`, `matricule`, `nom`, `prenom`, `email`, `departement`, `contact`, `date_login`, `date_naiss`, `sexe`, `mp`) VALUES
(1, 'log.jpg', 'L1E', 'Merveille', 'Ange', 'az@gmail.com', 'info', 699983578, '2024-11-10 13:19:41', '0000-00-00', 'F', 'boss1'),
(2, 'WIN_20231204_15_05_52_Pro.jpg', 'SR3A', 'meyouka', 'laurian Mael', 'meyoukalaurian@gmail.com', 'Informatique', 697502708, '2024-11-10 20:49:45', '2003-09-09', '', NULL),
(4, 'WIN_20231204_15_05_34_Pro.jpg', 'SR3A1', 'meyouka18', 'laurian Mael', 'meyoukalauriann@gmail.com', 'Informatique', 65211152, '2024-11-10 21:20:54', '2024-11-24', '', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_ad`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `arrive`
--
ALTER TABLE `arrive`
  ADD PRIMARY KEY (`arrive_id`),
  ADD KEY `id_p` (`id_p`);

--
-- Index pour la table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`id_p`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_ad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `arrive`
--
ALTER TABLE `arrive`
  MODIFY `arrive_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `id_p` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `arrive`
--
ALTER TABLE `arrive`
  ADD CONSTRAINT `arrive_ibfk_1` FOREIGN KEY (`id_p`) REFERENCES `personnel` (`id_p`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

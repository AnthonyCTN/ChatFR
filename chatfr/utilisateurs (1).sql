-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : mer. 05 juin 2024 à 23:17
-- Version du serveur : 5.7.39
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chatfr`
--

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `biographie` text,
  `profile_image` varchar(255) DEFAULT 'default_profile.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `mot_de_passe`, `email`, `biographie`, `profile_image`) VALUES
(1, 'antho', '$2y$10$andyGRulXhDs6wY4qmSAIewuM3yPe0mvl949VtkB9Mp3BDVwYwezy', 'cantone@gmail.com', 'azazaz', 'default_profile.png'),
(2, 'az', '$2y$10$gE6T7lGlXd.QN42XsNN1Ne5HP/VJZpJD6IWTwrbx2.hwmfyt720JS', 'azerty@gmail.com', 'az\r\n', 'default_profile.png'),
(3, 'aq', '$2y$10$kwdRIEOdmFoQjwDPXk.uH.1cwxozlInONRANssH5PSl5jJ9VlO9e2', 'cantonepro5@gmail.com', NULL, 'default_profile.png'),
(4, 'goat', '$2y$10$HFIAEgbFz7N9it9naKTD7u.jhUqWebATxl5ocgT9/ehsXuo0Wuth.', 'azrty@gmail.com', NULL, 'default_profile.png');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Structure de la table `secteur_agri_autre_chauffeurs`
--

CREATE TABLE `secteur_agri_autre_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour la table `secteur_agri_autre_chauffeurs`
--
ALTER TABLE `secteur_agri_autre_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `secteur_options_sector_id_foreign` (`company_id`);
  ADD KEY `secteur_options_company_id_foreign` (`company_id`);

--
-- AUTO_INCREMENT pour la table `secteur_agri_autre_chauffeurs`
--
ALTER TABLE `secteur_agri_autre_chauffeurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- Contraintes pour la table `secteur_agri_autre_chauffeurs`
--
ALTER TABLE `secteur_agri_autre_chauffeurs`
  ADD CONSTRAINT `secteur_secteur_id_foreign` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE;
  ADD CONSTRAINT `secteur_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Déchargement des données de la table `secteur_agri_autre_chauffeurs`
--

INSERT INTO `secteur_agri_autre_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 1218, NULL, '51', 14, '2024-03-22 16:39:54', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 1264, NULL, '51', 14, '2024-03-22 16:39:54', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 1388, NULL, '51', 14, '2024-03-22 16:39:54', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------
--
-- Structure de la table `secteur_agri_autre_employes`
--

CREATE TABLE `secteur_agri_autre_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour la table `secteur_agri_autre_employes`
--
ALTER TABLE `secteur_agri_autre_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_agri_autre_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_agri_autre_employes_company` (`company_id`);

--
-- Contraintes pour la table `secteur_agri_autre_employes`
--
ALTER TABLE `secteur_agri_autre_employes`
  ADD CONSTRAINT `fk_secteur_agri_autre_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_agri_autre_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- AUTO_INCREMENT pour la table `secteur_agri_autre_employes`
--
ALTER TABLE `secteur_agri_autre_employes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- Déchargement des données de la table `secteur_agri_autre_employes`
--

INSERT INTO `secteur_agri_autre_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, NULL, 20910, '48', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, NULL, 34214, '48', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, NULL, 39270, '48', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, NULL, 48047, '48', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, NULL, 58132, '48', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, NULL, 66994, '48', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_agri_autre_ouvriers`
--

CREATE TABLE `secteur_agri_autre_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour la table `secteur_agri_autre_ouvriers`
--
ALTER TABLE `secteur_agri_autre_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_agri_autre_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_agri_autre_ouvriers_company` (`company_id`);

--
-- Contraintes pour la table `secteur_agri_autre_ouvriers`
--
ALTER TABLE `secteur_agri_autre_ouvriers`
  ADD CONSTRAINT `fk_secteur_agri_autre_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_agri_autre_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- AUTO_INCREMENT pour la table `secteur_agri_autre_ouvriers`
--
ALTER TABLE `secteur_agri_autre_ouvriers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- Déchargement des données de la table `secteur_agri_autre_ouvriers`
--

INSERT INTO `secteur_agri_autre_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1 ', NULL, 594, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2 ', NULL, 865, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3 ', NULL, 1020, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4A', NULL, 1073, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4B', NULL, 1113, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5A', NULL, 1199, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5B', NULL, 1278, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '6A', NULL, 1415, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6B', NULL, 1511, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '7 ', NULL, 1616, NULL, '47', 14, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_agri_ccrc_chauffeurs`
--

CREATE TABLE `secteur_agri_ccrc_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour la table `secteur_agri_ccrc_chauffeurs`
--
ALTER TABLE `secteur_agri_ccrc_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_agri_ccrc_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_agri_ccrc_chauffeurs_company` (`company_id`);

--
-- AUTO_INCREMENT pour la table `secteur_agri_ccrc_chauffeurs`
--
ALTER TABLE `secteur_agri_ccrc_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour la table `secteur_agri_ccrc_chauffeurs`
--
ALTER TABLE `secteur_agri_ccrc_chauffeurs`
  ADD CONSTRAINT `fk_secteur_agri_ccrc_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_agri_ccrc_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Déchargement des données de la table `secteur_agri_ccrc_chauffeurs`
--

INSERT INTO `secteur_agri_ccrc_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 1050, NULL, '46', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 1104, NULL, '46', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 1175, NULL, '46', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_agri_ccrc_employes`
--

CREATE TABLE `secteur_agri_ccrc_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour la table `secteur_agri_ccrc_employes`
--
ALTER TABLE `secteur_agri_ccrc_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_agri_ccrc_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_agri_ccrc_employes_company` (`company_id`);

--
-- AUTO_INCREMENT pour la table `secteur_agri_ccrc_employes`
--
ALTER TABLE `secteur_agri_ccrc_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour la table `secteur_agri_ccrc_employes`
--
ALTER TABLE `secteur_agri_ccrc_employes`
  ADD CONSTRAINT `fk_secteur_agri_ccrc_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_agri_ccrc_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Déchargement des données de la table `secteur_agri_ccrc_employes`
--

INSERT INTO `secteur_agri_ccrc_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, NULL, 16698, '43', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, NULL, 28777, '43', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, NULL, 15281, '43', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, NULL, 41765, '43', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, NULL, 49656, '43', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, NULL, 58736, '43', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_agri_ccrc_ouvriers`
--

CREATE TABLE `secteur_agri_ccrc_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour la table `secteur_agri_ccrc_ouvriers`
--
ALTER TABLE `secteur_agri_ccrc_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_agri_ccrc_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_agri_ccrc_ouvriers_company` (`company_id`);

--
-- AUTO_INCREMENT pour la table `secteur_agri_ccrc_ouvriers`
--
ALTER TABLE `secteur_agri_ccrc_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour la table `secteur_agri_ccrc_ouvriers`
--
ALTER TABLE `secteur_agri_ccrc_ouvriers`
  ADD CONSTRAINT `fk_secteur_agri_ccrc_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_agri_ccrc_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Déchargement des données de la table `secteur_agri_ccrc_ouvriers`
--

INSERT INTO `secteur_agri_ccrc_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', 'MO', 462, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 745, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', 'OS 1A', 869, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4A', 'OS 1B', 901, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4B', 'OS 2A', 939, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5A', 'OS 2B', 1046, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5B', 'OP 1A', 1103, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '6A', 'OP 1B', 1214, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6B', 'OP 2A', 1336, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '7', 'OP2B', 1411, NULL, '42', 13, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------



-- --------------------------------------------------------

--
-- Structure de la table `secteur_assurances_employes`
--

CREATE TABLE `secteur_assurances_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_assurances_employes`
--

INSERT INTO `secteur_assurances_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1ère Catégorie', NULL, NULL, 75000, '60', 18, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2e Catégorie', NULL, NULL, 78588, '60', 18, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3e Catégorie', NULL, NULL, 79355, '60', 18, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4e Catégorie', NULL, NULL, 90461, '60', 18, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5e Catégorie', NULL, NULL, 112682, '60', 18, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6e Catégorie', NULL, NULL, 125102, '60', 18, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7e Catégorie', NULL, NULL, 133512, '60', 18, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_banque_agents`
--

CREATE TABLE `secteur_banque_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_banque_agents`
--

INSERT INTO `secteur_banque_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1ère Class', 'Position', NULL, 133600, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2e Classe', 'Position', NULL, 133985, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3e Classe', 'Position', NULL, 142983, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4e Classe', 'Position', NULL, 146259, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5ème Classe 1', 'Position', NULL, 165049, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5e Classe 2', 'Position', NULL, 165049, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6e Classe', 'Position', NULL, 182780, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7e Classe', 'Position', NULL, 207948, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '8e Classe', 'Position', NULL, 235304, '59', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_banque_employes`
--

CREATE TABLE `secteur_banque_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_banque_employes`
--

INSERT INTO `secteur_banque_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1ère Catégorie', NULL, NULL, 75000, '58', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2e Catégorie', NULL, NULL, 72859, '58', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3e Catégorie', NULL, NULL, 79355, '58', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4e Catégorie', NULL, NULL, 90461, '58', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5e Catégorie', NULL, NULL, 112682, '58', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6e Catégorie', NULL, NULL, 125102, '58', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7e Catégorie', NULL, NULL, 133512, '58', 17, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_batiments`
--

CREATE TABLE `secteur_batiments` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_batiments`
--

INSERT INTO `secteur_batiments` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'SMIG', 'Moins qualifié', 398, 75000, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1A MO', 'Moins qualifié', 438, 76029, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '1B MO B', 'Moins qualifié', 446, 77363, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2 MS', 'Moyennement qualifié', 450, 77848, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A OS 1A', 'Ouvrier spécialisé', 458, 79314, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B OS 1B', 'Ouvrier spécialisé', 478, 82974, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '4A OS 2A', 'Ouvrier qualifié', 483, 83705, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '4B OS 2B', 'Ouvrier qualifié', 509, 88344, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '5A OP 1A', 'Ouvrier professionnel', 516, 89564, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '5B OP 1B', 'Ouvrier professionnel', 544, 94201, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '6A OP 2A', 'Ouvrier hautement qualifié', 557, 96395, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, '6B OP 2B', 'Ouvrier hautement qualifié', 615, 106646, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'HC OP 3', 'Hors catégorie', 826, 143130, '34', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, '3A OS 1A-H1', '3A OS 1A-H1', 462, 80978, '34', 10, '2024-03-27 16:04:39', '2024-03-27 16:04:39', '2');

-- --------------------------------------------------------

--
-- Structure de la table `secteur_batiment_agents`
--

CREATE TABLE `secteur_batiment_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_batiment_agents`
--

INSERT INTO `secteur_batiment_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M1', NULL, 800, 138822, '37', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M2', NULL, 859, 148876, '37', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M3', NULL, 1006, 174485, '37', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M4', NULL, 1102, 191000, '37', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M5', NULL, 1172, 203206, '37', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_batiment_cadres`
--

CREATE TABLE `secteur_batiment_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_batiment_cadres`
--

INSERT INTO `secteur_batiment_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '5A', 'Position', NULL, 178796, '38', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '5B', 'Position', NULL, 201979, '38', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2A', 'Position', NULL, 219709, '38', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2B', 'Position', NULL, 244626, '38', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A', 'Position', NULL, 263886, '38', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B', 'Position', NULL, 395746, '38', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '2B-J7', '2B-J7', NULL, 250000, '38', 10, '2024-03-23 23:40:38', '2024-03-23 23:40:38', NULL),
(8, 'CA15-H2', 'CA15-H2', NULL, 500000, '38', 10, '2025-04-12 02:54:16', '2025-04-12 02:54:16', '2004'),
(9, '3A-H1', '3A-H1', NULL, 300000, '38', 10, '2025-06-17 04:49:20', '2025-06-17 04:49:20', '2'),
(13, 'CA14-H1', 'CA14-H1', NULL, 320001, '38', 10, '2025-06-17 04:52:00', '2025-06-17 04:52:00', '2'),
(14, '5B-H2', '5B-H2', NULL, 210000, '38', 10, '2025-06-17 04:55:32', '2025-06-17 04:55:32', '2');

-- --------------------------------------------------------

--
-- Structure de la table `secteur_batiment_chauffeurs`
--

CREATE TABLE `secteur_batiment_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(250) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_batiment_chauffeurs`
--

INSERT INTO `secteur_batiment_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 483, 83705, '36', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 506, 87610, '36', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 515, 89319, '36', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', 'Conducteur de véhicule de transport en commun', 521, 90296, '36', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_batiment_employes`
--

CREATE TABLE `secteur_batiment_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_batiment_employes`
--

INSERT INTO `secteur_batiment_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '(SMIG)', NULL, 398, 75000, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1', NULL, 434, 75165, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2', NULL, 482, 83461, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3', NULL, 507, 87855, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4', NULL, 551, 95420, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5', NULL, 647, 112260, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6', NULL, 748, 129587, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7A', NULL, 756, 131049, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '7B', NULL, 834, 144472, '35', 10, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_commerce_agents`
--

CREATE TABLE `secteur_commerce_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_commerce_agents`
--

INSERT INTO `secteur_commerce_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '7A', 'M2', 746, 129313, '136', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '7B', 'M3', 785, 135942, '136', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '8A', 'M4', 785, 135942, '136', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '8B', 'M5', 796, 137955, '136', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '8C', '8C', 667, 137955, '136', 12, '2024-04-09 15:03:05', '2024-04-09 15:03:05', NULL),
(6, '9A', '9A', 667, 139660, '136', 12, '2024-04-09 15:03:05', '2024-04-09 15:03:05', NULL),
(7, '9B', '9B', 667, 157121, '136', 12, '2024-04-09 15:03:05', '2024-04-09 15:03:05', NULL),
(8, '7A-H1', '7A-H1', 750, 129998, '136', 12, '2024-05-20 01:21:59', '2024-05-20 01:21:59', '29');

-- --------------------------------------------------------

--
-- Structure de la table `secteur_commerce_cadres`
--

CREATE TABLE `secteur_commerce_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_commerce_cadres`
--

INSERT INTO `secteur_commerce_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '10A', 'Position', 951, 164938, '41', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '10B', 'Position', 1345, 184733, '41', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '10C', 'Position', 1199, 207824, '41', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '11', 'Position', 1332, 230918, '41', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_commerce_employees`
--

CREATE TABLE `secteur_commerce_employees` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_commerce_employees`
--

INSERT INTO `secteur_commerce_employees` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1A (SMIG)', NULL, 384, 75000, '40', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1B', NULL, 481, 83344, '40', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2', NULL, 516, 89375, '40', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3', NULL, 532, 92084, '40', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4', NULL, 574, 99506, '40', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5', NULL, 685, 118737, '40', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6', 'M1', 744, 128907, '40', 12, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '1B-H1', '1B-H1', 500, 83344, '40', 12, '2025-08-11 12:37:19', '2025-08-11 12:37:19', '2129');

-- --------------------------------------------------------

--
-- Structure de la table `secteur_dockers_employes`
--

CREATE TABLE `secteur_dockers_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_dockers_employes`
--

INSERT INTO `secteur_dockers_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1e', 'M.O Transit', 346, NULL, '39', 11, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2e', 'M.S Dockers', 371, NULL, '39', 11, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3e', 'Spécialiste, Treuilliste', 382, NULL, '39', 11, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4e', 'Chef équipe', 394, NULL, '39', 11, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5e', 'Pointeur / Chauffeurs / D.', 411, NULL, '39', 11, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6e', 'Acconiers / Superviseurs', 442, NULL, '39', 11, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7e', 'Chef de Quai', 677, NULL, '39', 11, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_elevage_chauffeurs`
--

CREATE TABLE `secteur_elevage_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_elevage_chauffeurs`
--

INSERT INTO `secteur_elevage_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 1302, NULL, '53', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 1365, NULL, '53', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 1486, NULL, '53', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_elevage_employes`
--

CREATE TABLE `secteur_elevage_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_elevage_employes`
--

INSERT INTO `secteur_elevage_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, NULL, 20682, '54', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, NULL, 34460, '54', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, NULL, 39456, '54', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, NULL, 47710, '54', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, NULL, 58183, '54', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, NULL, 67517, '54', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_elevage_ouvriers`
--

CREATE TABLE `secteur_elevage_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_elevage_ouvriers`
--

INSERT INTO `secteur_elevage_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', 'MO', 594, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 897, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', 'OS 1A', 1052, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4A', 'OS 1B', 1182, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4B', 'OS 2A', 1248, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5A', 'OS 2B', 1333, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5B', 'OP 1A', 1421, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '6A', 'OP 1B', 1587, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6B', 'OP 2A', 1668, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '7', 'OP2B', 1743, NULL, '52', 15, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_forestier_chauffeurs`
--

CREATE TABLE `secteur_forestier_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_forestier_chauffeurs`
--

INSERT INTO `secteur_forestier_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 1797, NULL, '56', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 1846, NULL, '56', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 1992, NULL, '56', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_forestier_employes`
--

CREATE TABLE `secteur_forestier_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_forestier_employes`
--

INSERT INTO `secteur_forestier_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, NULL, 26838, '57', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, NULL, 47254, '57', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, NULL, 52818, '57', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, NULL, 61271, '57', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, NULL, 73691, '57', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, NULL, 82810, '57', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_forestier_ouvriers`
--

CREATE TABLE `secteur_forestier_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_forestier_ouvriers`
--

INSERT INTO `secteur_forestier_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1 SMIG', 'MO', 736, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 1209, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', 'OS 1A', 1419, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4A', 'OS 1B', 1651, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4B', 'OS 2A', 1743, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5A', 'OS 2B', 1818, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5B', 'OP 1A', 1934, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '6A', 'OP 1B', 2112, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6B', 'OP 2A', 2192, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '7', 'OP2B', 2248, NULL, '55', 16, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_hotellerie_maitrises`
--

CREATE TABLE `secteur_hotellerie_maitrises` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_hotellerie_maitrises`
--

INSERT INTO `secteur_hotellerie_maitrises` (`id`, `categorie`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '7', 125422, '137', 9, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL),
(2, '8', 144099, '137', 9, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_hotelleris`
--

CREATE TABLE `secteur_hotelleris` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_hotelleris`
--

INSERT INTO `secteur_hotelleris` (`id`, `categorie`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1A (SMIG)', 75000, '33', 9, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1B', 81220, '33', 9, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2', 81693, '33', 9, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3', 85033, '33', 9, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4', 88007, '33', 9, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5', 93717, '33', 9, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6', 107539, '33', 9, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_hotelleris_cadres`
--

CREATE TABLE `secteur_hotelleris_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_hotelleris_cadres`
--

INSERT INTO `secteur_hotelleris_cadres` (`id`, `categorie`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '9', 166219, '138', 9, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL),
(2, '10', 198147, '138', 9, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL),
(3, '11', 219904, '138', 9, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_agri_agents`
--

CREATE TABLE `secteur_industriel_agri_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_agri_agents`
--

INSERT INTO `secteur_industriel_agri_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'MNP', NULL, 658, 114069, '25', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1', NULL, 747, 129393, '25', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2', NULL, 799, 138464, '25', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3', NULL, 955, 165372, '25', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4', NULL, 1038, 179917, '25', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M5', NULL, 1124, 194912, '25', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_agri_cadres`
--

CREATE TABLE `secteur_industriel_agri_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_agri_cadres`
--

INSERT INTO `secteur_industriel_agri_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '5A', 'Position', NULL, 166638, '26', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '5B', 'Position', NULL, 191831, '26', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2A', 'Position', NULL, 201483, '26', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2B', 'Position', NULL, 228661, '26', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A', 'Position', NULL, 237695, '26', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B', 'Position', NULL, 356470, '26', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_agri_chauffeurs`
--

CREATE TABLE `secteur_industriel_agri_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_agri_chauffeurs`
--

INSERT INTO `secteur_industriel_agri_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 426, 75773, '27', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 460, 79666, '27', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 476, 82564, '27', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', 'Conducteur de véhicule de transport en commun', 480, 83169, '27', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_agri_employes`
--

CREATE TABLE `secteur_industriel_agri_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_agri_employes`
--

INSERT INTO `secteur_industriel_agri_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', NULL, 384, 75000, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 477, 81673, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 483, 82997, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 518, 89015, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 618, 106187, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 700, 120348, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 708, 121608, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 760, 130614, '24', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_agri_ouvriers`
--

CREATE TABLE `secteur_industriel_agri_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_agri_ouvriers`
--

INSERT INTO `secteur_industriel_agri_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', 'MO', 398, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 439, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 440, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 1B', 451, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 452, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 476, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 488, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 504, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6A', 'OP 2A', 518, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6B', 'OP2B', 576, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7', 'OP3', 780, NULL, '23', 7, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_bois_agents`
--

CREATE TABLE `secteur_industriel_bois_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_bois_agents`
--

INSERT INTO `secteur_industriel_bois_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'MNP', NULL, 676, 117290, '15', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1', NULL, 767, 133045, '15', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2', NULL, 821, 142372, '15', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3', NULL, 981, 170041, '15', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4', NULL, 1067, 184995, '15', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M5', NULL, 1156, 200414, '15', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_bois_cadres`
--

CREATE TABLE `secteur_industriel_bois_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_bois_cadres`
--

INSERT INTO `secteur_industriel_bois_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '5A', 'Position', NULL, 171342, '16', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '5B', 'Position', NULL, 197245, '16', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2A', 'Position', NULL, 207171, '16', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2B', 'Position', NULL, 235116, '16', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A', 'Position', NULL, 244450, '16', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B', 'Position', NULL, 366533, '16', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_bois_chauffeurs`
--

CREATE TABLE `secteur_industriel_bois_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_bois_chauffeurs`
--

INSERT INTO `secteur_industriel_bois_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 449, 77912, '17', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 489, 81915, '17', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 623, 84894, '17', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', 'Conducteur de véhicule de transport en commun', 494, 86726, '17', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_bois_employes`
--

CREATE TABLE `secteur_industriel_bois_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_bois_employes`
--

INSERT INTO `secteur_industriel_bois_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1 (SMIG)', NULL, 388, 75000, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 496, 83979, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 500, 85340, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 531, 91528, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 633, 109184, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 717, 123745, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 725, 125041, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 778, 134301, '14', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_bois_ouvriers`
--

CREATE TABLE `secteur_industriel_bois_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_bois_ouvriers`
--

INSERT INTO `secteur_industriel_bois_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1 (SMIG)', 'MO', 433, 75000, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 437, 75711, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 438, 75901, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 1B', 448, 77652, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 449, 77843, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 473, 81916, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 484, 83857, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 501, 86769, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6A', 'OP 2A', 514, 89109, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6B', 'OP 2B', 572, 99197, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7', 'OP 3', 774, 134140, '13', 5, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_employes`
--

CREATE TABLE `secteur_industriel_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_employes`
--

INSERT INTO `secteur_industriel_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', NULL, 389, 75000, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 498, 86319, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 502, 86924, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 533, 92367, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 636, 110185, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 720, 124878, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 728, 126187, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 782, 135531, '2', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_ouvriers`
--

CREATE TABLE `secteur_industriel_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_ouvriers`
--

INSERT INTO `secteur_industriel_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', 'MO', 389, 75000, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 449, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 451, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 1B', 465, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 466, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 483, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 491, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 503, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6A', 'OP 2A', 513, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6B', 'OP2B', 573, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7', 'OP3', 781, NULL, '1', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_poly_agents`
--

CREATE TABLE `secteur_industriel_poly_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_poly_cadres`
--

CREATE TABLE `secteur_industriel_poly_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_poly_employes`
--

CREATE TABLE `secteur_industriel_poly_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_poly_employes`
--

INSERT INTO `secteur_industriel_poly_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', NULL, 388, 67200, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 496, 85935, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 500, 86538, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 531, 91956, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 633, 109695, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 717, 124323, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 725, 125626, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 778, 134929, '125', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_poly_ouvriers`
--

CREATE TABLE `secteur_industriel_poly_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_poly_ouvriers`
--

INSERT INTO `secteur_industriel_poly_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', 'MO', 388, 75000, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 470, 81534, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 473, 81923, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 1B', 474, 82117, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 475, 82311, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 479, 83087, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 489, 84835, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 502, 86776, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6', 'OP 2', 507, 88135, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '7', 'OP 3', 572, 99200, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'HC', 'HC', 778, 134726, '124', 30, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_sucre_agents`
--

CREATE TABLE `secteur_industriel_sucre_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_sucre_agents`
--

INSERT INTO `secteur_industriel_sucre_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'MNP', NULL, 658, 114029, '30', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1', NULL, 746, 129347, '30', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2', NULL, 798, 138415, '30', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3', NULL, 953, 165313, '30', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4', NULL, 1038, 179853, '30', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M5', NULL, 1124, 194844, '30', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_sucre_cadres`
--

CREATE TABLE `secteur_industriel_sucre_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_sucre_cadres`
--

INSERT INTO `secteur_industriel_sucre_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '5A', 'Position', NULL, 166579, '31', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '5B', 'Position', NULL, 191764, '31', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2A', 'Position', NULL, 201412, '31', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2B', 'Position', NULL, 228580, '31', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A', 'Position', NULL, 237611, '31', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B', 'Position', NULL, 356344, '31', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_sucre_chauffeurs`
--

CREATE TABLE `secteur_industriel_sucre_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_sucre_chauffeurs`
--

INSERT INTO `secteur_industriel_sucre_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 437, 75745, '32', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 460, 79638, '32', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 476, 82534, '32', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', 'Conducteur de véhicule de transport en commun', 480, 83139, '32', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_sucre_employes`
--

CREATE TABLE `secteur_industriel_sucre_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_sucre_employes`
--

INSERT INTO `secteur_industriel_sucre_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', NULL, 442, 75000, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 542, 81644, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 551, 82967, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 591, 88984, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 705, 106149, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 798, 120305, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 807, 121565, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 867, 130569, '29', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_sucre_ouvriers`
--

CREATE TABLE `secteur_industriel_sucre_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_sucre_ouvriers`
--

INSERT INTO `secteur_industriel_sucre_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', 'MO', 384, 75000, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 424, 67205, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 426, 67557, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 1B', 435, 68965, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 436, 69140, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 460, 72835, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 471, 74594, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 486, 84270, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6A', 'OP 2A', 500, 86578, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6B', 'OP2B', 556, 96391, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7', 'OP3', 753, 130445, '28', 8, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_text_agents`
--

CREATE TABLE `secteur_industriel_text_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_text_agents`
--

INSERT INTO `secteur_industriel_text_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'MNP', NULL, 587, 101787, '20', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1', NULL, 666, 115460, '20', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2', NULL, 713, 123554, '20', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3', NULL, 851, 147565, '20', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4', NULL, 926, 160543, '20', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M5', NULL, 1003, 173924, '20', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_text_cadres`
--

CREATE TABLE `secteur_industriel_text_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_text_cadres`
--

INSERT INTO `secteur_industriel_text_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '5A', 'Position', NULL, 166411, '21', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '5B', 'Position', NULL, 191568, '21', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2A', 'Position', NULL, 201207, '21', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2B', 'Position', NULL, 318748, '21', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A', 'Position', NULL, 237369, '21', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B', 'Position', NULL, 355981, '21', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_text_chauffeurs`
--

CREATE TABLE `secteur_industriel_text_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_text_chauffeurs`
--

INSERT INTO `secteur_industriel_text_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 398, 77873, '22', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 418, 81873, '22', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 433, 84852, '22', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', 'Conducteur de véhicule de transport en commun', 436, 85473, '22', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_text_employes`
--

CREATE TABLE `secteur_industriel_text_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_text_employes`
--

INSERT INTO `secteur_industriel_text_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', NULL, 391, 75000, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 485, 83936, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 492, 85296, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 528, 91483, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 629, 109131, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 713, 123684, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 721, 124979, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 774, 134234, '19', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_text_ouvriers`
--

CREATE TABLE `secteur_industriel_text_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_text_ouvriers`
--

INSERT INTO `secteur_industriel_text_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', 'MO', 391, 75000, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 436, 75603, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 437, 75799, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 2A', 449, 77758, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 449, 77562, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 472, 81871, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 484, 83829, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 501, 86767, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6A', 'OP 2A', 514, 89118, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6B', 'OP2B', 573, 99302, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7', 'OP3', 774, 134166, '18', 6, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_thon_agents`
--

CREATE TABLE `secteur_industriel_thon_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_thon_agents`
--

INSERT INTO `secteur_industriel_thon_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'MNP', NULL, 682, 118180, '121', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1', NULL, 774, 134056, '121', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2', NULL, 828, 143453, '121', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3', NULL, 989, 171332, '121', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4', NULL, 1075, 186400, '121', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M5', NULL, 1165, 201935, '121', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_thon_cadres`
--

CREATE TABLE `secteur_industriel_thon_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_thon_cadres`
--

INSERT INTO `secteur_industriel_thon_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '5A', 'Position', NULL, 172643, '122', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '5B', 'Position', NULL, 198744, '122', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2A', 'Position', NULL, 208743, '122', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2B', 'Position', NULL, 236901, '122', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A', 'Position', NULL, 246261, '122', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B', 'Position', NULL, 369316, '122', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_thon_chauffeurs`
--

CREATE TABLE `secteur_industriel_thon_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_thon_chauffeurs`
--

INSERT INTO `secteur_industriel_thon_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 394, 68264, '123', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 414, 71771, '123', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 429, 74382, '123', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', 'Conducteur de véhicule de transport en commun', 432, 74927, '123', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_thon_employes`
--

CREATE TABLE `secteur_industriel_thon_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_thon_employes`
--

INSERT INTO `secteur_industriel_thon_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', NULL, 398, 75000, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 489, 84616, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 496, 85988, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 532, 92223, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 635, 110014, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 720, 124685, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 727, 125991, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 781, 135321, '120', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_industriel_thon_ouvriers`
--

CREATE TABLE `secteur_industriel_thon_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_industriel_thon_ouvriers`
--

INSERT INTO `secteur_industriel_thon_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1(SMIG)', 'MO', 398, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 439, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 440, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 1B', 451, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 452, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 476, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 488, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 504, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6A', 'OP 2A', 518, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6B', 'OP2B', 576, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7', 'OP3', 780, NULL, '119', 29, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_indus_agents`
--

CREATE TABLE `secteur_indus_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_indus_agents`
--

INSERT INTO `secteur_indus_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'MNP', NULL, 607, 105213, '3', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1', NULL, 689, 119345, '3', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2', NULL, 737, 127712, '3', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3', NULL, 880, 152531, '3', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4', NULL, 957, 165946, '3', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M5', NULL, 1037, 179778, '3', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_indus_cadres`
--

CREATE TABLE `secteur_indus_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_indus_cadres`
--

INSERT INTO `secteur_indus_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1A', 'Position', NULL, 172911, '4', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1B', 'Position', NULL, 199052, '4', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2A', 'Position', NULL, 209068, '4', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '2B', 'Position', NULL, 237269, '4', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '3A', 'Position', NULL, 246644, '4', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '3B', 'Position', NULL, 369890, '4', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maison_employes`
--

CREATE TABLE `secteur_maison_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maison_employes`
--

INSERT INTO `secteur_maison_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1ère catégorie (SMIG)', 'Employés de maison sans spécialité, petit boy, petite bonne aide-cuisinier', NULL, 75000, '115', 27, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2e catégorie', 'Boy ou bonne n’assurant qu’une partie des travaux de la maison sans lavage du linge', NULL, 76921, '115', 27, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3e catégorie', 'Boy ou bonne chargé (e ) d’exécuter l’ensemble des travaux courants de l’intérieur et justifiant de plus de 2 ans de pratique. Cuisinier ayant au moins 2 ans de pratique', NULL, 83587, '115', 27, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4e catégorie', 'Boy cuisinier ou bonne cuisinière assurant l’ensemble des travaux d’intérieur y compris la cuisine courante; boy ou bonne qualifié (e ) justifiant de plus de 4 ans de pratique, blanchisseur et repasseur', NULL, 85505, '115', 27, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5e catégorie', 'Cuisinier ou cuisinière qualifié (e ) sachant faire la pâtisserie', NULL, 87740, '115', 27, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6e catégorie', 'Cuisinier ou cuisinière qualifié (e ) sachant faire la pâtisserie ou la charcuterie', NULL, 91121, '115', 27, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7e catégorie', 'Maître d’hôtel', NULL, 94905, '115', 27, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_capits`
--

CREATE TABLE `secteur_maritime_capits` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` int(11) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_capits`
--

INSERT INTO `secteur_maritime_capits` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A01', 273466, 370773, 485536, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'A02', 279549, 379019, 496334, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'A03', 285630, 387265, 507133, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'A04', 291713, 395512, 517931, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'A05', 297795, 403758, 528731, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'A06', 303877, 412004, 539529, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'A07', 309959, 420251, 550328, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'A08', 316042, 428497, 561126, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'A09', 322123, 436743, 571926, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'A10', 328206, 444989, 582725, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'A11', 334287, 453236, 593523, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'A12', 340370, 461482, 604322, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'A13', 346452, 469728, 615120, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'A14', 352534, 477975, 625920, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'A15', 358616, 486221, 636718, '76', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_chef_mecas`
--

CREATE TABLE `secteur_maritime_chef_mecas` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` int(11) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_chef_mecas`
--

INSERT INTO `secteur_maritime_chef_mecas` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'C01', 257702, 349398, 457545, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'C02', 263783, 357645, 468344, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C03', 269866, 365890, 479142, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'C04', 275947, 374137, 489942, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'C05', 282030, 382384, 500740, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'C06', 288113, 390629, 511539, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'C07', 294194, 398876, 522337, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'C08', 300277, 407123, 533136, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'C09', 306359, 415368, 543936, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'C10', 312441, 423615, 554734, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'C11', 318523, 431861, 565533, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'C12', 324605, 440108, 576331, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'C13', 330687, 448354, 587131, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'C14', 336770, 456600, 597929, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'C15', 342851, 464847, 608728, '74', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_machines`
--

CREATE TABLE `secteur_maritime_machines` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` int(11) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_machines`
--

INSERT INTO `secteur_maritime_machines` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'H01', 123530, 163708, 206758, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'H02', 126247, 167308, 210330, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'H03', 128963, 170907, 214855, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'H04', 131679, 174507, 219381, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'H05', 134396, 178107, 223906, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'H06', 137112, 181707, 228432, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'H07', 139829, 185306, 232957, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'H08', 142545, 188907, 237483, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'H09', 145261, 192506, 242007, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'H10', 147978, 196105, 246533, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'H11', 150694, 199706, 251058, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'H12', 153410, 203305, 255584, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'H13', 156127, 206905, 260109, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'H14', 158843, 210505, 264635, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'H15', 161558, 214104, 269160, '73', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_maitres`
--

CREATE TABLE `secteur_maritime_maitres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` int(11) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_maitres`
--

INSERT INTO `secteur_maritime_maitres` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'G01', 126542, 167699, 210822, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'G02', 129141, 171143, 215150, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'G03', 131738, 174586, 219479, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'G04', 134337, 178029, 223808, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'G05', 136935, 181473, 228137, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'G06', 139533, 184916, 232465, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'G07', 142131, 188358, 236794, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'G08', 144730, 191801, 241122, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'G09', 147328, 195245, 245451, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'G10', 149926, 198688, 249779, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'G11', 152524, 202131, 254108, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'G12', 155123, 205574, 258437, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'G13', 157721, 209018, 262766, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'G14', 160318, 212461, 267094, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'G15', 162917, 215904, 271423, '72', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_matelos`
--

CREATE TABLE `secteur_maritime_matelos` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` int(11) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_matelos`
--

INSERT INTO `secteur_maritime_matelos` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'H01', 123530, 163708, 206758, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'H02', 126247, 167308, 210330, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'H03', 128963, 170907, 214855, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'H04', 131679, 174507, 219381, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'H05', 134396, 178107, 223906, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'H06', 137112, 181707, 228432, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'H07', 139829, 185306, 232957, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'H08', 142545, 188907, 237483, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'H09', 145261, 192506, 242007, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'H10', 147978, 196105, 246533, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'H11', 150694, 199706, 251058, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'H12', 153410, 203305, 255584, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'H13', 156127, 206905, 260109, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'H14', 158843, 210505, 264635, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'H15', 161558, 214104, 269160, '71', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_polies`
--

CREATE TABLE `secteur_maritime_polies` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` varchar(250) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_polies`
--

INSERT INTO `secteur_maritime_polies` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'G01', '126542', 167699, 210822, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'G02', '129141', 171143, 215150, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'G03', '131738', 174586, 219479, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'G04', '134337', 178029, 223808, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'G05', '136935', 181473, 228137, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'G06', '139533', 184916, 232465, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'G07', '142131', 188358, 236794, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'G08', '144730', 191801, 241122, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'G09', '147328', 195245, 245451, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'G10', '149926', 198688, 249779, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'G11', '152524', 202131, 254108, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'G12', '155123', 205574, 258437, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'G13', '157721', 209018, 262766, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'G14', '160318', 212461, 267094, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'G15', '162917', 215904, 271423, '70', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_second_capits`
--

CREATE TABLE `secteur_maritime_second_capits` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` int(11) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_second_capits`
--

INSERT INTO `secteur_maritime_second_capits` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'B01', 225675, 305976, 400684, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B02', 231817, 314303, 411587, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'B03', 237958, 322629, 422491, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'B04', 244099, 330956, 433395, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'B05', 250240, 339282, 444298, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'B06', 254247, 344714, 451412, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'B07', 260329, 352961, 462210, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'B08', 266411, 361207, 473009, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'B09', 272493, 369453, 483807, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'B10', 278576, 377700, 494607, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'B11', 284657, 385945, 505405, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'B12', 290740, 394192, 516204, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'B13', 296821, 402439, 527002, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'B14', 302904, 410684, 537802, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'B15', 308986, 418931, 548600, '77', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_maritime_second_mecas`
--

CREATE TABLE `secteur_maritime_second_mecas` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `navi_cotiere` int(11) DEFAULT NULL,
  `cabot_inter` int(11) DEFAULT NULL,
  `long_cours` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_maritime_second_mecas`
--

INSERT INTO `secteur_maritime_second_mecas` (`id`, `categorie`, `navi_cotiere`, `cabot_inter`, `long_cours`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'D01', 223, 170302, 580396, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'D02', 229, 312310, 906407, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'D03', 235, 452319, 233418, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D04', 241, 593327, 559428, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'D05', 247, 735335, 886439, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'D06', 253, 876344, 211450, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'D07', 257, 847349, 596457, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'D08', 263, 929357, 842468, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'D09', 270, 12366, 89479, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'D10', 276, 94374, 336490, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'D11', 282, 176382, 581500, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'D12', 288, 259390, 828511, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'D13', 294, 340399, 74522, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'D14', 300, 423407, 320533, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'D15', 306, 504415, 567544, '75', 21, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_nettoyage_chauffeurs`
--

CREATE TABLE `secteur_nettoyage_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_nettoyage_chauffeurs`
--

INSERT INTO `secteur_nettoyage_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', NULL, 405, NULL, '117', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', NULL, 426, NULL, '117', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', NULL, 441, NULL, '117', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', NULL, 472, NULL, '117', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_nettoyage_employes`
--

CREATE TABLE `secteur_nettoyage_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_nettoyage_employes`
--

INSERT INTO `secteur_nettoyage_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, NULL, 60000, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, NULL, 77084, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, NULL, 77626, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, NULL, 82485, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, NULL, 98398, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, NULL, 111520, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, NULL, 112687, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, NULL, 121032, '118', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_nettoyage_ouvriers`
--

CREATE TABLE `secteur_nettoyage_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_nettoyage_ouvriers`
--

INSERT INTO `secteur_nettoyage_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, 346, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 444, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 448, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 476, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 568, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 644, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 650, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 699, NULL, '116', 28, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_boscs`
--

CREATE TABLE `secteur_peches_boscs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_boscs`
--

INSERT INTO `secteur_peches_boscs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M3A', NULL, NULL, 181470, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M3B', NULL, NULL, 186914, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M3C', NULL, NULL, 192358, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M4A', NULL, NULL, 197802, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4B', NULL, NULL, 203246, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M4C', NULL, NULL, 208691, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M5A', NULL, NULL, 241135, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M5B', NULL, NULL, 219579, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M5C', NULL, NULL, 226838, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 235911, '82', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_brevets`
--

CREATE TABLE `secteur_peches_brevets` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_brevets`
--

INSERT INTO `secteur_peches_brevets` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '6eA', NULL, NULL, 130712, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '6eB', NULL, NULL, 134634, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '6eC', NULL, NULL, 138556, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '7eA', NULL, NULL, 142477, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7eB', NULL, NULL, 146399, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '7eC', NULL, NULL, 150320, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M1A', NULL, NULL, 154241, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M1B', NULL, NULL, 158163, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M1C', NULL, NULL, 163391, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 167313, '81', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_capits`
--

CREATE TABLE `secteur_peches_capits` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_capits`
--

INSERT INTO `secteur_peches_capits` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CA6', NULL, NULL, 250710, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CA7', NULL, NULL, 258231, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CA8', NULL, NULL, 265753, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CA9', NULL, NULL, 273274, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'CA10', NULL, NULL, 280795, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'CA11', NULL, NULL, 288317, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'CA12', NULL, NULL, 295838, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'CA13', NULL, NULL, 303359, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'CA14', NULL, NULL, 313388, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'CA15', NULL, NULL, 320909, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA16', NULL, NULL, 328430, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'HC', NULL, NULL, 338451, '86', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_chef_moteurs`
--

CREATE TABLE `secteur_peches_chef_moteurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_chef_moteurs`
--

INSERT INTO `secteur_peches_chef_moteurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M3A', NULL, NULL, 179615, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M3B', NULL, NULL, 185003, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M3C', NULL, NULL, 190392, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M4A', NULL, NULL, 195780, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4B', NULL, NULL, 201169, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M4C', NULL, NULL, 206557, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M5A', NULL, NULL, 211946, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M5B', NULL, NULL, 217334, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M5C', NULL, NULL, 214519, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 229907, '83', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_boscos`
--

CREATE TABLE `secteur_peches_cotiere_boscos` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_boscos`
--

INSERT INTO `secteur_peches_cotiere_boscos` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M2A', NULL, NULL, 110800, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M2B', NULL, NULL, 114124, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2C', NULL, NULL, 117400, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3A', NULL, NULL, 120772, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M3B', NULL, NULL, 124096, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M3C', NULL, NULL, 127420, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M4A', NULL, NULL, 130744, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M4B', NULL, NULL, 134068, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M4C', NULL, NULL, 138525, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 141824, '105', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_capis`
--

CREATE TABLE `secteur_peches_cotiere_capis` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_capis`
--

INSERT INTO `secteur_peches_cotiere_capis` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CA2', NULL, NULL, 203600, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CA3', NULL, NULL, 209708, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CA4', NULL, NULL, 215816, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CA5', NULL, NULL, 221924, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'CA6', NULL, NULL, 228032, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'CA7', NULL, NULL, 234140, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'CA8', NULL, NULL, 240248, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'CA9', NULL, NULL, 246356, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'CA10', NULL, NULL, 254500, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'CA11', NULL, NULL, 260608, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'HC', NULL, NULL, 266716, '102', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_capis_capas`
--

CREATE TABLE `secteur_peches_cotiere_capis_capas` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_capis_capas`
--

INSERT INTO `secteur_peches_cotiere_capis_capas` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'C1', NULL, NULL, 153378, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'C2', NULL, NULL, 157979, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C3', NULL, NULL, 162581, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'C4', NULL, NULL, 167182, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'C5', NULL, NULL, 171783, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'C6', NULL, NULL, 176385, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'C7', NULL, NULL, 180986, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'C8', NULL, NULL, 185587, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'C9', NULL, NULL, 191723, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'C10', NULL, NULL, 196324, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'HC', NULL, NULL, 200925, '103', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_eleves`
--

CREATE TABLE `secteur_peches_cotiere_eleves` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_eleves`
--

INSERT INTO `secteur_peches_cotiere_eleves` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'E01', NULL, NULL, 74500, '99', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'E02', NULL, NULL, 76735, '99', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'E03', NULL, NULL, 78970, '99', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'E04', NULL, NULL, 81205, '99', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'E05', NULL, NULL, 83440, '99', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'E06', NULL, NULL, 85675, '99', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'E07', NULL, NULL, 87910, '99', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_matlots`
--

CREATE TABLE `secteur_peches_cotiere_matlots` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_matlots`
--

INSERT INTO `secteur_peches_cotiere_matlots` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '6eA', NULL, NULL, 98235, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '6eB', NULL, NULL, 101182, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '6eC', NULL, NULL, 104129, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '7eA', NULL, NULL, 107076, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7eB', NULL, NULL, 110023, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '7eC', NULL, NULL, 112970, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M1A', NULL, NULL, 115917, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M1B', NULL, NULL, 118864, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M1C', NULL, NULL, 122794, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 125741, '101', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_matlot_simpls`
--

CREATE TABLE `secteur_peches_cotiere_matlot_simpls` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_matlot_simpls`
--

INSERT INTO `secteur_peches_cotiere_matlot_simpls` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '5e', NULL, NULL, 82921, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '6eA', NULL, NULL, 85409, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '6eB', NULL, NULL, 87896, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '6eC', NULL, NULL, 90384, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7eA', NULL, NULL, 92872, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '7eB', NULL, NULL, 95359, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7eC', NULL, NULL, 97847, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M1A', NULL, NULL, 100334, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M1B', NULL, NULL, 103651, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'M1C', NULL, NULL, 106139, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'HC', NULL, NULL, 108827, '100', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_mecas`
--

CREATE TABLE `secteur_peches_cotiere_mecas` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_mecas`
--

INSERT INTO `secteur_peches_cotiere_mecas` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M2A', NULL, NULL, 112273, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M2B', NULL, NULL, 115641, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2C', NULL, NULL, 119009, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3A', NULL, NULL, 122387, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M3B', NULL, NULL, 125746, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M3C', NULL, NULL, 129114, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M4A', NULL, NULL, 132482, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M4B', NULL, NULL, 135850, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M4C', NULL, NULL, 140341, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 143710, '104', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_novieces`
--

CREATE TABLE `secteur_peches_cotiere_novieces` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_novieces`
--

INSERT INTO `secteur_peches_cotiere_novieces` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'NOVICE SMI', NULL, NULL, 60000, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '4', NULL, NULL, 61800, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '5', NULL, NULL, 63600, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '6', NULL, NULL, 65400, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7', NULL, NULL, 67200, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '8', NULL, NULL, 69000, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '9', NULL, NULL, 70800, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '10', NULL, NULL, 72600, '98', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_cotiere_second_bocos`
--

CREATE TABLE `secteur_peches_cotiere_second_bocos` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_cotiere_second_bocos`
--

INSERT INTO `secteur_peches_cotiere_second_bocos` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M1A', NULL, NULL, 108000, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1B', NULL, NULL, 111240, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M1C', NULL, NULL, 114480, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M2A', NULL, NULL, 117720, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M2B', NULL, NULL, 120960, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M2C', NULL, NULL, 124200, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M3A', NULL, NULL, 127440, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M3B', NULL, NULL, 130680, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M3C', NULL, NULL, 133920, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 139320, '106', 24, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_eleves`
--

CREATE TABLE `secteur_peches_eleves` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_eleves`
--

INSERT INTO `secteur_peches_eleves` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'E01', NULL, NULL, 74500, '79', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'E02', NULL, NULL, 76735, '79', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'E03', NULL, NULL, 78970, '79', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'E04', NULL, NULL, 81205, '79', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'E05', NULL, NULL, 83440, '79', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'E06', NULL, NULL, 85675, '79', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'E07', NULL, NULL, 87910, '79', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_graisseurs`
--

CREATE TABLE `secteur_peches_graisseurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_graisseurs`
--

INSERT INTO `secteur_peches_graisseurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '7eA', NULL, NULL, 144278, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '7eB', NULL, NULL, 148606, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '7eC', NULL, NULL, 152934, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M1A', NULL, NULL, 157262, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M1B', NULL, NULL, 161591, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M1C', NULL, NULL, 165919, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M2A', NULL, NULL, 170247, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M2B', NULL, NULL, 174576, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M2C', NULL, NULL, 180347, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 184675, '85', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_bosco_elecs`
--

CREATE TABLE `secteur_peches_larges_bosco_elecs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_bosco_elecs`
--

INSERT INTO `secteur_peches_larges_bosco_elecs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M3A', NULL, NULL, 142898, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M3B', NULL, NULL, 147185, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M3C', NULL, NULL, 151472, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M4A', NULL, NULL, 155759, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4B', NULL, NULL, 164333, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M4C', NULL, NULL, 168620, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M5A', NULL, NULL, 172907, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M5B', NULL, NULL, 178623, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M5C', NULL, NULL, 182909, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 187196, '96', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_cuistos`
--

CREATE TABLE `secteur_peches_larges_cuistos` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_cuistos`
--

INSERT INTO `secteur_peches_larges_cuistos` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M3A', NULL, NULL, 129032, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M3B', NULL, NULL, 132903, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M3C', NULL, NULL, 138774, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M4A', NULL, NULL, 140645, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M4B', NULL, NULL, 144516, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M4C', NULL, NULL, 148387, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M5A', NULL, NULL, 152258, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M5B', NULL, NULL, 156129, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M5C', NULL, NULL, 161290, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 169032, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA2', NULL, NULL, 204515, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'CA3', NULL, NULL, 210650, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'CA4', NULL, NULL, 216786, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'CA5', NULL, NULL, 222921, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'CA6', NULL, NULL, 229057, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(16, 'CA7', NULL, NULL, 235192, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(17, 'CA8', NULL, NULL, 241328, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(18, 'CA9', NULL, NULL, 247463, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(19, 'CA10', NULL, NULL, 255644, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(20, 'HC2', NULL, NULL, 261779, '92', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_eleves`
--

CREATE TABLE `secteur_peches_larges_eleves` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_eleves`
--

INSERT INTO `secteur_peches_larges_eleves` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'E01', NULL, NULL, 74500, '91', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'E02', NULL, NULL, 76735, '91', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'E03', NULL, NULL, 78970, '91', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'E04', NULL, NULL, 81205, '91', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'E05', NULL, NULL, 83440, '91', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'E06', NULL, NULL, 85675, '91', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'E07', NULL, NULL, 87910, '91', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_graisses`
--

CREATE TABLE `secteur_peches_larges_graisses` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_graisses`
--

INSERT INTO `secteur_peches_larges_graisses` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M1A', NULL, NULL, 117300, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M1B', NULL, NULL, 120819, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M1C', NULL, NULL, 124338, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M2A', NULL, NULL, 127857, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M2B', NULL, NULL, 131376, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M2C', NULL, NULL, 134895, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M3A', NULL, NULL, 138414, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M3B', NULL, NULL, 141933, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M3C', NULL, NULL, 146625, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 150144, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA1', NULL, NULL, 199809, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'CA2', NULL, NULL, 205803, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'CA3', NULL, NULL, 211798, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'CA4', NULL, NULL, 217792, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'CA5', NULL, NULL, 223786, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(16, 'CA6', NULL, NULL, 229780, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(17, 'CA7', NULL, NULL, 235775, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(18, 'CA8', NULL, NULL, 241769, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(19, 'CA9', NULL, NULL, 249761, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(20, 'HC2', NULL, NULL, 255756, '32', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_matlots`
--

CREATE TABLE `secteur_peches_larges_matlots` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_matlots`
--

INSERT INTO `secteur_peches_larges_matlots` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '6eA', NULL, NULL, 105918, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '6eB', NULL, NULL, 109096, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '6eC', NULL, NULL, 112273, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '7eA', NULL, NULL, 115451, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7eB', NULL, NULL, 118628, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '7eC', NULL, NULL, 121802, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M1A', NULL, NULL, 124983, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M2B', NULL, NULL, 128161, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M3C', NULL, NULL, 132398, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 135575, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA0', NULL, NULL, 195705, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'CA1', NULL, NULL, 201576, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'CA2', NULL, NULL, 207447, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'CA3', NULL, NULL, 213318, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'CA4', NULL, NULL, 219790, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(16, 'CA5', NULL, NULL, 225061, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(17, 'CA6', NULL, NULL, 230932, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(18, 'CA7', NULL, NULL, 236803, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(19, 'CA8', NULL, NULL, 244631, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(20, 'HC2', NULL, NULL, 264202, '93', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_matlots_simples`
--

CREATE TABLE `secteur_peches_larges_matlots_simples` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_matlots_simples`
--

INSERT INTO `secteur_peches_larges_matlots_simples` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '6eA', NULL, NULL, 102300, '94', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '6eB', NULL, NULL, 105369, '94', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '6eC', NULL, NULL, 108438, '94', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '7eA', NULL, NULL, 111507, '94', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7eB', NULL, NULL, 114576, '94', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '7eC', NULL, NULL, 117645, '94', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'HC', NULL, NULL, 120714, '94', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_novices`
--

CREATE TABLE `secteur_peches_larges_novices` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_novices`
--

INSERT INTO `secteur_peches_larges_novices` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'NOVICE SMI', NULL, NULL, 60000, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '4', NULL, NULL, 61800, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '5', NULL, NULL, 63600, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '6', NULL, NULL, 65400, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7', NULL, NULL, 67200, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '8', NULL, NULL, 69000, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '9', NULL, NULL, 70800, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '10', NULL, NULL, 72600, '90', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_off_ponts`
--

CREATE TABLE `secteur_peches_larges_off_ponts` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_off_ponts`
--

INSERT INTO `secteur_peches_larges_off_ponts` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CO1', NULL, NULL, 192358, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CO2', NULL, NULL, 198129, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CO3', NULL, NULL, 203899, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CO4', NULL, NULL, 209670, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'CO5', NULL, NULL, 215441, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'CO6', NULL, NULL, 221212, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'CO7', NULL, NULL, 226982, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'CO8', NULL, NULL, 232753, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'CO9', NULL, NULL, 238524, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 246218, '95', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_larges_second_boscos`
--

CREATE TABLE `secteur_peches_larges_second_boscos` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_larges_second_boscos`
--

INSERT INTO `secteur_peches_larges_second_boscos` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M2A', NULL, NULL, 127816, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M2B', NULL, NULL, 131650, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2C', NULL, NULL, 135485, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3A', NULL, NULL, 139319, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M3B', NULL, NULL, 143154, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M3C', NULL, NULL, 146988, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M4A', NULL, NULL, 150823, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M4B', NULL, NULL, 154657, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M4C', NULL, NULL, 159770, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 163604, '97', 23, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_mecanis`
--

CREATE TABLE `secteur_peches_mecanis` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_mecanis`
--

INSERT INTO `secteur_peches_mecanis` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CA5', NULL, NULL, 242620, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CA6', NULL, NULL, 249899, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CA7', NULL, NULL, 257177, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CA8', NULL, NULL, 264226, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'CA9', NULL, NULL, 271734, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'CA10', NULL, NULL, 279013, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'CA11', NULL, NULL, 286292, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'CA12', NULL, NULL, 293570, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'CA13', NULL, NULL, 383275, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'CA14', NULL, NULL, 310554, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA15', NULL, NULL, 317832, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'HC', NULL, NULL, 327537, '38', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_novices`
--

CREATE TABLE `secteur_peches_novices` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_novices`
--

INSERT INTO `secteur_peches_novices` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'NOVICE SMI', NULL, NULL, 60000, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '4', NULL, NULL, 61800, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '5', NULL, NULL, 63600, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '6', NULL, NULL, 65400, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7', NULL, NULL, 67200, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '8', NULL, NULL, 69000, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '9', NULL, NULL, 70800, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '10', NULL, NULL, 72600, '78', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_off_pons`
--

CREATE TABLE `secteur_peches_off_pons` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_off_pons`
--

INSERT INTO `secteur_peches_off_pons` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CA0', NULL, NULL, 192358, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CA1', NULL, NULL, 198129, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CA2', NULL, NULL, 203899, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CA3', NULL, NULL, 209670, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'CA4', NULL, NULL, 215441, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'CA5', NULL, NULL, 221212, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'CA6', NULL, NULL, 226982, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'CA7', NULL, NULL, 232753, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'CA8', NULL, NULL, 238524, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'CA9', NULL, NULL, 246218, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA10', NULL, NULL, 251989, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'HC', NULL, NULL, 259683, '89', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_sbrevets`
--

CREATE TABLE `secteur_peches_sbrevets` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_sbrevets`
--

INSERT INTO `secteur_peches_sbrevets` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '6eA', NULL, NULL, 138516, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '6eB', NULL, NULL, 142672, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '6eC', NULL, NULL, 146827, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '7eA', NULL, NULL, 150983, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '7eB', NULL, NULL, 155138, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '7eC', NULL, NULL, 159294, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M1A', NULL, NULL, 163449, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M1B', NULL, NULL, 167605, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M1C', NULL, NULL, 173145, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 177301, '80', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_sceon_boscos`
--

CREATE TABLE `secteur_peches_sceon_boscos` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_sceon_boscos`
--

INSERT INTO `secteur_peches_sceon_boscos` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M2A', NULL, NULL, 169326, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M2B', NULL, NULL, 174406, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M2C', NULL, NULL, 179486, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M3A', NULL, NULL, 184566, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M3B', NULL, NULL, 189645, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M3C', NULL, NULL, 194725, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M4A', NULL, NULL, 199805, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M4B', NULL, NULL, 204885, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'M4C', NULL, NULL, 211658, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'HC', NULL, NULL, 216738, '84', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_scon_capits`
--

CREATE TABLE `secteur_peches_scon_capits` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_scon_capits`
--

INSERT INTO `secteur_peches_scon_capits` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CA4', NULL, NULL, 235060, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CA5', NULL, NULL, 242112, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CA6', NULL, NULL, 249164, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CA7', NULL, NULL, 256215, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'CA8', NULL, NULL, 263267, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'CA9', NULL, NULL, 270319, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'CA10', NULL, NULL, 277371, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'CA11', NULL, NULL, 284423, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'CA12', NULL, NULL, 293825, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'CA13', NULL, NULL, 300876, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA14', NULL, NULL, 307929, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'HC', NULL, NULL, 317331, '87', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_peches_scon_mecas`
--

CREATE TABLE `secteur_peches_scon_mecas` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_peches_scon_mecas`
--

INSERT INTO `secteur_peches_scon_mecas` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CA3', NULL, NULL, 215705, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CA4', NULL, NULL, 220176, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CA5', NULL, NULL, 228647, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CA6', NULL, NULL, 235118, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'CA7', NULL, NULL, 241590, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'CA8', NULL, NULL, 248061, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'CA9', NULL, NULL, 254532, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'CA10', NULL, NULL, 261003, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'CA11', NULL, NULL, 229631, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'CA12', NULL, NULL, 276102, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'CA13', NULL, NULL, 282574, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'HC', NULL, NULL, 291202, '88', 22, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_dist_agents`
--

CREATE TABLE `secteur_petro_dist_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_dist_agents`
--

INSERT INTO `secteur_petro_dist_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M1', NULL, NULL, 244658, '66', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M2', NULL, NULL, 277451, '66', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M3', NULL, NULL, 333590, '66', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M4', NULL, NULL, 373941, '66', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_dist_cadres`
--

CREATE TABLE `secteur_petro_dist_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_dist_cadres`
--

INSERT INTO `secteur_petro_dist_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'C1', NULL, NULL, 355610, '67', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'C2', NULL, NULL, 406416, '67', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C3', NULL, NULL, 508017, '67', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'C4', NULL, NULL, 660422, '67', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_dist_chauffeurs`
--

CREATE TABLE `secteur_petro_dist_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_dist_chauffeurs`
--

INSERT INTO `secteur_petro_dist_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CH 1', NULL, NULL, 119662, '68', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CH 2', NULL, NULL, 125580, '68', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CH 3', NULL, NULL, 127422, '68', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CH 4', NULL, NULL, 142713, '68', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_dist_employes`
--

CREATE TABLE `secteur_petro_dist_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_dist_employes`
--

INSERT INTO `secteur_petro_dist_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1A (SMIG)', NULL, NULL, 75000, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1B', NULL, NULL, 94878, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2', NULL, NULL, 111939, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3', NULL, NULL, 113761, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4', NULL, NULL, 125638, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5', NULL, NULL, 142713, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6', NULL, NULL, 173855, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7', NULL, NULL, 194431, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '8', NULL, NULL, 230075, '69', 20, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_prod_agents`
--

CREATE TABLE `secteur_petro_prod_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_prod_agents`
--

INSERT INTO `secteur_petro_prod_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M1', NULL, NULL, 220010, '64', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M2', NULL, NULL, 249499, '64', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M3', NULL, NULL, 299984, '64', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M4', NULL, NULL, 336269, '64', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_prod_cadres`
--

CREATE TABLE `secteur_petro_prod_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_prod_cadres`
--

INSERT INTO `secteur_petro_prod_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'C1', NULL, NULL, 319786, '65', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'C2', NULL, NULL, 365472, '65', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C3', NULL, NULL, 456839, '65', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'C4', NULL, NULL, 593890, '65', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_prod_chauffeurs`
--

CREATE TABLE `secteur_petro_prod_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_prod_chauffeurs`
--

INSERT INTO `secteur_petro_prod_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'CH 1', NULL, NULL, 107608, '63', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'CH 2', NULL, NULL, 112929, '63', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'CH 3', NULL, NULL, 114585, '63', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'CH 4', NULL, NULL, 128336, '63', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_prod_employes`
--

CREATE TABLE `secteur_petro_prod_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_prod_employes`
--

INSERT INTO `secteur_petro_prod_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1A SMIG', '', NULL, 66000, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1B', NULL, NULL, 93852, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2', NULL, NULL, 110728, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3', NULL, NULL, 112531, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4', NULL, NULL, 124278, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5', NULL, NULL, 141170, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6', NULL, NULL, 171975, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7', NULL, NULL, 192327, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '8', NULL, NULL, 227586, '62', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_petro_prod_ouvriers`
--

CREATE TABLE `secteur_petro_prod_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_petro_prod_ouvriers`
--

INSERT INTO `secteur_petro_prod_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1A SMIG', NULL, NULL, 66000, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1B', NULL, NULL, 93852, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2', NULL, NULL, 110728, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3', NULL, NULL, 112531, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4', NULL, NULL, 124278, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5', NULL, NULL, 141170, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6', NULL, NULL, 171975, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7', NULL, NULL, 192327, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '8', NULL, NULL, 227586, '2', 19, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_securite_chauffeurs`
--

CREATE TABLE `secteur_securite_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_securite_chauffeurs`
--

INSERT INTO `secteur_securite_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', NULL, 405, NULL, '7', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', NULL, 423, NULL, '7', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', NULL, 441, NULL, '7', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', NULL, 460, NULL, '7', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_securite_employes`
--

CREATE TABLE `secteur_securite_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_securite_employes`
--

INSERT INTO `secteur_securite_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, NULL, 60000, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, NULL, 63000, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, NULL, 67073, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, NULL, 74063, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, NULL, 83250, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, NULL, 99356, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7', NULL, NULL, 114668, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '8', NULL, NULL, 125216, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '9', NULL, NULL, 140414, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '10', NULL, NULL, 149714, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '11', NULL, NULL, 208239, '6', 2, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_tourisms`
--

CREATE TABLE `secteur_tourisms` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_tourisms`
--

INSERT INTO `secteur_tourisms` (`id`, `categorie`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1A (SMIG)', 75000, '139', 31, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '1B', 80462, '139', 31, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '2', 80928, '139', 31, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3', 84235, '139', 31, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4', 87177, '139', 31, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5', 92842, '139', 31, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '6', 106534, '139', 31, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_tourisms_cadres`
--

CREATE TABLE `secteur_tourisms_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_tourisms_cadres`
--

INSERT INTO `secteur_tourisms_cadres` (`id`, `categorie`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '9', 164667, '140', 31, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL),
(2, '10', 196295, '140', 31, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL),
(3, '11', 217849, '140', 31, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_tourisms_matrises`
--

CREATE TABLE `secteur_tourisms_matrises` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_tourisms_matrises`
--

INSERT INTO `secteur_tourisms_matrises` (`id`, `categorie`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '7', 124251, '141', 31, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL),
(2, '8', 142753, '141', 31, '2024-03-22 15:40:52', '2024-03-22 15:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_transport_agents`
--

CREATE TABLE `secteur_transport_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_transport_agents`
--

INSERT INTO `secteur_transport_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M 1 A', NULL, 644, 111635, '109', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M 1 B', NULL, 736, 127513, '109', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M 2 A', NULL, 748, 129641, '109', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M 2 B', NULL, 766, 132721, '109', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M 3', NULL, 899, 155825, '109', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M 4', NULL, 986, 170826, '109', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M 5', NULL, 1068, 185066, '109', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_transport_cadres`
--

CREATE TABLE `secteur_transport_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_transport_cadres`
--

INSERT INTO `secteur_transport_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'C 1 A', NULL, NULL, 183579, '110', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'C 1 B', NULL, NULL, 194999, '110', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C 2 A', NULL, NULL, 204736, '110', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'C 2 B', NULL, NULL, 221797, '110', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'C 2 C', NULL, NULL, 234708, '110', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'C 3', NULL, NULL, 285642, '110', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_transport_chauffeurs`
--

CREATE TABLE `secteur_transport_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `secteur_transport_employes`
--

CREATE TABLE `secteur_transport_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_transport_employes`
--

INSERT INTO `secteur_transport_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1 SMIG', NULL, 346, 75000, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, 447, 77399, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, 475, 82260, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, 508, 88120, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5', NULL, 592, 102632, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '6', NULL, 660, 114413, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '7A', NULL, 667, 115609, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '7B', NULL, 735, 127468, '108', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_transport_ouvriers`
--

CREATE TABLE `secteur_transport_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_transport_ouvriers`
--

INSERT INTO `secteur_transport_ouvriers` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'SMIG', 'MO', 346, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', 'MS', 402, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3A', 'OS 1A', 417, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '3B', 'OS 1B', 442, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '4A', 'OS 2A', 454, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '4B', 'OS 2B', 474, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5A', 'OP 1A', 496, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '5B', 'OP 1B', 515, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6A', 'OP 2A', 523, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6B', 'OP 2B', 571, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7', 'OP 3', 743, NULL, '107', 25, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_trps_aerien_agents`
--

CREATE TABLE `secteur_trps_aerien_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `echelle` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_trps_aerien_agents`
--

INSERT INTO `secteur_trps_aerien_agents` (`id`, `categorie`, `echelle`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'D1', '01', NULL, 140881, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'D1', '02', NULL, 149646, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'D1', '03', NULL, 158398, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D1', '04', NULL, 167162, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'D1', '05', NULL, 175924, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'D1', '06', NULL, 184686, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'D1', '07', NULL, 193447, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'D1', '08', NULL, 202206, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'D1', '09', NULL, 210967, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'D1', '10', NULL, 219726, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'D2', '01', NULL, 193480, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'D2', '02', NULL, 201931, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'D2', '03', NULL, 210375, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'D2', '04', NULL, 218814, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'D2', '05', NULL, 227261, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(16, 'D2', '06', NULL, 235705, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(17, 'D2', '07', NULL, 244149, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(18, 'D2', '08', NULL, 252597, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(19, 'D2', '09', NULL, 261041, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(20, 'D2', '10', NULL, 269483, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(21, 'C1', '01', NULL, 209209, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(22, 'C1', '02', NULL, 216680, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(23, 'C1', '03', NULL, 224152, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(24, 'C1', '04', NULL, 231631, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(25, 'C1', '05', NULL, 239095, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(26, 'C1', '06', NULL, 246567, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(27, 'C1', '07', NULL, 254044, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(28, 'C1', '08', NULL, 261512, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(29, 'C1', '09', NULL, 265745, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(30, 'C1', '10', NULL, 276454, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(31, 'C2', '01', NULL, 254044, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(32, 'C2', '02', NULL, 261512, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(33, 'C2', '03', NULL, 268985, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(34, 'C2', '04', NULL, 276454, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(35, 'C2', '05', NULL, 283930, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(36, 'C2', '06', NULL, 291400, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(37, 'C2', '07', NULL, 298869, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(38, 'C2', '08', NULL, 306339, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(39, 'C2', '09', NULL, 313817, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(40, 'C2', '10', NULL, 321286, '112', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_trps_aerien_cadres`
--

CREATE TABLE `secteur_trps_aerien_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `echelle` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_trps_aerien_cadres`
--

INSERT INTO `secteur_trps_aerien_cadres` (`id`, `categorie`, `echelle`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'B1', '01', NULL, 236856, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B1', '02', NULL, 245733, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'B1', '03', NULL, 254619, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'B1', '04', NULL, 263505, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'B1', '05', NULL, 272386, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'B1', '06', NULL, 281265, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'B1', '07', NULL, 290147, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'B1', '08', NULL, 299029, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'B1', '09', NULL, 307912, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'B1', '10', NULL, 304608, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'B2', '01', NULL, 299029, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'B2', '02', NULL, 307912, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'B2', '03', NULL, 304608, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'B2', '04', NULL, 325676, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'B2', '05', NULL, 334553, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(16, 'B2', '06', NULL, 343439, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(17, 'B2', '07', NULL, 352319, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(18, 'B2', '08', NULL, 347309, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(19, 'B2', '09', NULL, 370089, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(20, 'B2', '10', NULL, 378968, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(21, 'B3', '01', NULL, 347309, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(22, 'B3', '02', NULL, 370089, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(23, 'B3', '03', NULL, 378968, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(24, 'B3', '04', NULL, 387852, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(25, 'B3', '05', NULL, 396732, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(26, 'B3', '06', NULL, 405615, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(27, 'B3', '07', NULL, 414499, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(28, 'B3', '08', NULL, 423378, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(29, 'B3', '09', NULL, 432262, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(30, 'B3', '10', NULL, 441141, '113', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_trps_aerien_cadres_sups`
--

CREATE TABLE `secteur_trps_aerien_cadres_sups` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `echelle` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_trps_aerien_cadres_sups`
--

INSERT INTO `secteur_trps_aerien_cadres_sups` (`id`, `categorie`, `echelle`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A1', '01', NULL, 364633, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'A1', '02', NULL, 381725, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'A1', '03', NULL, 398821, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'A1', '04', NULL, 415912, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'A1', '05', NULL, 433001, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'A1', '06', NULL, 450094, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'A1', '07', NULL, 467187, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'A1', '08', NULL, 484277, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'A1', '09', NULL, 501374, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'A1', '10', NULL, 518464, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'A2', '01', NULL, 484277, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'A2', '02', NULL, 501374, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'A2', '03', NULL, 518464, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'A2', '04', NULL, 535555, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'A2', '05', NULL, 552642, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(16, 'A2', '06', NULL, 569741, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(17, 'A2', '07', NULL, 575328, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(18, 'A2', '08', NULL, 592587, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(19, 'A2', '09', NULL, 621018, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(20, 'A2', '10', NULL, 638106, '114', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_trps_aerien_ouvriers`
--

CREATE TABLE `secteur_trps_aerien_ouvriers` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `echelle` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_trps_aerien_ouvriers`
--

INSERT INTO `secteur_trps_aerien_ouvriers` (`id`, `categorie`, `echelle`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'E1', 'SMIG', NULL, 75000, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'E1', '01', NULL, 64038, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'E1', '02', NULL, 78211, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'E1', '03', NULL, 87143, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'E1', '04', NULL, 96294, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'E1', '05', NULL, 105330, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'E1', '06', NULL, 114380, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'E2', '01', NULL, 80118, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, 'E2', '02', NULL, 89030, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, 'E2', '03', NULL, 97142, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, 'E2', '04', NULL, 105282, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, 'E2', '05', NULL, 113403, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, 'E2', '06', NULL, 121532, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(14, 'E3', '01', NULL, 85398, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(15, 'E3', '02', NULL, 96336, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(16, 'E3', '03', NULL, 107268, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(17, 'E3', '04', NULL, 118201, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(18, 'E3', '05', NULL, 129145, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(19, 'E3', '06', NULL, 140076, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(20, 'E4', '01', NULL, 95048, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(21, 'E4', '02', NULL, 104838, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(22, 'E4', '03', NULL, 115538, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(23, 'E4', '04', NULL, 124415, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(24, 'E4', '05', NULL, 134206, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(25, 'E4', '06', NULL, 144000, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(26, 'E4', '07', NULL, 153781, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(27, 'E4', '08', NULL, 163577, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(28, 'E4', '09', NULL, 173372, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(29, 'E4', '10', NULL, 183159, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(30, 'E5', '01', NULL, 139048, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(31, 'E5', '02', NULL, 147646, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(32, 'E5', '03', NULL, 156236, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(33, 'E5', '04', NULL, 164825, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(34, 'E5', '05', NULL, 173423, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(35, 'E5', '06', NULL, 182012, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(36, 'E5', '07', NULL, 190609, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(37, 'E5', '08', NULL, 199201, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(38, 'E5', '09', NULL, 199447, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(39, 'E5', '10', NULL, 200356, '111', 26, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_trps_fond_agents`
--

CREATE TABLE `secteur_trps_fond_agents` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_trps_fond_agents`
--

INSERT INTO `secteur_trps_fond_agents` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'M 1 A', NULL, NULL, 123806, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'M 1 B', NULL, NULL, 125792, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'M 2 A', NULL, NULL, 128145, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'M 2 B', NULL, NULL, 132485, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'M 3 A', NULL, NULL, 158231, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, 'M 3 B', NULL, NULL, 165190, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, 'M 4', NULL, NULL, 172148, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, 'M 5', NULL, NULL, 185268, '11', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_trps_fond_cadres`
--

CREATE TABLE `secteur_trps_fond_cadres` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_trps_fond_cadres`
--

INSERT INTO `secteur_trps_fond_cadres` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'C 1 A', NULL, NULL, 190441, '12', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'C 1 B', NULL, NULL, 202288, '12', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C 2 A', NULL, NULL, 212388, '12', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'C 2 B', NULL, NULL, 230086, '12', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, 'C 3', NULL, NULL, 296317, '12', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `secteur_trps_fond_employes`
--

CREATE TABLE `secteur_trps_fond_employes` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(50) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `secteur_trps_fond_employes`
--

INSERT INTO `secteur_trps_fond_employes` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, '1', NULL, NULL, 75000, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, '2', NULL, NULL, 79595, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, '3', NULL, NULL, 80155, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, '4', NULL, NULL, 85172, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(5, '5A', NULL, NULL, 93386, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(6, '5B', NULL, NULL, 101603, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(7, '5C', NULL, NULL, 104614, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(8, '6A', NULL, NULL, 108269, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(9, '6B', NULL, NULL, 115152, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(10, '6C', NULL, NULL, 115794, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(11, '7A', NULL, NULL, 116357, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(12, '7B', NULL, NULL, 124974, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(13, '7C', NULL, NULL, 127779, '10', 4, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sec_indus_chauffeurs`
--

CREATE TABLE `sec_indus_chauffeurs` (
  `id` int(11) NOT NULL,
  `categorie` varchar(10) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `salaire_minima_horaire` int(11) DEFAULT NULL,
  `salaire_minima_mensuel` int(11) DEFAULT NULL,
  `type_poste` varchar(50) DEFAULT NULL,
  `id_secteur` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `company_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sec_indus_chauffeurs`
--

INSERT INTO `sec_indus_chauffeurs` (`id`, `categorie`, `definition`, `salaire_minima_horaire`, `salaire_minima_mensuel`, `type_poste`, `id_secteur`, `created_at`, `updated_at`, `company_id`) VALUES
(1, 'A', 'Conducteur de voiture de tourisme de petit tracteur ou de véhicule pesant moins de 3 T', 510, 88453, '5', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(2, 'B', 'Conducteur de véhicule poids lourds de 3 à 5T de charge utile', 537, 92998, '5', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(3, 'C', 'Conducteur de véhicule poids lourds dépassant 5 T de charge utile ou tracteur attelé à remorque semi-portée', 556, 96380, '5', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL),
(4, 'D', 'Conducteur de véhicule de transport en commun', 560, 97086, '5', 1, '2024-03-22 16:40:52', '2024-03-22 16:42:11', NULL);

-- --------------------------------------------------------

-- Index pour la table secteur_assurances_employes
ALTER TABLE `secteur_assurances_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_assurances_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_assurances_employes_company` (`company_id`);

ALTER TABLE `secteur_assurances_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `secteur_assurances_employes`
  ADD CONSTRAINT `fk_secteur_assurances_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_assurances_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_banque_agents
ALTER TABLE `secteur_banque_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_banque_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_banque_agents_company` (`company_id`);

ALTER TABLE `secteur_banque_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `secteur_banque_agents`
  ADD CONSTRAINT `fk_secteur_banque_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_banque_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_banque_employes
ALTER TABLE `secteur_banque_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_banque_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_banque_employes_company` (`company_id`);

ALTER TABLE `secteur_banque_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `secteur_banque_employes`
  ADD CONSTRAINT `fk_secteur_banque_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_banque_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_batiments
ALTER TABLE `secteur_batiments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_batiments_sector` (`id_secteur`),
  ADD KEY `fk_secteur_batiments_company` (`company_id`);

ALTER TABLE `secteur_batiments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `secteur_batiments`
  ADD CONSTRAINT `fk_secteur_batiments_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_batiments_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_batiment_agents
ALTER TABLE `secteur_batiment_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_batiment_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_batiment_agents_company` (`company_id`);

ALTER TABLE `secteur_batiment_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `secteur_batiment_agents`
  ADD CONSTRAINT `fk_secteur_batiment_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_batiment_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_batiment_cadres
ALTER TABLE `secteur_batiment_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_batiment_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_batiment_cadres_company` (`company_id`);

ALTER TABLE `secteur_batiment_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `secteur_batiment_cadres`
  ADD CONSTRAINT `fk_secteur_batiment_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_batiment_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_batiment_chauffeurs
ALTER TABLE `secteur_batiment_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_batiment_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_batiment_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_batiment_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_batiment_chauffeurs`
  ADD CONSTRAINT `fk_secteur_batiment_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_batiment_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_batiment_employes
ALTER TABLE `secteur_batiment_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_batiment_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_batiment_employes_company` (`company_id`);

ALTER TABLE `secteur_batiment_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `secteur_batiment_employes`
  ADD CONSTRAINT `fk_secteur_batiment_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_batiment_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_commerce_agents
ALTER TABLE `secteur_commerce_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_commerce_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_commerce_agents_company` (`company_id`);

ALTER TABLE `secteur_commerce_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_commerce_agents`
  ADD CONSTRAINT `fk_secteur_commerce_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_commerce_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_commerce_cadres
ALTER TABLE `secteur_commerce_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_commerce_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_commerce_cadres_company` (`company_id`);

ALTER TABLE `secteur_commerce_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_commerce_cadres`
  ADD CONSTRAINT `fk_secteur_commerce_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_commerce_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_commerce_employees
ALTER TABLE `secteur_commerce_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_commerce_employees_sector` (`id_secteur`),
  ADD KEY `fk_secteur_commerce_employees_company` (`company_id`);

ALTER TABLE `secteur_commerce_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_commerce_employees`
  ADD CONSTRAINT `fk_secteur_commerce_employees_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_commerce_employees_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_dockers_employes
ALTER TABLE `secteur_dockers_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_dockers_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_dockers_employes_company` (`company_id`);

ALTER TABLE `secteur_dockers_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `secteur_dockers_employes`
  ADD CONSTRAINT `fk_secteur_dockers_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_dockers_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_elevage_chauffeurs
ALTER TABLE `secteur_elevage_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_elevage_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_elevage_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_elevage_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `secteur_elevage_chauffeurs`
  ADD CONSTRAINT `fk_secteur_elevage_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_elevage_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_elevage_employes
ALTER TABLE `secteur_elevage_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_elevage_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_elevage_employes_company` (`company_id`);

ALTER TABLE `secteur_elevage_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_elevage_employes`
  ADD CONSTRAINT `fk_secteur_elevage_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_elevage_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_elevage_ouvriers
ALTER TABLE `secteur_elevage_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_elevage_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_elevage_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_elevage_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `secteur_elevage_ouvriers`
  ADD CONSTRAINT `fk_secteur_elevage_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_elevage_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_forestier_chauffeurs
ALTER TABLE `secteur_forestier_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_forestier_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_forestier_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_forestier_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `secteur_forestier_chauffeurs`
  ADD CONSTRAINT `fk_secteur_forestier_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_forestier_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_forestier_employes
ALTER TABLE `secteur_forestier_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_forestier_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_forestier_employes_company` (`company_id`);

ALTER TABLE `secteur_forestier_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_forestier_employes`
  ADD CONSTRAINT `fk_secteur_forestier_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_forestier_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_forestier_ouvriers
ALTER TABLE `secteur_forestier_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_forestier_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_forestier_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_forestier_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `secteur_forestier_ouvriers`
  ADD CONSTRAINT `fk_secteur_forestier_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_forestier_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_hotellerie_maitrises
ALTER TABLE `secteur_hotellerie_maitrises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_hotellerie_maitrises_sector` (`id_secteur`),
  ADD KEY `fk_secteur_hotellerie_maitrises_company` (`company_id`);

ALTER TABLE `secteur_hotellerie_maitrises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `secteur_hotellerie_maitrises`
  ADD CONSTRAINT `fk_secteur_hotellerie_maitrises_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_hotellerie_maitrises_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_hotelleris
ALTER TABLE `secteur_hotelleris`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_hotelleris_sector` (`id_secteur`),
  ADD KEY `fk_secteur_hotelleris_company` (`company_id`);

ALTER TABLE `secteur_hotelleris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `secteur_hotelleris`
  ADD CONSTRAINT `fk_secteur_hotelleris_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_hotelleris_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_hotelleris_cadres
ALTER TABLE `secteur_hotelleris_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_hotelleris_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_hotelleris_cadres_company` (`company_id`);

ALTER TABLE `secteur_hotelleris_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `secteur_hotelleris_cadres`
  ADD CONSTRAINT `fk_secteur_hotelleris_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_hotelleris_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_agri_agents
ALTER TABLE `secteur_industriel_agri_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_agri_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_agri_agents_company` (`company_id`);

ALTER TABLE `secteur_industriel_agri_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_agri_agents`
  ADD CONSTRAINT `fk_secteur_industriel_agri_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_agri_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_agri_cadres
ALTER TABLE `secteur_industriel_agri_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_agri_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_agri_cadres_company` (`company_id`);

ALTER TABLE `secteur_industriel_agri_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_agri_cadres`
  ADD CONSTRAINT `fk_secteur_industriel_agri_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_agri_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_agri_chauffeurs
ALTER TABLE `secteur_industriel_agri_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_agri_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_agri_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_industriel_agri_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_industriel_agri_chauffeurs`
  ADD CONSTRAINT `fk_secteur_industriel_agri_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_agri_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_agri_employes
ALTER TABLE `secteur_industriel_agri_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_agri_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_agri_employes_company` (`company_id`);

ALTER TABLE `secteur_industriel_agri_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_industriel_agri_employes`
  ADD CONSTRAINT `fk_secteur_industriel_agri_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_agri_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_agri_ouvriers
ALTER TABLE `secteur_industriel_agri_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_agri_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_agri_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_industriel_agri_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_industriel_agri_ouvriers`
  ADD CONSTRAINT `fk_secteur_industriel_agri_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_agri_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_bois_agents
ALTER TABLE `secteur_industriel_bois_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_bois_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_bois_agents_company` (`company_id`);

ALTER TABLE `secteur_industriel_bois_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_bois_agents`
  ADD CONSTRAINT `fk_secteur_industriel_bois_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_bois_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_bois_cadres
ALTER TABLE `secteur_industriel_bois_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_bois_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_bois_cadres_company` (`company_id`);

ALTER TABLE `secteur_industriel_bois_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_bois_cadres`
  ADD CONSTRAINT `fk_secteur_industriel_bois_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_bois_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_bois_chauffeurs
ALTER TABLE `secteur_industriel_bois_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_bois_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_bois_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_industriel_bois_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_industriel_bois_chauffeurs`
  ADD CONSTRAINT `fk_secteur_industriel_bois_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_bois_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_bois_employes
ALTER TABLE `secteur_industriel_bois_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_bois_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_bois_employes_company` (`company_id`);

ALTER TABLE `secteur_industriel_bois_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_industriel_bois_employes`
  ADD CONSTRAINT `fk_secteur_industriel_bois_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_bois_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_bois_ouvriers
ALTER TABLE `secteur_industriel_bois_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_bois_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_bois_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_industriel_bois_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_industriel_bois_ouvriers`
  ADD CONSTRAINT `fk_secteur_industriel_bois_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_bois_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_employes
ALTER TABLE `secteur_industriel_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_employes_company` (`company_id`);

ALTER TABLE `secteur_industriel_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_industriel_employes`
  ADD CONSTRAINT `fk_secteur_industriel_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_ouvriers
ALTER TABLE `secteur_industriel_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_industriel_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_industriel_ouvriers`
  ADD CONSTRAINT `fk_secteur_industriel_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_poly_agents
ALTER TABLE `secteur_industriel_poly_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_poly_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_poly_agents_company` (`company_id`);

ALTER TABLE `secteur_industriel_poly_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_industriel_poly_agents`
  ADD CONSTRAINT `fk_secteur_industriel_poly_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_poly_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_poly_cadres
ALTER TABLE `secteur_industriel_poly_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_poly_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_poly_cadres_company` (`company_id`);

ALTER TABLE `secteur_industriel_poly_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_industriel_poly_cadres`
  ADD CONSTRAINT `fk_secteur_industriel_poly_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_poly_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_poly_employes
ALTER TABLE `secteur_industriel_poly_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_poly_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_poly_employes_company` (`company_id`);

ALTER TABLE `secteur_industriel_poly_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_industriel_poly_employes`
  ADD CONSTRAINT `fk_secteur_industriel_poly_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_poly_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_poly_ouvriers
ALTER TABLE `secteur_industriel_poly_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_poly_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_poly_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_industriel_poly_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_industriel_poly_ouvriers`
  ADD CONSTRAINT `fk_secteur_industriel_poly_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_poly_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_sucre_agents
ALTER TABLE `secteur_industriel_sucre_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_sucre_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_sucre_agents_company` (`company_id`);

ALTER TABLE `secteur_industriel_sucre_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_sucre_agents`
  ADD CONSTRAINT `fk_secteur_industriel_sucre_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_sucre_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_sucre_cadres
ALTER TABLE `secteur_industriel_sucre_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_sucre_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_sucre_cadres_company` (`company_id`);

ALTER TABLE `secteur_industriel_sucre_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_sucre_cadres`
  ADD CONSTRAINT `fk_secteur_industriel_sucre_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_sucre_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_sucre_chauffeurs
ALTER TABLE `secteur_industriel_sucre_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_sucre_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_sucre_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_industriel_sucre_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_industriel_sucre_chauffeurs`
  ADD CONSTRAINT `fk_secteur_industriel_sucre_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_sucre_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_sucre_employes
ALTER TABLE `secteur_industriel_sucre_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_sucre_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_sucre_employes_company` (`company_id`);

ALTER TABLE `secteur_industriel_sucre_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_industriel_sucre_employes`
  ADD CONSTRAINT `fk_secteur_industriel_sucre_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_sucre_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_sucre_ouvriers
ALTER TABLE `secteur_industriel_sucre_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_sucre_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_sucre_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_industriel_sucre_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_industriel_sucre_ouvriers`
  ADD CONSTRAINT `fk_secteur_industriel_sucre_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_sucre_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_text_agents
ALTER TABLE `secteur_industriel_text_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_text_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_text_agents_company` (`company_id`);

ALTER TABLE `secteur_industriel_text_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_text_agents`
  ADD CONSTRAINT `fk_secteur_industriel_text_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_text_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_text_cadres
ALTER TABLE `secteur_industriel_text_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_text_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_text_cadres_company` (`company_id`);

ALTER TABLE `secteur_industriel_text_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_text_cadres`
  ADD CONSTRAINT `fk_secteur_industriel_text_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_text_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_text_chauffeurs
ALTER TABLE `secteur_industriel_text_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_text_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_text_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_industriel_text_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_industriel_text_chauffeurs`
  ADD CONSTRAINT `fk_secteur_industriel_text_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_text_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_text_employes
ALTER TABLE `secteur_industriel_text_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_text_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_text_employes_company` (`company_id`);

ALTER TABLE `secteur_industriel_text_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_industriel_text_employes`
  ADD CONSTRAINT `fk_secteur_industriel_text_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_text_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_text_ouvriers
ALTER TABLE `secteur_industriel_text_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_text_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_text_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_industriel_text_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_industriel_text_ouvriers`
  ADD CONSTRAINT `fk_secteur_industriel_text_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_text_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;


ALTER TABLE `secteur_industriel_thon_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_thon_agents` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_thon_agents` (`company_id`);

ALTER TABLE `secteur_industriel_thon_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `secteur_industriel_thon_agents`
  ADD CONSTRAINT `fk_secteur_industriel_thon_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_thon_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_thon_agents
ALTER TABLE `secteur_industriel_thon_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_thon_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_thon_agents_company` (`company_id`);

ALTER TABLE `secteur_industriel_thon_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_thon_agents`
  ADD CONSTRAINT `fk_secteur_industriel_thon_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_thon_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_thon_cadres
ALTER TABLE `secteur_industriel_thon_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_thon_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_thon_cadres_company` (`company_id`);

ALTER TABLE `secteur_industriel_thon_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_industriel_thon_cadres`
  ADD CONSTRAINT `fk_secteur_industriel_thon_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_thon_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_thon_chauffeurs
ALTER TABLE `secteur_industriel_thon_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_thon_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_thon_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_industriel_thon_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_industriel_thon_chauffeurs`
  ADD CONSTRAINT `fk_secteur_industriel_thon_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_thon_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_thon_employes
ALTER TABLE `secteur_industriel_thon_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_thon_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_thon_employes_company` (`company_id`);

ALTER TABLE `secteur_industriel_thon_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_industriel_thon_employes`
  ADD CONSTRAINT `fk_secteur_industriel_thon_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_thon_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_industriel_thon_ouvriers
ALTER TABLE `secteur_industriel_thon_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_industriel_thon_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_industriel_thon_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_industriel_thon_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_industriel_thon_ouvriers`
  ADD CONSTRAINT `fk_secteur_industriel_thon_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_industriel_thon_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_indus_agents
ALTER TABLE `secteur_indus_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_indus_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_indus_agents_company` (`company_id`);

ALTER TABLE `secteur_indus_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_indus_agents`
  ADD CONSTRAINT `fk_secteur_indus_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_indus_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_indus_cadres
ALTER TABLE `secteur_indus_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_indus_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_indus_cadres_company` (`company_id`);

ALTER TABLE `secteur_indus_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `secteur_indus_cadres`
  ADD CONSTRAINT `fk_secteur_indus_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_indus_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maison_employes
ALTER TABLE `secteur_maison_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maison_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maison_employes_company` (`company_id`);

ALTER TABLE `secteur_maison_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `secteur_maison_employes`
  ADD CONSTRAINT `fk_secteur_maison_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maison_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_capits
ALTER TABLE `secteur_maritime_capits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_capits_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_capits_company` (`company_id`);

ALTER TABLE `secteur_maritime_capits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_capits`
  ADD CONSTRAINT `fk_secteur_maritime_capits_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_capits_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_chef_mecas
ALTER TABLE `secteur_maritime_chef_mecas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_chef_mecas_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_chef_mecas_company` (`company_id`);

ALTER TABLE `secteur_maritime_chef_mecas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_chef_mecas`
  ADD CONSTRAINT `fk_secteur_maritime_chef_mecas_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_chef_mecas_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_machines
ALTER TABLE `secteur_maritime_machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_machines_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_machines_company` (`company_id`);

ALTER TABLE `secteur_maritime_machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_machines`
  ADD CONSTRAINT `fk_secteur_maritime_machines_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_machines_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_maitres
ALTER TABLE `secteur_maritime_maitres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_maitres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_maitres_company` (`company_id`);

ALTER TABLE `secteur_maritime_maitres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_maitres`
  ADD CONSTRAINT `fk_secteur_maritime_maitres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_maitres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_matelos
ALTER TABLE `secteur_maritime_matelos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_matelos_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_matelos_company` (`company_id`);

ALTER TABLE `secteur_maritime_matelos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_matelos`
  ADD CONSTRAINT `fk_secteur_maritime_matelos_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_matelos_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_polies
ALTER TABLE `secteur_maritime_polies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_polies_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_polies_company` (`company_id`);

ALTER TABLE `secteur_maritime_polies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_polies`
  ADD CONSTRAINT `fk_secteur_maritime_polies_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_polies_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_second_capits
ALTER TABLE `secteur_maritime_second_capits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_second_capits_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_second_capits_company` (`company_id`);

ALTER TABLE `secteur_maritime_second_capits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_second_capits`
  ADD CONSTRAINT `fk_secteur_maritime_second_capits_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_second_capits_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_maritime_second_mecas
ALTER TABLE `secteur_maritime_second_mecas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_maritime_second_mecas_sector` (`id_secteur`),
  ADD KEY `fk_secteur_maritime_second_mecas_company` (`company_id`);

ALTER TABLE `secteur_maritime_second_mecas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `secteur_maritime_second_mecas`
  ADD CONSTRAINT `fk_secteur_maritime_second_mecas_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_maritime_second_mecas_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_nettoyage_chauffeurs
ALTER TABLE `secteur_nettoyage_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_nettoyage_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_nettoyage_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_nettoyage_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `secteur_nettoyage_chauffeurs`
  ADD CONSTRAINT `fk_secteur_nettoyage_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_nettoyage_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_nettoyage_employes
ALTER TABLE `secteur_nettoyage_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_nettoyage_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_nettoyage_employes_company` (`company_id`);

ALTER TABLE `secteur_nettoyage_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_nettoyage_employes`
  ADD CONSTRAINT `fk_secteur_nettoyage_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_nettoyage_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_nettoyage_ouvriers
ALTER TABLE `secteur_nettoyage_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_nettoyage_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_nettoyage_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_nettoyage_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `secteur_nettoyage_ouvriers`
  ADD CONSTRAINT `fk_secteur_nettoyage_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_nettoyage_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_boscs
ALTER TABLE `secteur_peches_boscs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_boscs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_boscs_company` (`company_id`);

ALTER TABLE `secteur_peches_boscs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `secteur_peches_boscs`
  ADD CONSTRAINT `fk_secteur_peches_boscs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_boscs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_brevets
ALTER TABLE `secteur_peches_brevets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_brevets_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_brevets_company` (`company_id`);

ALTER TABLE `secteur_peches_brevets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `secteur_peches_brevets`
  ADD CONSTRAINT `fk_secteur_peches_brevets_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_brevets_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_capits
ALTER TABLE `secteur_peches_capits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_capits_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_capits_company` (`company_id`);

ALTER TABLE `secteur_peches_capits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `secteur_peches_capits`
  ADD CONSTRAINT `fk_secteur_peches_capits_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_capits_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_chef_moteurs
ALTER TABLE `secteur_peches_chef_moteurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_chef_moteurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_chef_moteurs_company` (`company_id`);

ALTER TABLE `secteur_peches_chef_moteurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `secteur_peches_chef_moteurs`
  ADD CONSTRAINT `fk_secteur_peches_chef_moteurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_chef_moteurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_cotiere_boscos
ALTER TABLE `secteur_peches_cotiere_boscos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_cotiere_boscos_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_cotiere_boscos_company` (`company_id`);

ALTER TABLE `secteur_peches_cotiere_boscos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `secteur_peches_cotiere_boscos`
  ADD CONSTRAINT `fk_secteur_peches_cotiere_boscos_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_cotiere_boscos_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_cotiere_capis
ALTER TABLE `secteur_peches_cotiere_capis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_cotiere_capis_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_cotiere_capis_company` (`company_id`);

ALTER TABLE `secteur_peches_cotiere_capis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_peches_cotiere_capis`
  ADD CONSTRAINT `fk_secteur_peches_cotiere_capis_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_cotiere_capis_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_cotiere_capis_capas
ALTER TABLE `secteur_peches_cotiere_capis_capas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_cotiere_capis_capas_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_cotiere_capis_capas_company` (`company_id`);

ALTER TABLE `secteur_peches_cotiere_capis_capas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `secteur_peches_cotiere_capis_capas`
  ADD CONSTRAINT `fk_secteur_peches_cotiere_capis_capas_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_cotiere_capis_capas_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_cotiere_eleves
ALTER TABLE `secteur_peches_cotiere_eleves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_cotiere_eleves_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_cotiere_eleves_company` (`company_id`);

ALTER TABLE `secteur_peches_cotiere_eleves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `secteur_peches_cotiere_eleves`
  ADD CONSTRAINT `fk_secteur_peches_cotiere_eleves_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_cotiere_eleves_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_cotiere_matlots
ALTER TABLE `secteur_peches_cotiere_matlots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_cotiere_matlots_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_cotiere_matlots_company` (`company_id`);

ALTER TABLE `secteur_peches_cotiere_matlots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `secteur_peches_cotiere_matlots`
  ADD CONSTRAINT `fk_secteur_peches_cotiere_matlots_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_cotiere_matlots_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_peches_cotiere_matlot_simpls
ALTER TABLE `secteur_peches_cotiere_matlot_simpls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_peches_cotiere_matlot_simpls_sector` (`id_secteur`),
  ADD KEY `fk_secteur_peches_cotiere_matlot_simpls_company` (`company_id`);

ALTER TABLE `secteur_peches_cotiere_matlot_simpls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `secteur_peches_cotiere_matlot_simpls`
  ADD CONSTRAINT `fk_secteur_peches_cotiere_matlot_simpls_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_peches_cotiere_matlot_simpls_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour les autres tables de pêche (structure similaire)
-- Je continue avec le même pattern pour toutes les tables restantes...

-- Index pour la table secteur_petro_dist_agents
ALTER TABLE `secteur_petro_dist_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_dist_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_dist_agents_company` (`company_id`);

ALTER TABLE `secteur_petro_dist_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_dist_agents`
  ADD CONSTRAINT `fk_secteur_petro_dist_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_dist_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_dist_cadres
ALTER TABLE `secteur_petro_dist_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_dist_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_dist_cadres_company` (`company_id`);

ALTER TABLE `secteur_petro_dist_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_dist_cadres`
  ADD CONSTRAINT `fk_secteur_petro_dist_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_dist_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_dist_chauffeurs
ALTER TABLE `secteur_petro_dist_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_dist_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_dist_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_petro_dist_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_dist_chauffeurs`
  ADD CONSTRAINT `fk_secteur_petro_dist_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_dist_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_dist_employes
ALTER TABLE `secteur_petro_dist_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_dist_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_dist_employes_company` (`company_id`);

ALTER TABLE `secteur_petro_dist_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_dist_employes`
  ADD CONSTRAINT `fk_secteur_petro_dist_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_dist_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_prod_agents
ALTER TABLE `secteur_petro_prod_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_prod_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_prod_agents_company` (`company_id`);

ALTER TABLE `secteur_petro_prod_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_prod_agents`
  ADD CONSTRAINT `fk_secteur_petro_prod_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_prod_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_prod_cadres
ALTER TABLE `secteur_petro_prod_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_prod_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_prod_cadres_company` (`company_id`);

ALTER TABLE `secteur_petro_prod_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_prod_cadres`
  ADD CONSTRAINT `fk_secteur_petro_prod_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_prod_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_prod_chauffeurs
ALTER TABLE `secteur_petro_prod_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_prod_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_prod_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_petro_prod_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_prod_chauffeurs`
  ADD CONSTRAINT `fk_secteur_petro_prod_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_prod_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_prod_employes
ALTER TABLE `secteur_petro_prod_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_prod_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_prod_employes_company` (`company_id`);

ALTER TABLE `secteur_petro_prod_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_prod_employes`
  ADD CONSTRAINT `fk_secteur_petro_prod_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_prod_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_petro_prod_ouvriers
ALTER TABLE `secteur_petro_prod_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_petro_prod_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_petro_prod_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_petro_prod_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_petro_prod_ouvriers`
  ADD CONSTRAINT `fk_secteur_petro_prod_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_petro_prod_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_securite_chauffeurs
ALTER TABLE `secteur_securite_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_securite_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_securite_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_securite_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_securite_chauffeurs`
  ADD CONSTRAINT `fk_secteur_securite_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_securite_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_securite_employes
ALTER TABLE `secteur_securite_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_securite_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_securite_employes_company` (`company_id`);

ALTER TABLE `secteur_securite_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_securite_employes`
  ADD CONSTRAINT `fk_secteur_securite_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_securite_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_tourisms
ALTER TABLE `secteur_tourisms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_tourisms_sector` (`id_secteur`),
  ADD KEY `fk_secteur_tourisms_company` (`company_id`);

ALTER TABLE `secteur_tourisms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_tourisms`
  ADD CONSTRAINT `fk_secteur_tourisms_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_tourisms_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_tourisms_cadres
ALTER TABLE `secteur_tourisms_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_tourisms_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_tourisms_cadres_company` (`company_id`);

ALTER TABLE `secteur_tourisms_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_tourisms_cadres`
  ADD CONSTRAINT `fk_secteur_tourisms_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_tourisms_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_tourisms_matrises
ALTER TABLE `secteur_tourisms_matrises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_tourisms_matrises_sector` (`id_secteur`),
  ADD KEY `fk_secteur_tourisms_matrises_company` (`company_id`);

ALTER TABLE `secteur_tourisms_matrises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_tourisms_matrises`
  ADD CONSTRAINT `fk_secteur_tourisms_matrises_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_tourisms_matrises_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_transport_cadres
ALTER TABLE `secteur_transport_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_transport_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_transport_cadres_company` (`company_id`);

ALTER TABLE `secteur_transport_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_transport_cadres`
  ADD CONSTRAINT `fk_secteur_transport_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_transport_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_transport_chauffeurs
ALTER TABLE `secteur_transport_chauffeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_transport_chauffeurs_sector` (`id_secteur`),
  ADD KEY `fk_secteur_transport_chauffeurs_company` (`company_id`);

ALTER TABLE `secteur_transport_chauffeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_transport_chauffeurs`
  ADD CONSTRAINT `fk_secteur_transport_chauffeurs_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_transport_chauffeurs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_transport_ouvriers
ALTER TABLE `secteur_transport_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_transport_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_transport_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_transport_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_transport_ouvriers`
  ADD CONSTRAINT `fk_secteur_transport_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_transport_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_trps_aerien_agents
ALTER TABLE `secteur_trps_aerien_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_trps_aerien_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_trps_aerien_agents_company` (`company_id`);

ALTER TABLE `secteur_trps_aerien_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_trps_aerien_agents`
  ADD CONSTRAINT `fk_secteur_trps_aerien_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_trps_aerien_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_trps_aerien_cadres
ALTER TABLE `secteur_trps_aerien_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_trps_aerien_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_trps_aerien_cadres_company` (`company_id`);

ALTER TABLE `secteur_trps_aerien_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_trps_aerien_cadres`
  ADD CONSTRAINT `fk_secteur_trps_aerien_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_trps_aerien_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_trps_aerien_cadres_sups
ALTER TABLE `secteur_trps_aerien_cadres_sups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_trps_aerien_cadres_sups_sector` (`id_secteur`),
  ADD KEY `fk_secteur_trps_aerien_cadres_sups_company` (`company_id`);

ALTER TABLE `secteur_trps_aerien_cadres_sups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_trps_aerien_cadres_sups`
  ADD CONSTRAINT `fk_secteur_trps_aerien_cadres_sups_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_trps_aerien_cadres_sups_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_trps_aerien_ouvriers
ALTER TABLE `secteur_trps_aerien_ouvriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_trps_aerien_ouvriers_sector` (`id_secteur`),
  ADD KEY `fk_secteur_trps_aerien_ouvriers_company` (`company_id`);

ALTER TABLE `secteur_trps_aerien_ouvriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_trps_aerien_ouvriers`
  ADD CONSTRAINT `fk_secteur_trps_aerien_ouvriers_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_trps_aerien_ouvriers_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_trps_fond_agents
ALTER TABLE `secteur_trps_fond_agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_trps_fond_agents_sector` (`id_secteur`),
  ADD KEY `fk_secteur_trps_fond_agents_company` (`company_id`);

ALTER TABLE `secteur_trps_fond_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_trps_fond_agents`
  ADD CONSTRAINT `fk_secteur_trps_fond_agents_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_trps_fond_agents_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_trps_fond_cadres
ALTER TABLE `secteur_trps_fond_cadres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_trps_fond_cadres_sector` (`id_secteur`),
  ADD KEY `fk_secteur_trps_fond_cadres_company` (`company_id`);

ALTER TABLE `secteur_trps_fond_cadres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_trps_fond_cadres`
  ADD CONSTRAINT `fk_secteur_trps_fond_cadres_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_trps_fond_cadres_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- Index pour la table secteur_trps_fond_employes
ALTER TABLE `secteur_trps_fond_employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_secteur_trps_fond_employes_sector` (`id_secteur`),
  ADD KEY `fk_secteur_trps_fond_employes_company` (`company_id`);

ALTER TABLE `secteur_trps_fond_employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secteur_trps_fond_employes`
  ADD CONSTRAINT `fk_secteur_trps_fond_employes_sector` FOREIGN KEY (`id_secteur`) REFERENCES `sectors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_secteur_trps_fond_employes_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;
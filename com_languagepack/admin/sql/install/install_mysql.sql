--
-- Table structure for table `#__languagepack_sources`
--
CREATE TABLE IF NOT EXISTS `#__languagepack_sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `const` VARCHAR(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__languagepack_jversions`
--
CREATE TABLE IF NOT EXISTS `#__languagepack_jversions` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__languagepack_languages`
-- TODO: Foreign Key on ARS Category
--
CREATE TABLE IF NOT EXISTS `#__languagepack_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `lang_code` VARCHAR(7) NOT NULL,
  `jversion_id` INT(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `ars_category` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_languagepack_languages_group_id` FOREIGN KEY (`group_id`) REFERENCES `#__usergroups` (`id`)  ON DELETE NO ACTION,
  CONSTRAINT `fk_languagepack_languages_source_id` FOREIGN KEY (`source_id`) REFERENCES `#__languagepack_sources` (`id`)  ON DELETE NO ACTION,
  CONSTRAINT `fk_languagepack_languages_joomla_version` FOREIGN KEY (`jversion_id`) REFERENCES `#__languagepack_jversions` (`id`)  ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__languagepack_releases`
-- TODO: Foreign Key on ARS Releases
--
CREATE TABLE IF NOT EXISTS `#__languagepack_releases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  -- int(11) because it references our users table. This is stupid and should be fixed in J4
  `maintainer_id` int(11) NOT NULL,
  `release_name` VARCHAR(5) NOT NULL,
  `ars_release_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_languagepack_releases_maintainer_id` FOREIGN KEY (`maintainer_id`) REFERENCES `#__users` (`id`)  ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Populate data into the sources table
--
INSERT INTO `#__languagepack_sources` (`const`, `name`) VALUES
('github', 'LANGUAGE_PACK_SOURCE_GITHUB'),
('crowdin', 'LANGUAGE_PACK_SOURCE_CROWDIN');

--
-- Populate data into the Joomla Versions table
--
INSERT INTO `#__languagepack_jversions` (`name`) VALUES
('COM_LANGUAGE_PACK_JOOMLA_VERSION_1_5'),
('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5'),
('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_X');

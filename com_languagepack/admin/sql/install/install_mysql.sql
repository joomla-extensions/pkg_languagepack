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
-- Table structure for table `#__languagepack_applications`
--
CREATE TABLE IF NOT EXISTS `#__languagepack_applications` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(100) NOT NULL,
  `ars_environment` bigint(20) NOT NULL,
  `ars_visual_group` bigint(20) NOT NULL,
  `s3_path` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_languagepack_languages_ars_environment` FOREIGN KEY (`ars_environment`) REFERENCES `#__ars_environments` (`id`)  ON DELETE NO ACTION,
  CONSTRAINT `fk_languagepack_languages_ars_visual_group` FOREIGN KEY (`ars_visual_group`) REFERENCES `#__ars_vgroups` (`id`)  ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__languagepack_languages`
--
CREATE TABLE IF NOT EXISTS `#__languagepack_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `lang_code` VARCHAR(7) NOT NULL,
  `application_id` INT(10) unsigned NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `ars_category` bigint(20) NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_languagepack_languages_group_id` FOREIGN KEY (`group_id`) REFERENCES `#__usergroups` (`id`)  ON DELETE NO ACTION,
  CONSTRAINT `fk_languagepack_languages_source_id` FOREIGN KEY (`source_id`) REFERENCES `#__languagepack_sources` (`id`)  ON DELETE NO ACTION,
  CONSTRAINT `fk_languagepack_languages_application_id` FOREIGN KEY (`application_id`) REFERENCES `#__languagepack_applications` (`id`)  ON DELETE NO ACTION,
  CONSTRAINT `fk_languagepack_languages_ars_category` FOREIGN KEY (`ars_category`) REFERENCES `#__ars_categories` (`id`)  ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__languagepack_releases`
-- TODO: Foreign Key on ARS Releases
-- TODO: Unique key on release name. Release Name should allow longer name
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
('crowdin', 'LANGUAGE_PACK_SOURCE_CROWDIN'),
('upload', 'LANGUAGE_PACK_SOURCE_UPLOAD');

--
-- Populate data into the Joomla Versions table
--
INSERT INTO `#__languagepack_applications` (`name`, `description`, `alias`, `ars_environment`, `ars_visual_group`, `s3_path`) VALUES
('COM_LANGUAGE_PACK_JOOMLA_VERSION_1_0', 'COM_LANGUAGE_PACK_JOOMLA_VERSION_1_0_DESC', 'translation10', 10, 7, 'joomladownloads/translations/joomla10/'),
('COM_LANGUAGE_PACK_JOOMLA_VERSION_1_5', 'COM_LANGUAGE_PACK_JOOMLA_VERSION_1_5_DESC', 'translation15', 3, 2, 'joomladownloads/translations/joomla15/'),
('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5', 'COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_DESC', 'translation25', 2, 3, 'joomladownloads/translations/joomla25/'),
('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_X', 'COM_LANGUAGE_PACK_JOOMLA_VERSION_3_x_DESC', 'translation3', 1, 4, 'joomladownloads/translations/joomla3/'),
('COM_LANGUAGE_PACK_JOOMLA_VERSION_4_X', 'COM_LANGUAGE_PACK_JOOMLA_VERSION_4_x_DESC', 'translation4', 11, 8, 'joomladownloads/translations/joomla4/');

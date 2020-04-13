--
-- Table structure for table `#__languagepack_languages`
-- TODO: Foreign Key on ARS Category
--
CREATE TABLE IF NOT EXISTS `#__languagepack_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` VARCHAR(5) NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `ars_category` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT "fk_languagepack_languages_group_id" FOREIGN KEY ("group_id") REFERENCES "#__usergroups" ("id")  ON DELETE CASCADE,
  CONSTRAINT "fk_languagepack_languages_source_id" FOREIGN KEY ("source_id") REFERENCES "#__languagepack_sources" ("id")  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `#__languagepack_releases`
-- TODO: Foreign Key on ARS Releases
--
CREATE TABLE IF NOT EXISTS `#__languagepack_releases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `maintainer_id` int(10) unsigned NOT NULL,
  `release_name` VARCHAR(5) NOT NULL,
  `ars_release_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Populate data into the sources table
--
INSERT INTO `#__languagepack_sources` (`const`, `name`) VALUES
('github', 'LANGUAGE_PACK_SOURCE_GITHUB'),
('crowdin', 'LANGUAGE_PACK_SOURCE_CROWDIN');

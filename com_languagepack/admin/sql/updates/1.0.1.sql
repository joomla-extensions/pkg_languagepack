ALTER TABLE `#__languagepack_applications` ADD `state` int(1) DEFAULT 1;
ALTER TABLE `#__languagepack_applications` ADD `locked` int(1) DEFAULT 0;
ALTER TABLE `#__languagepack_languages` ADD `state` int(1) DEFAULT 1;
ALTER TABLE `#__languagepack_languages` ADD `locked` int(1) DEFAULT 0;

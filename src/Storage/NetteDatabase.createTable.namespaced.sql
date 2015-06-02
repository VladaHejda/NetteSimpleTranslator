CREATE TABLE `localization_text` (
  `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ns` varchar(24) NULL,
  `text` varchar(255) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE (`ns`, `text`)
) ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_bin COMMENT='default texts for translations';

CREATE TABLE `localization` (
  `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `text_id` int(1) UNSIGNED NOT NULL,
  `lang` char(2) NOT NULL,
  `variant` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `translation` varchar(255) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE (`text_id`, `lang`, `variant`),
  CONSTRAINT `x` FOREIGN KEY (`text_id`) REFERENCES `localization_text` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='text translations';

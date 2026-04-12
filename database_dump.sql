CREATE TABLE `short_url` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_url` varchar(2048) NOT NULL,
  `short_code` varchar(32) NOT NULL,
  `hits_count` int NOT NULL DEFAULT 0,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_short_code` (`short_code`),
  KEY `idx-short_url-original_url` (`original_url`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `short_url_hit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `short_url_id` int NOT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` varchar(2048) DEFAULT NULL,
  `referer` varchar(2048) DEFAULT NULL,
  `created_at` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-short_url_hit-short_url_id` (`short_url_id`),
  CONSTRAINT `fk-short_url_hit-short_url_id`
    FOREIGN KEY (`short_url_id`) REFERENCES `short_url` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

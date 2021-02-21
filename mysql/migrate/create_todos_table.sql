CREATE TABLE IF NOT EXISTS `todo` (
    `id` INT unsigned NOT NULL auto_increment,
    `title` VARCHAR(255) NOT NULL,
    `comment` TEXT,
    `state` TINYINT(3) NOT NULL DEFAULT 1, 
    `deadline` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
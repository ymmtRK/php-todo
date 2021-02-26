CREATE TABLE IF NOT EXISTS `todo` (
    `id` INT unsigned NOT NULL auto_increment,
    `date` TIMESTAMP NOT NULL,
    `text` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
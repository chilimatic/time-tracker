-- create the database on the system
CREATE DATABASE `time-tracker` /*!40100 DEFAULT CHARACTER SET utf8 */;
-- create the user
CREATE user 'time-tracker'@'localhost' IDENTIFIED BY 'Aef8naelWohGho4lAecu9chaOoHu3soh';
-- give the user permission to the necessary actions on the database
GRANT SELECT,INSERT, UPDATE, DELETE ON `time_tracker`.* to 'time-tracker'@'localhost';

-- user table
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `password` VARCHAR(512) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created` TIMESTAMP NOT NULL,
  `modified` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `project` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `created` TIMESTAMP NOT NULL,
  `modified` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `time-tracker`.`session` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `project_id` INT UNSIGNED NOT NULL,
  `start_time` TIMESTAMP NOT NULL,
  `end_time` TIMESTAMP NULL,
  `time_diff` FLOAT UNSIGNED NULL,
  `created` TIMESTAMP NOT NULL,
  `modified` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_time-track_1_idx` (`user_id` ASC),
  INDEX `fk_time-track_2_idx` (`project_id` ASC),
  CONSTRAINT `fk_time-track_1`
  FOREIGN KEY (`user_id`)
  REFERENCES `user` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_time-track_2`
  FOREIGN KEY (`project_id`)
  REFERENCES `project` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
  ENGINE = InnoDB;
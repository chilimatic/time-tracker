SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `user`
CHANGE COLUMN `created` `created` TIMESTAMP NOT NULL ,
ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC),
ADD INDEX `index3` (`active` ASC, `name` ASC);

ALTER TABLE `project`
CHANGE COLUMN `created` `created` TIMESTAMP NOT NULL ;

ALTER TABLE `session`
CHANGE COLUMN `start_time` `start_time` TIMESTAMP NOT NULL ,
ADD COLUMN `task_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `project_id`,
ADD INDEX `fk_session_1_idx` (`task_id` ASC),
ADD INDEX `index5` (`user_id` ASC, `project_id` ASC, `task_id` ASC);

CREATE TABLE IF NOT EXISTS `task` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `value` FLOAT(11) NOT NULL DEFAULT 0,
  `public` TINYINT(1) NOT NULL DEFAULT 0,
  `created` TIMESTAMP NOT NULL,
  `modified` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `user_project_map` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `project_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `project_id`),
  INDEX `fk_user_table_map_2_idx` (`project_id` ASC),
  CONSTRAINT `fk_user_table_map_1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_table_map_2`
    FOREIGN KEY (`project_id`)
    REFERENCES `project` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `user_project_task_map` (
  `task_id` INT(10) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `project_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`task_id`, `user_id`, `project_id`),
  INDEX `fk_user_project__task_map_1_idx` (`project_id` ASC),
  INDEX `fk_user_project__task_map_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_project__task_map_1`
    FOREIGN KEY (`project_id`)
    REFERENCES `project` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_project__task_map_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_project__task_map_3`
    FOREIGN KEY (`task_id`)
    REFERENCES `task` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

ALTER TABLE `session`
ADD CONSTRAINT `fk_session_1`
  FOREIGN KEY (`task_id`)
  REFERENCES `task` (`id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
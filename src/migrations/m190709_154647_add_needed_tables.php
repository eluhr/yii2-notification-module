<?php

use yii\db\Migration;

/**
 * Class m190709_154647_add_needed_tables
 */
class m190709_154647_add_needed_tables extends Migration
{

    /**
     * @return bool|void
     */
    public function up()
    {
        $user_group_table_name = Yii::$app->db->tablePrefix . 'message_user_group';

        $user_group_x_user_table_name = Yii::$app->db->tablePrefix . 'message_user_group_x_user';

        $message_table_name = Yii::$app->db->tablePrefix . 'message';

        $inbox_message_table_name = Yii::$app->db->tablePrefix . 'inbox_message';

        $user_table_name = Yii::$app->db->tablePrefix . 'user';

        $this->execute(<<<SQL
CREATE TABLE IF NOT EXISTS `{$user_group_table_name}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `owner_id` INT NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_group_user1_idx` (`owner_id` ASC),
  UNIQUE INDEX `idx_user_id_name` (`owner_id` ASC, `name` ASC),
  CONSTRAINT `fk_user_group_user1`
    FOREIGN KEY (`owner_id`)
    REFERENCES `{$user_table_name}` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE IF NOT EXISTS `{$user_group_x_user_table_name}` (
  `message_user_group_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`message_user_group_id`, `receiver_id`),
  INDEX `fk_user_group_x_user_user1_idx` (`receiver_id` ASC),
  INDEX `fk_user_group_x_user_user_group_idx` (`message_user_group_id` ASC),
  CONSTRAINT `fk_user_group_x_user_user_group`
    FOREIGN KEY (`message_user_group_id`)
    REFERENCES `{$user_group_table_name}` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_group_x_user_user1`
    FOREIGN KEY (`receiver_id`)
    REFERENCES `{$user_table_name}` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);

CREATE TABLE IF NOT EXISTS `{$message_table_name}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `author_id` INT NOT NULL,
  `subject` VARCHAR(128) NOT NULL,
  `text` TEXT(1024) NOT NULL,
  `send_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_message_user1_idx` (`author_id` ASC),
  CONSTRAINT `fk_message_user1`
    FOREIGN KEY (`author_id`)
    REFERENCES `{$user_table_name}` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE IF NOT EXISTS `{$inbox_message_table_name}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `read` TINYINT(1) NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_inbox_message1_idx` (`message_id` ASC),
  INDEX `fk_inbox_user1_idx` (`receiver_id` ASC),
  CONSTRAINT `fk_inbox_message1`
    FOREIGN KEY (`message_id`)
    REFERENCES `{$message_table_name}` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inbox_user1`
    FOREIGN KEY (`receiver_id`)
    REFERENCES `{$user_table_name}` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
SQL
        );
    }

    /**
     * @return bool
     */
    public function down()
    {
        echo "m190709_154647_add_needed_tables cannot be reverted.\n";
        return false;
    }

}

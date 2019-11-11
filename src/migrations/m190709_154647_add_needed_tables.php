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
        $userGroupTableName = Yii::$app->db->tablePrefix . 'message_user_group';

        $userGroupXUserTableName = Yii::$app->db->tablePrefix . 'message_user_group_x_user';

        $messageTableName = Yii::$app->db->tablePrefix . 'message';

        $inboxMessageTableName = Yii::$app->db->tablePrefix . 'inbox_message';

        $userTableName = Yii::$app->db->tablePrefix . 'user';

        $this->execute(<<<SQL
CREATE TABLE IF NOT EXISTS `{$userGroupTableName}` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `owner_id` INT(11) NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_group_user1_idx` (`owner_id` ASC),
  UNIQUE INDEX `idx_user_id_name` (`owner_id` ASC, `name` ASC),
  CONSTRAINT `fk_user_group_user1`
    FOREIGN KEY (`owner_id`)
    REFERENCES `{$userTableName}` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE IF NOT EXISTS `{$userGroupXUserTableName}` (
  `message_user_group_id` INT(11) NOT NULL,
  `receiver_id` INT(11) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`message_user_group_id`, `receiver_id`),
  INDEX `fk_user_group_x_user_user1_idx` (`receiver_id` ASC),
  INDEX `fk_user_group_x_user_user_group_idx` (`message_user_group_id` ASC),
  CONSTRAINT `fk_user_group_x_user_user_group`
    FOREIGN KEY (`message_user_group_id`)
    REFERENCES `{$userGroupTableName}` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_group_x_user_user1`
    FOREIGN KEY (`receiver_id`)
    REFERENCES `{$userTableName}` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);

CREATE TABLE IF NOT EXISTS `{$messageTableName}` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `author_id` INT(11) NOT NULL,
  `subject` VARCHAR(128) NOT NULL,
  `text` TEXT(1024) NOT NULL,
  `send_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_message_user1_idx` (`author_id` ASC),
  CONSTRAINT `fk_message_user1`
    FOREIGN KEY (`author_id`)
    REFERENCES `{$userTableName}` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE IF NOT EXISTS `{$inboxMessageTableName}` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `message_id` INT(11) NOT NULL,
  `receiver_id` INT(11) NOT NULL,
  `read` TINYINT(1) NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_inbox_message1_idx` (`message_id` ASC),
  INDEX `fk_inbox_user1_idx` (`receiver_id` ASC),
  CONSTRAINT `fk_inbox_message1`
    FOREIGN KEY (`message_id`)
    REFERENCES `{$messageTableName}` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inbox_user1`
    FOREIGN KEY (`receiver_id`)
    REFERENCES `{$userTableName}` (`id`)
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

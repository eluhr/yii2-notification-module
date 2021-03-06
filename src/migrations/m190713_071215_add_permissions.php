<?php

use yii\db\Migration;

/**
 * Class m190713_071215_add_permissions
 */
class m190713_071215_add_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $authItemTableName = Yii::$app->db->tablePrefix . 'auth_item';
        $authItemChildTableName = Yii::$app->db->tablePrefix . 'auth_item_child';

        $this->execute(<<<SQL
INSERT INTO `{$authItemTableName}` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`)
VALUES
	('notification.send_priority_mail', 2, 'User is able to send a prioritised message', NULL, NULL, 1562961684, 1562961684);
SQL
        );
        $this->execute(<<<SQL

INSERT INTO `{$authItemChildTableName}` (`parent`, `child`)
VALUES
	('NotificationAdmin', 'notification.send_priority_mail');
SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190713_071215_add_permissions cannot be reverted.\n";
        return false;
    }
}

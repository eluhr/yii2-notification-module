<?php

use yii\db\Migration;

/**
 * Class m190712_200215_add_permissions
 */
class m190712_200215_add_permissions extends Migration
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
	('notification.compose_a_message', 2, 'User is able to compose a message and send it to users', NULL, NULL, 1562961684, 1562961684),
	('notification.send_mail_to_everyone', 2, 'User is able to send a mail to every user', NULL, NULL, 1562961618, 1562961618),
	('notification.user_group', 2, 'User is able to maintain user groups and send messages to created user groups', NULL, NULL, 1562961660, 1562961660);

INSERT INTO `{$authItemTableName}` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`)
VALUES
	('NotificationAdmin', 1, 'Full access to the notification module', NULL, NULL, 1562961827, 1562961827);

INSERT INTO `{$authItemChildTableName}` (`parent`, `child`)
VALUES
	('NotificationAdmin', 'notification.compose_a_message'),
	('NotificationAdmin', 'notification.send_mail_to_everyone'),
	('NotificationAdmin', 'notification.user_group');
SQL
);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190712_200215_add_permissions cannot be reverted.\n";
        return false;
    }
}

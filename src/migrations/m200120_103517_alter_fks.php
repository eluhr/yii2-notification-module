<?php

use yii\db\Migration;

/**
 * Class m200120_103517_alter_fks
 */
class m200120_103517_alter_fks extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk_user_group_user1', '{{%message_user_group}}');
        $this->addForeignKey('fk_user_group_user1', '{{%message_user_group}}', 'owner_id', '{{%user}}', 'id', 'CASCADE',
            'CASCADE');
        $this->dropForeignKey('fk_user_group_x_user_user1', '{{%message_user_group_x_user}}');
        $this->addForeignKey('fk_user_group_x_user_user1', '{{%message_user_group_x_user}}', 'receiver_id', '{{%user}}',
            'id', 'CASCADE', 'CASCADE');
        $this->dropForeignKey('fk_message_user1', '{{%message}}');
        $this->addForeignKey('fk_message_user1', '{{%message}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->dropForeignKey('fk_inbox_user1', '{{%inbox_message}}');
        $this->addForeignKey('fk_inbox_user1', '{{%inbox_message}}', 'receiver_id', '{{%user}}', 'id', 'CASCADE',
            'CASCADE');
    }

    public function down()
    {
        echo "m200120_103517_alter_fks cannot be reverted.\n";
        return false;
    }

}

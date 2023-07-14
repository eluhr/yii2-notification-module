<?php

use yii\db\Migration;

/**
 * Class m230714_081102_add_message_recipient_table
 */
class m230714_081102_add_message_recipient_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%message_recipient}}', [
            'id' => $this->primaryKey(),
            'recipient_id' => $this->integer()->null(),
            'recipient_group_id' => $this->integer()->null(),
            'message_id' => $this->integer()->notNull(),
            'is_read' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey('fk_message_recipient_ri', '{{%message_recipient}}', 'recipient_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_message_recipient_rgi', '{{%message_recipient}}', 'recipient_group_id', '{{%message_user_group}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%message_recipient}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Class m191119_113407_add_preferences_table
 */
class m191119_113407_add_preferences_table extends Migration
{

    public function up()
    {
        $this->createTable('{{%message_preferences}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'wants_to_additionally_receive_messages_by_mail' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey('fk_message_preferences_user_0', '{{%message_preferences}}', 'user_id', '{{%user}}', 'id',
            'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m191119_113407_add_preferences_table cannot be reverted.\n";
        return false;
    }

}

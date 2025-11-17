<?php

use yii\db\Migration;

class m251117_082146_alter_inbox_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%inbox_message}}', 'deleted', $this->boolean()->notNull()->after('marked')->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%inbox_message}}', 'deleted');
    }
}

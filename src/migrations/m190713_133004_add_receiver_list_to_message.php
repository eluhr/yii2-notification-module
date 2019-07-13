<?php

use yii\db\Migration;

/**
 * Class m190713_133004_add_receiver_list_to_message
 */
class m190713_133004_add_receiver_list_to_message extends Migration
{

    /**
     * @return bool|void
     */
    public function up()
    {
        $this->addColumn('{{%message}}','all_receiver_ids','VARCHAR(255) NULL AFTER send_at');
        // migrate old data
        $message_table_name = Yii::$app->db->tablePrefix . 'message';
        $inbox_message_table_name = Yii::$app->db->tablePrefix . 'inbox_message';
        $this->execute(<<<SQL
UPDATE {$message_table_name} AS m SET m.all_receiver_ids = (SELECT GROUP_CONCAT(DISTINCT receiver_id) FROM {$inbox_message_table_name} AS im WHERE m.id = im.message_id);
SQL
);
    }

    /**
     * @return bool
     */
    public function down()
    {
        echo "m190713_133004_add_receiver_list_to_message cannot be reverted.\n";
        return false;
    }
}

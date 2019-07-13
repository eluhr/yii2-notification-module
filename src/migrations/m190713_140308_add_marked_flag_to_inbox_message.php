<?php

use yii\db\Migration;

/**
 * Class m190713_140308_add_marked_flag_to_inbox_message
 */
class m190713_140308_add_marked_flag_to_inbox_message extends Migration
{

    /**
     * @return bool|void
     */
    public function up()
    {
        $this->addColumn('{{%inbox_message}}','marked','TINYINT(1) NULL DEFAULT 0 AFTER `read`');
    }

    /**
     * @return bool
     */
    public function down()
    {
        echo "m190713_140308_add_marked_flag_to_inbox_message cannot be reverted.\n";
        return false;
    }

}

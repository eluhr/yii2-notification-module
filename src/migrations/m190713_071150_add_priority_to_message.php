<?php

use yii\db\Migration;

/**
 * Class m190713_071149_add_priority_to_message
 */
class m190713_071150_add_priority_to_message extends Migration
{

    /**
     * @return bool|void
     */
    public function up()
    {
        $this->addColumn('{{%message}}','priority','TINYINT(1) NULL AFTER send_at');
    }

    /**
     * @return bool
     */
    public function down()
    {
        echo "m190713_071149_add_priority_to_message cannot be reverted.\n";
        return false;
    }

}

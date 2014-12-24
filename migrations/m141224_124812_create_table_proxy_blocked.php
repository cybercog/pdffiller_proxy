<?php

use yii\db\Schema;
use yii\db\Migration;

class m141224_124812_create_table_proxy_blocked extends Migration
{
    private $_table = '{{%proxy_blocked}}';

    public function safeUp()
    {
        $this->createTable($this->_table, [
            'id'       => Schema::TYPE_INTEGER,
            'ip'       => 'varchar(30) NOT NULL',
            'table'    => 'varchar(30) NOT NULL',
            'create_date'   => Schema::TYPE_DATETIME . ' default NULL',

        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM');

        $this->createIndex('ip_index', $this->_table, 'ip');
        $this->createIndex('table_index', $this->_table, 'table');
        $this->createIndex('create_date_index', $this->_table, 'create_date');
    }

    public function safeDown()
    {
        $this->dropTable($this->_table);
    }
}

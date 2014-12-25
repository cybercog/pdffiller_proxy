<?php

namespace maxlen\proxy\models;

use Yii;

/**
 * This is the model class for table "proxy_blocked".
 *
 * @property integer $id
 * @property string $ip
 * @property string $table
 * @property string $create_date
 */
class ProxyBlocked extends \yii\db\ActiveRecord
{
    public $cnt = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proxy_blocked';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbForms');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['ip', 'table'], 'required'],
            [['create_date'], 'safe'],
            [['ip', 'table'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'table' => 'Table',
            'cnt' => 'Count of duplicates',
            'create_date' => 'Create Date',
        ];
    }
}

<?php

namespace maxlen\proxy\models;

use Yii;

/**
 * This is the model class for table "proxy_log".
 *
 * @property string $id
 * @property string $ip
 * @property string $search_engine
 * @property string $code
 * @property string $dt
 * @property string $dt_unblock
 */
class ProxyLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proxy_log';
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
            [['ip', 'code', 'dt', 'dt_unblock'], 'required'],
            [['search_engine'], 'string'],
            [['code'], 'integer'],
            [['dt', 'dt_unblock'], 'safe'],
            [['ip'], 'string', 'max' => 30]
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
            'search_engine' => 'Search Engine',
            'code' => 'Code',
            'dt' => 'Dt',
            'dt_unblock' => 'Dt Unblock',
        ];
    }
}

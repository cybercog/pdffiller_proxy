<?php

namespace maxlen\proxy\models;

use Yii;

/**
 * This is the model class for table "proxy_usa".
 *
 * @property integer $id
 * @property string $host
 * @property string $port
 * @property integer $success
 * @property integer $failure
 * @property integer $active
 * @property string $login
 * @property string $password
 * @property integer $yahoo_failure
 */
class ProxyUsa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proxy_usa';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbPdfDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['host', 'port', 'success', 'failure', 'login', 'password', 'yahoo_failure'], 'required'],
            [['success', 'failure', 'active', 'yahoo_failure'], 'integer'],
            [['host'], 'string', 'max' => 30],
            [['port'], 'string', 'max' => 10],
            [['login', 'password'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'host' => 'Host',
            'port' => 'Port',
            'success' => 'Success',
            'failure' => 'Failure',
            'active' => 'Active',
            'login' => 'Login',
            'password' => 'Password',
            'yahoo_failure' => 'Yahoo Failure',
        ];
    }
}

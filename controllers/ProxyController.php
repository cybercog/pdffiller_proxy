<?php

namespace maxlen\proxy\controllers;

use yii\web\Controller;
use maxlen\proxy\models\ProxyAdwords;
use maxlen\proxy\models\ProxyBuy;
use maxlen\proxy\models\ProxyLog;
use maxlen\proxy\models\ProxyUkraine;
use maxlen\proxy\models\ProxyUsa;

use yii\data\ActiveDataProvider;

class ProxyController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public static function GetProxy($limit = 1, $search_engine = 'google')
    {
        global $proxy;
        
        if (isset($proxy) && count($proxy) > 0) return $proxy;
        else {
            $limit = " LIMIT " . (($limit - 1) * 53) . ',53';
            
            $result = ProxyBuy::find()->joinWith('proxyLog')->where(
                    "active = :active AND (proxy_log.dt_unblock < :dt_unblock AND proxy_log.search_engine = :search_engine) OR 
                    proxy_log.search_engine = :search_engine OR proxy_log.search_engine IS NULL", 
                    [':active' => 1, ':dt_unblock' => date('Y-m-d H:i:s', time()), ':search_engine' => $search_engine])
                    ->groupBy(['id'])->orderBy('id')->limit(($limit - 1) * 53)->offset(53)->all();
            
            foreach ($result as $res) {
                $proxy[] = $res;
            }
            var_dump ($proxy);
            die();
            return $proxy;
        }
    }
}

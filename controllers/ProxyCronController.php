<?php

namespace maxlen\proxy\controllers;

use yii\console\Controller;
use yii\db\Exception;
use maxlen\proxy\models\ProxyAdwords;
use maxlen\proxy\models\ProxyBuy;
use maxlen\proxy\models\ProxyLog;
use maxlen\proxy\models\ProxyUkraine;
use maxlen\proxy\models\ProxyUsa;
use maxlen\proxy\models\ProxySpider;

class ProxyCronController extends Controller
{

    public function actionBadProxy()
    {
        $badProxies = [];
        
//        $proxyModel = ProxyBuy::find()->count();
        $proxyModel = ProxyController::GetProxy(2);
//        var_dump($proxyModel);
//        die();
        foreach($proxyModel as $proxyM) {
            $proxy = [
                'login' => $proxyM->login,
                'password' => $proxyM->password,
                'host' => $proxyM->host,
                'port' => $proxyM->port,
                ];
            $res = ProxyController::getHTML('http://pdffiller.com.ua', $proxy, true);
            
            if(isset($res['info'])) {
                if($res['info']['http_code'] !== 200) {
                    var_dump($res['info']);
                    die();
                }
            }
            
        }
//        ini_set('display_errors',1);
//        ini_set('max_execution_time', 0);
//        ini_set("memory_limit","10000M");

    }
}

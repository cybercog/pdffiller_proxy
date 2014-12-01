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
    
    private $_badProxies = [];

    public function actionBadProxy()
    {
        $this->checkProxies(ProxyBuy::find()->all());
        $this->checkProxies(ProxyAdwords::find()->all());
        $this->checkProxies(ProxyUkraine::find()->all());
        $this->checkProxies(ProxyUsa::find()->all());
        $this->checkProxies(ProxySpider::find()->all());

        \Yii::$app->mailer
            ->compose('proxyBlockReport', ['count_total' => count($this->_badProxies), 'data' => $this->_badProxies])
            ->setFrom('maxim.gavrilenko@pdffiller.com')
            ->setTo(['maxim.gavrilenko@pdffiller.com', 'koshevchenko@gmail.com'])
            ->setSubject('Proxy Not Worked')
            ->send();
    }
    
    public function checkProxies($proxyModel) {
        foreach($proxyModel as $proxyM) {
            $proxy = [
                'login' => $proxyM->login,
                'password' => $proxyM->password,
                'host' => $proxyM->host,
                'port' => $proxyM->port,
                ];
            $res = ProxyController::getHTML('http://pdffiller.com.ua', $proxy, true);
            
            if(isset($res['info'])) {
                if($res['info']['http_code'] == 0) {
                    $this->_badProxies[] = $proxy;
                }
            }
            
        }
    }
}

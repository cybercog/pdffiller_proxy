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
    private $_countBadProxies = 0;

    public function actionBadProxy()
    {
        $this->checkProxies(ProxyBuy::find()->all(), ProxyBuy::tableName());
        $this->checkProxies(ProxyAdwords::find()->all(), ProxyAdwords::tableName());
        $this->checkProxies(ProxyUkraine::find()->all(), ProxyUkraine::tableName());
        $this->checkProxies(ProxyUsa::find()->all(), ProxyUsa::tableName());
        $this->checkProxies(ProxySpider::find()->all(), ProxySpider::tableName());

        \Yii::$app->mailer
            ->compose('proxyBlockReport', ['count_total' => $this->_countBadProxies, 'data' => $this->_badProxies])
            ->setFrom('maxim.gavrilenko@pdffiller.com')
            ->setTo(['maxim.gavrilenko@pdffiller.com', 'koshevchenko@gmail.com'])
            ->setSubject('Proxy Not Worked')
            ->send();
    }
    
    /**
     * Fill blocked proxies
     * @param object $proxyModel
     * @param string $table
     */
    public function checkProxies($proxyModel, $table) {
        
        if(!isset($this->_badProxies[$table])) {
            $this->_badProxies[$table] = [];
        }
        
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
                    $this->_badProxies[$table][] = $proxy;
                    $this->_countBadProxies++;
                }
            }
        }
        
    }
}

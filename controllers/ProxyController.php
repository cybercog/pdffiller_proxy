<?php

namespace maxlen\proxy\controllers;

use yii\web\Controller;
use maxlen\proxy\models\ProxyAdwords;
use maxlen\proxy\models\ProxyBuy;
use maxlen\proxy\models\ProxyLog;
use maxlen\proxy\models\ProxyUkraine;
use maxlen\proxy\models\ProxyUsa;
use maxlen\proxy\models\ProxySpider;

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
            $result = ProxyBuy::find()->joinWith('proxyLog')->where(
                    "active = :active AND (proxy_log.dt_unblock < :dt_unblock AND proxy_log.search_engine = :search_engine) OR 
                    proxy_log.search_engine = :search_engine OR proxy_log.search_engine IS NULL", 
                    [':active' => 1, ':dt_unblock' => date('Y-m-d H:i:s', time()), ':search_engine' => $search_engine])
                    ->groupBy(['id'])->orderBy('id')->limit(53)->offset(($limit - 1) * 53)->all();
            
            foreach ($result as $res) {
                $proxy[] = $res;
            }
            return $proxy;
        }
    }
    
    public static function GetProxySpider($limit = 0, $search_engine = 'google')
    {
        global $proxy;
        
        if (isset($proxy) && count($proxy) > 0) return $proxy;
        else {
            $result = ProxySpider::find()->joinWith('proxyLog')->where(
                    "active = :active AND (proxy_log.dt_unblock < :dt_unblock AND proxy_log.search_engine = :search_engine) OR 
                    proxy_log.search_engine = :search_engine OR proxy_log.search_engine IS NULL", 
                    [':active' => 1, ':dt_unblock' => date('Y-m-d H:i:s', time()), ':search_engine' => $search_engine])
                    ->groupBy(['id'])->orderBy('id')->limit(45)->offset(($limit - 1) * 45)->all();
            
            foreach ($result as $res) {
                $proxy[] = $res;
            }
            return $proxy;
        }
    }
    
    public static function GetProxyUkraine($limit = 0, $search_engine = 'google')
    {
        global $proxy;
        
        if (isset($proxy) && count($proxy) > 0) return $proxy;
        else {
            $result = ProxyUkraine::find()->joinWith('proxyLog')->where(
                    "active = :active AND (proxy_log.dt_unblock < :dt_unblock AND proxy_log.search_engine = :search_engine) OR 
                    proxy_log.search_engine = :search_engine OR proxy_log.search_engine IS NULL", 
                    [':active' => 1, ':dt_unblock' => date('Y-m-d H:i:s', time()), ':search_engine' => $search_engine])
                    ->groupBy(['id'])->orderBy('id')->limit(50)->offset(($limit - 1) * 50)->all();
            
;            foreach ($result as $res) {
                $proxy[] = $res;
            }
            return $proxy;
        }
    }
    
    public static function GetProxyAdwords($limit = 0, $search_engine = 'google')
    {
        global $proxy;
        
        if (isset($proxy) && count($proxy) > 0) return $proxy;
        else {
            $result = ProxyAdwords::find()->joinWith('proxyLog')->where(
                    "active = :active AND (proxy_log.dt_unblock < :dt_unblock AND proxy_log.search_engine = :search_engine) OR 
                    proxy_log.search_engine = :search_engine OR proxy_log.search_engine IS NULL", 
                    [':active' => 1, ':dt_unblock' => date('Y-m-d H:i:s', time()), ':search_engine' => $search_engine])
                    ->groupBy(['id'])->orderBy('id')->all();
            
            foreach ($result as $res) {
                $proxy[] = $res;
            }
            return $proxy;
        }
    }
    
    public static function GetProxyUSA($limit = 0, $search_engine = 'google')
    {
        global $proxy;
        
        if (isset($proxy) && count($proxy) > 0) return $proxy;
        else {
            $result = ProxyUsa::find()->joinWith('proxyLog')->where(
                    "active = :active AND (proxy_log.dt_unblock < :dt_unblock AND proxy_log.search_engine = :search_engine) OR 
                    proxy_log.search_engine = :search_engine OR proxy_log.search_engine IS NULL", 
                    [':active' => 1, ':dt_unblock' => date('Y-m-d H:i:s', time()), ':search_engine' => $search_engine])
                    ->groupBy(['id'])->orderBy('id')->limit(52)->offset(($limit - 1) * 52)->all();
            
            foreach ($result as $res) {
                $proxy[] = $res;
            }
            return $proxy;
        }
    }
    
    public static function getGoogleSearchResPage($query, $proxy)
    {
        $url = 'https://www.google.com.ua/search?q='.urlencode($query);
        $useragent = self::getRandomUserAgent();
        ini_set('user_agent', $useragent);
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $url);
        curl_setopt($curl_session, CURLOPT_VERBOSE, 1);
        curl_setopt($curl_session, CURLOPT_USERAGENT, $useragent);
        curl_setopt($curl_session, CURLOPT_PROXYUSERPWD, $proxy['login'] . ':' . $proxy['password']);
        curl_setopt($curl_session, CURLOPT_PROXY, $proxy['host'] . ':' . $proxy['port']);
        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_session, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($curl_session);
        
        if(!self::checkProxyResponseCode($proxy['host'], curl_getinfo($curl_session))) {
            return false;
        }
        
        curl_close($curl_session);
        
        return $output;
    }
    
    public static function getGoogleResults($word, $host, $port, $login, $password, $start = 0, $lang = '', $is_map = false)
    {
        global $agents;
        $key = rand(0, count($agents));
        $pdf = 0;
        $useragent = self::getRandomUserAgent();
        ini_set('user_agent', $useragent);
        $url = "http://www.google.com/search?q=" . urlencode($word) . '&start=' . $start . $lang;
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $url);
        curl_setopt($curl_session, CURLOPT_VERBOSE, 1);
        curl_setopt($curl_session, CURLOPT_USERAGENT, $useragent);
        curl_setopt($curl_session, CURLOPT_PROXYUSERPWD, $login . ':' . $password);
        curl_setopt($curl_session, CURLOPT_PROXY, $host . ':' . $port);
        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_session, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl_session);
        
        if(!self::checkProxyResponseCode($host, curl_getinfo($curl_session))) {
            return false;
        }
        
        curl_close($curl_session);

        return $res;
        if(!$is_map)
            return static::getGooglePageResults($res);
        else
            return static::getGooglePageResultsMap($res);
    }
    
    public static function getGooglePageResults($googlePage){
        $res = $googlePage;
        
        $relevant = true;
        $relevantPhrase = 'In order to show you the most relevant results, we have omitted some entries';
        if(strpos($res, $relevantPhrase) !== false){
            $relevant = false;
        }
        
        if (!$res || $res == '' || strlen($res) < 10) {
            echo '-1';
            return -1;
        }

        //  ---  find number of results
        preg_match_all('/<div.*id="resultStats">(.*) results<\/div>/Us', $res, $main);
        if (!isset($main[1][0])) $main[1][0] = 0;
        $return = array();
        $return['pages'] = str_replace('About ', '', str_replace(',', '', $main[1][0]));
        //echo $main[1][0];exit;

        preg_match_all('/<div.*id="resultStats"(.*)<\/div>/Us', $res, $stats);

        if(isset($stats[1][0]) && !empty($stats[1][0])) {
            $stats = explode('(', $stats[1][0]);
            $stats = explode('nobr', $stats[0]);
            $stats = explode(';', $stats[0]);
            $google_res_count = preg_replace('~[^0-9]+~','',$stats[0]);
            /*
            $google_res_count = (int)$stats[1][0];
            $google_res_count = preg_replace("/\D/","", $stats[1][0]);
             */
        }
        else
            $google_res_count = 0;
            
        $res_or = $res;

        $res = substr($res, strpos($res, '<div id="ires">'));
        $pos = strpos($res, 'id="foot"');
        if ($pos > 0)
            $res = substr($res, 0, $pos);
        //echo $res;exit;
        preg_match_all('/<li class="g">.*<h3 class="r"><a href="\/url\?q=(.*)&amp;sa=U.*">(.*)<\/a><\/h3>.*<span class="st">(.*)<\/span>.*<\/li>/Us', $res, $main);
        //'<li class="g"><h3 class="r"><a href="\/url?q=(.*)&amp;sa=U.*">(.*)<\/a><\/h3>.*<span class="st">(.*)<\/span>.*<\/li>';
        //<li class="g"><span style="float:left"><span class="mime">[PDF]</span>&nbsp;</span><h3 class="r"><a href="/url?q=http://www.crestwoodmedcenter.com/Documents/The_Heart_Of_The_Matter.pdf&amp;sa=U&amp;ei=SsFKUbLhAciOtQb6tICwBA&amp;ved=0CBgQFjAA&amp;usg=AFQjCNEOBEX6AX-cVdei-nJo8fl-rkmCdw">The_Heart_Of_The_Matter - Crestwood Medical Center</a></h3><div class="s"><div class="kv" style="margin-bottom:2px"><cite>www.crestwoodmedcenter.com/Documents/The_Heart_Of_The_Matter.pdf</cite><span class="flc"> - <a href="/url?q=http://webcache.googleusercontent.com/search%3Fq%3Dcache:zS1YjSgDAuwJ:http://www.crestwoodmedcenter.com/Documents/The_Heart_Of_The_Matter.pdf%252Bfiletype:pdf%2Bsite:www.crestwoodmedcenter.com%26hl%3Den%26ct%3Dclnk&amp;sa=U&amp;ei=SsFKUbLhAciOtQb6tICwBA&amp;ved=0CBkQIDAA&amp;usg=AFQjCNHTJ1LMFBueE6pyIc2v8pDOlYvjng">Cached</a></span></div><span class="st">PREMIER PATIENT EXPERIENCE. Heart of the Matter. Hospital proves cardiac <br>  procedure is safe and is now fighting to keep the service available <b>...</b></span><br></div></li>
        //print_r($main);exit;
        preg_match_all('/<p class="_Bmc" style="margin:3px 8px"><a href="(.*)">(.*)<\/a><\/p>/Us', $res, $offen_seek);
        
        $return['links'] = $main[1];
        $return['titles'] = $main[2];
        $return['descriptions'] = $main[3];
        $return['google_res_count'] = $google_res_count;
        $return['offen_seek_links'] = $offen_seek[1];
        $return['offen_seek_text'] = preg_replace('~(<b>|</b>)~','',$offen_seek[2]);
        $return['relevant'] = $relevant;

        if (count($return['links']) == 0 && strpos($res_or, '302 Moved') !== false && strpos($res_or, 'The document has moved') !== false)
            return -2;

        return $return;
    }
    
    public static function getGooglePageResultsMap($res){
        $relevant = true;
        $relevantPhrase = 'In order to show you the most relevant results, we have omitted some entries';
        if(strpos($res, $relevantPhrase) !== false){
            $relevant = false;
        }
        
        if (!$res || $res == '' || strlen($res) < 10) {
            echo '-1';
            return -1;
        }
        
        preg_match_all('/<table class="ts"(.*)>(.*)<\/table>/Us', $res, $main);
        
        $return = [];
        if(isset($main[2][0]) && !empty($main[2][0])) {
            $gmaps = $main[2][0];
            preg_match_all('/<h4 class="r"(.*)><a(.*)href="\/url\?q=http:\/\/(.*)\/&amp;.*">(.*)<\/a><\/h4>/Us', $gmaps, $main);
            if(isset($main[3])) {
                $return['sites'] = $main[3];
                $return['titles'] = $main[4];
            }
        }

        if (count($return['sites']) == 0 && strpos($res, '302 Moved') !== false && strpos($res, 'The document has moved') !== false)
            return -2;

        return $return;
    }
    
    public static function getYahooResults($word, $host, $port, $login, $password, $start)
    {
        //global $agents;
        $useragent = self::getRandomUserAgent();
        ini_set('user_agent', $useragent);
        $url = "http://search.yahoo.com/search;_ylt=Apg.fQFBzxhPrDgbbsxmnm2bvZx4?p=" . urlencode($word) . '&b=' . $start;
        //$url = "http://search.yahoo.com/search;_ylt=ApLYy1iuTRLVmjcbCmMs4u2bvZx4?p=filetype%3Apdf&toggle=1&cop=mss&ei=UTF-8&fr=yfp-t-900";
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $url);
        curl_setopt($curl_session, CURLOPT_VERBOSE, 1);
        curl_setopt($curl_session, CURLOPT_USERAGENT, $useragent);
        curl_setopt($curl_session, CURLOPT_PROXYUSERPWD, $login . ':' . $password);
        curl_setopt($curl_session, CURLOPT_PROXY, $host . ':' . $port);
        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_session, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl_session);
        
        if(!self::checkProxyResponseCode($host, curl_getinfo($curl_session), 'yahoo', 3600)) {
            return false;
        }
        
        curl_close($curl_session);

        if (!$res || $res == '' || strlen($res) < 10) {
            echo '-1';
            return -1;
        }

        preg_match_all('/<span>(.*) results<\/span><\/div><\/div>.*<div id="right">/Us', $res, $main);
        if (!isset($main[1][0])) $main[1][0] = 0;
        $return = array();
        $return['pages'] = intval(str_replace(',', '', $main[1][0]));

        $res_or = $res;

        $res = substr($res, strpos($res, '<h2>Search results'));
        $pos = strpos($res, 'id="pg"');
        if ($pos > 0)
            $res = substr($res, 0, $pos);

        preg_match_all('/<li.*><div class="res"><div><h3><a .*href=".*http%3a%2f%2f(.*)"target="_blank".*>(.*)<\/a><\/h3><\/div><span class="url" dir="ltr">.*<div class="abstr">(.*)<\/div><\/div><\/li>/Us', $res, $main);

        for($i=0;$i<count($main[1]);$i++){
            $pos=strpos($main[1][$i],'.pdf');
            if($pos>0)$main[1][$i]=substr($main[1][$i],0,$pos+4);
            $main[1][$i]='http://'.urldecode($main[1][$i]);
            $main[2][$i]=strip_tags($main[2][$i]);
            $main[3][$i]=strip_tags($main[3][$i]);
        }
        echo '-'.count($main[1]).'-';
        $return['links'] = $main[1];
        $return['titles'] = $main[2];
        $return['descriptions'] = $main[3];

        return $return;
    }
    
    public static function getBingResults($word, $host, $port, $login, $password, $start)
    {
        //global $agents;
        //$useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:23.0) Gecko/20100101 Firefox/23.0";
        $useragent = self::getRandomUserAgent();
        ini_set('user_agent', $useragent);
        $url = "http://www.bing.com/search?q=" . urlencode($word) . '&b=' . $start;
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $url);
        curl_setopt($curl_session, CURLOPT_VERBOSE, 1);
        curl_setopt($curl_session, CURLOPT_USERAGENT, $useragent);
        curl_setopt($curl_session, CURLOPT_PROXYUSERPWD, $login.':'.$password);
        curl_setopt($curl_session, CURLOPT_PROXY, $host.':'.$port);
        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_session, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl_session);
        
        if(!self::checkProxyResponseCode($host, curl_getinfo($curl_session), 'bing', 3600)) {
            return false;
        }
        
        curl_close($curl_session);

        if (!$res || $res == '' || strlen($res) < 10) {
            echo '-1';
            return -1;
        }

        preg_match_all('/<span class="sb_count".*>(.*) results<\/span>/Us',$res,$main);
        if(!isset($main[1][0])||empty($main[1][0])){
            preg_match_all('/<span class="sb_count".*>результаты: (.*)<\/span>/Us',$res,$main);

            if(!isset($main[1][0]))
                $main[1][0]=0;
        }

        $return=array();
        $return['pages']=intval(str_replace(array(',', ' ', '&#160;'),'',$main[1][0]));

        $res_or=$res;

        if(strpos($res,'ul id="wg0"') > 0){
            $res=substr($res,strpos($res,'ul id="wg0"'));
        }else{
            $res=substr($res,strpos($res,'<li class="b_algo"'));
        }

        $pos=strpos($res,'Pagination');
        if($pos>0){
            $res=substr($res,0,$pos);

            preg_match_all('/<li.*><div.*<h3><a href="(.*)".*>(.*)<\/a><\/h3><\/div>.*<p>(.*)<\/p><\/div><\/div><\/li>/Us',$res,$main);
            if(count($main[1]) == 0){
                preg_match_all('/<li.*><div.*<h2><a href="(.*)".*>(.*)<\/a><\/h2><\/div>.*<p>(.*)<\/p><\/div><\/li>/Us',$res,$main);
            }
        }else{
            $pos=strpos($res,'Разбиение на страницы');
            if($pos>0){
                $res=substr($res,0,$pos);
            }

            preg_match_all('/<li.*><h2><a href="(.*)".*>(.*)<\/a><\/h2><div.*>.*<\/div><p>(.*)<\/p><\/div><\/li>/Us',$res,$main);
        }

        for($i=0;$i<count($main[1]);$i++){
            $pos=strpos($main[1][$i],'.pdf');
            if($pos>0)$main[1][$i]=substr($main[1][$i],0,$pos+4);
            $main[1][$i]=urldecode($main[1][$i]);
            $main[2][$i]=strip_tags($main[2][$i]);
            $main[3][$i]=strip_tags($main[3][$i]);
        }

        $return['links'] = $main[1];
        $return['titles'] = $main[2];
        $return['descriptions'] = $main[3];

        return $return;
    }

    public static function getYandexResults($word, $host, $port, $login, $password, $start)
    {
        //$useragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:23.0) Gecko/20100101 Firefox/23.0";
        $useragent = self::getRandomUserAgent();
        ini_set('user_agent', $useragent);

    	$query_txt = $word[1].'&'.$word[2].'&'.$word[3].'&p='.$start;
    	if($start != 0) $word[] = 'p='.$start;
        shuffle($word);

        $url = "http://yandex.com/yandsearch?".implode("&", $word);

        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_URL, $url);
        curl_setopt($curl_session, CURLOPT_VERBOSE, 1);
        curl_setopt($curl_session, CURLOPT_USERAGENT, $useragent);
        curl_setopt($curl_session, CURLOPT_PROXYUSERPWD, $login.':'.$password);
        curl_setopt($curl_session, CURLOPT_PROXY, $host.':'.$port);
        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_session, CURLOPT_COOKIEFILE, "ya_cookies/{$host}-{$port}.txt");
		curl_setopt($curl_session, CURLOPT_COOKIEJAR, "ya_cookies/{$host}-{$port}.txt");
        curl_setopt($curl_session, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl_session);
        
        if(!self::checkProxyResponseCode($host, curl_getinfo($curl_session), 'yandex', 3600)) {
            return false;
        }
        
        curl_close($curl_session);

        $fp_page = fopen("data/yandex_".$query_txt.".html", 'a');
        fputs($fp_page, "{$login} / {$password} / {$host} / {$port} / {$url}<br>".$res);

        sleep(rand(240, 420));

        if (!$res || $res == '' || strlen($res) < 10) {
            echo '-1';
            return -1;
        }

        $return['pages']=100;

        $res_or=$res;

        $res=substr($res,strpos($res,'ul id="wg0"'));
        $pos=strpos($res,'Pagination');
        if($pos>0)
            $res=substr($res,0,$pos);
        
        preg_match_all('/<div.*><h2.*><a.*href="(.*)"><i.*><\/i><\/a><a.*>(.*)<\/a><\/h2><div.*><div.*><a.*><div.*><img.*\/><span.*>.*<\/span><\/div><\/a><\/div><div.*><div.*><span.*><a.*>.*<\/a><span.*>.*<\/span><a.*>.*<\/a><\/span><span.*><\/span><\/div><div.*>(.*)<\/div><div.*><a.*><span.*>.*<\/span><\/a><a.*><span.*>.*<\/span><\/a><\/div><\/div><\/div><\/div><\/div>/Us',$res,$main);

        for($i=0;$i<count($main[1]);$i++){
            $pos=strpos($main[1][$i],'.pdf');
            if($pos>0)$main[1][$i]=substr($main[1][$i],0,$pos+4);
            $main[1][$i]=urldecode($main[1][$i]);
            $main[2][$i]=strip_tags($main[2][$i]);
            $main[3][$i]=strip_tags($main[3][$i]);
        }
        
        $return['links'] = $main[1];
        $return['titles'] = $main[2];
        $return['descriptions'] = $main[3];

        return $return;
    }
    
    public static function proxyResults($word, $start, $yahoo =0)
    {
        global $proxy, $proxy_index, $proxy_table;
        
        $search = true;
        $p = 0;
        $step = 0;
            
        while ($search) {
            while($proxy_index < count($proxy) && !isset($proxy[$proxy_index])) {
                $proxy_index++;
            }
            
            if (!isset($proxy[$proxy_index])) {
                $proxy_index = 0;
                return false;
            }
            
            if ($proxy[$proxy_index]['active'] == 0) {
                $proxy_index++;
                if ($proxy_index >= count($proxy)) {
                    $proxy_index = 0;
                    $p++;
                    if ($p > 1) {
                        return false;
                    }
                }
            }
            if ($yahoo==3){
                $title = self::getYandexResults($word, $proxy[$proxy_index]['host'], $proxy[$proxy_index]['port'], $proxy[$proxy_index]['login'], $proxy[$proxy_index]['password'], $start);
            }elseif ($yahoo==2)
                $title = self::getBingResults($word, $proxy[$proxy_index]['host'], $proxy[$proxy_index]['port'], $proxy[$proxy_index]['login'], $proxy[$proxy_index]['password'], $start);
            elseif ($yahoo==1)
                $title = self::getYahooResults($word, $proxy[$proxy_index]['host'], $proxy[$proxy_index]['port'], $proxy[$proxy_index]['login'], $proxy[$proxy_index]['password'], $start);
            else
                $title = self::getGoogleResults($word, $proxy[$proxy_index]['host'], $proxy[$proxy_index]['port'], $proxy[$proxy_index]['login'], $proxy[$proxy_index]['password'], $start);
            if ($title == -1 || $title == -2) {
                if ($title == -2) {
                    $connection = \Yii::$app->dbPdfDb;
                    $connection->open();
                    if ($yahoo==3)
                        $connection->createCommand("UPDATE $proxy_table SET yandex_failure=yandex_failure+1 WHERE id=" . $proxy[$proxy_index]['id'])->execute();
                    elseif ($yahoo==2)
                        $connection->createCommand("UPDATE $proxy_table SET bing_failure=bing_failure+1 WHERE id=" . $proxy[$proxy_index]['id'])->execute();
                    elseif ($yahoo==1)
                        $connection->createCommand("UPDATE $proxy_table SET yahoo_failure=yahoo_failure+1 WHERE id=" . $proxy[$proxy_index]['id'])->execute();
                    else
                        $connection->createCommand("UPDATE $proxy_table SET failure=failure+1 WHERE id=" . $proxy[$proxy_index]['id'])->execute();
                }
                $proxy[$proxy_index]['active'] = 0;
                $proxy_index++;
                if ($proxy_index >= count($proxy)) {
                    $proxy_index = 0;
                    $p++;
                    if ($p > 1) {
                        return false;
                    }
                }
            } else {
                $search = false;
                $proxy_index++;
                if ($proxy_index >= count($proxy)) {
                    $proxy_index = 0;
                    if($yahoo == 0){
                    	//sleep(60 - count($proxy) + 20);
                        sleep(30);
                    }elseif($yahoo == 3){
                    	//sleep in function getYandexResults
                    }else{
                    	sleep(120);
                    }
                }
                return $title;
            }
        }
    }
    
    public static function proxyResultsGMap($query)
    {
        global $proxy, $proxy_index, $proxy_table;
        
        $search = true;
        $p = 0;
        $step = 0;
            
        while ($search) {
            while($proxy_index < count($proxy) && !isset($proxy[$proxy_index])) {
                $proxy_index++;
            }
            
            if (!isset($proxy[$proxy_index])) {
                $proxy_index = 0;
                return false;
            }
            
            if ($proxy[$proxy_index]['active'] == 0) {
                $proxy_index++;
                if ($proxy_index >= count($proxy)) {
                    $proxy_index = 0;
                    $p++;
                    if ($p > 1) {
                        return false;
                    }
                }
            }
            
            $title = self::getGoogleResults($query, $proxy[$proxy_index]['host'], $proxy[$proxy_index]['port'], $proxy[$proxy_index]['login'], $proxy[$proxy_index]['password'], 0, '', true);
            return $title;
            if ($title == -1 || $title == -2) {
                if ($title == -2) {
                    $connection = \Yii::$app->dbPdfDb;
                    $connection->open();
                    $connection->createCommand("UPDATE $proxy_table SET failure=failure+1 WHERE id=" . $proxy[$proxy_index]['id'])->execute();
                }
                $proxy[$proxy_index]['active'] = 0;
                $proxy_index++;
                if ($proxy_index >= count($proxy)) {
                    $proxy_index = 0;
                    $p++;
                    if ($p > 1) {
                        return false;
                    }
                }
            } else {
                $search = false;
                $proxy_index++;
                if ($proxy_index >= count($proxy)) {
                    $proxy_index = 0;
                    if($yahoo == 0){
                    	//sleep(60 - count($proxy) + 20);
                        sleep(30);
                    }elseif($yahoo == 3){
                    	//sleep in function getYandexResults
                    }else{
                    	sleep(120);
                    }
                }
                return $title;
            }
        }
    }
    
    /**
     *
     * @param string $url
     * @param array $proxy
     * @param string $agent
     * @return string 
     */
    public static function getHTML(
            $url, 
            $proxy = array(),
            $getInfo = false,
            $autoRedirect = false
            ){
        $curl_session = curl_init();
        curl_setopt($curl_session, CURLOPT_USERAGENT, static::getRandomUserAgent());
        curl_setopt($curl_session, CURLOPT_URL, $url);
        curl_setopt($curl_session, CURLOPT_VERBOSE, 1);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl_session, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);
        if ($proxy){
            curl_setopt($curl_session, CURLOPT_PROXYUSERPWD, $proxy['login'] . ':' . $proxy['password']);
            curl_setopt($curl_session, CURLOPT_PROXY, $proxy['host'] . ':' . $proxy['port']);
        }
        
        if ($autoRedirect){
            curl_setopt($curl_session, CURLOPT_FOLLOWLOCATION, true);
        }

        $res = curl_exec($curl_session);
        if($getInfo){
            $info = curl_getinfo($curl_session); 
            $infoRes = $res;
            $res = ['page' => $infoRes, 'info' => $info];
        }
        curl_close($curl_session);

        return $res;
    }
    
    public static function getRandomUserAgent() {
        return "Mozilla/5.0 (Windows; U; Windows NT 5.1; de-DE; rv:1.7.6) Gecko/20050226 Firefox/1.0.1";
        /*
        $browser_strings = array (
                "Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0",
                "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)",
                "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)",
                "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)",
                "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/4.0; InfoPath.2; SV1; .NET CLR 2.0.50727; WOW64)",
                "Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)",
                "Mozilla/4.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)",
                "Mozilla/1.22 (compatible; MSIE 10.0; Windows 3.1)",
                "Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; en-US))",
                "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 7.1; Trident/5.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; Media Center PC 6.0; InfoPath.3; MS-RTC LM 8; Zune 4.7)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; Media Center PC 6.0; InfoPath.3; MS-RTC LM 8; Zune 4.7",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; InfoPath.3; MS-RTC LM 8; .NET4.0C; .NET4.0E)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; chromeframe/12.0.742.112)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; Tablet PC 2.0; InfoPath.3; .NET4.0C; .NET4.0E)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; yie8)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET CLR 1.1.4322; .NET4.0C; Tablet PC 2.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; FunWebProducts)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; chromeframe/13.0.782.215)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; chromeframe/11.0.696.57)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0) chromeframe/10.0.648.205",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/4.0; GTB7.4; InfoPath.1; SV1; .NET CLR 2.8.52393; WOW64; en-US)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0; chromeframe/11.0.696.57)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/4.0; GTB7.4; InfoPath.3; SV1; .NET CLR 3.1.76908; WOW64; en-US)",
                "Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; en-US))",
                "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 7.1; Trident/5.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; Media Center PC 6.0; InfoPath.3; MS-RTC LM 8; Zune 4.7)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; Media Center PC 6.0; InfoPath.3; MS-RTC LM 8; Zune 4.7",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; InfoPath.3; MS-RTC LM 8; .NET4.0C; .NET4.0E)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; chromeframe/12.0.742.112)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; Tablet PC 2.0; InfoPath.3; .NET4.0C; .NET4.0E)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; yie8)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET CLR 1.1.4322; .NET4.0C; Tablet PC 2.0)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; FunWebProducts)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; chromeframe/13.0.782.215)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; chromeframe/11.0.696.57)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0) chromeframe/10.0.648.205",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/4.0; GTB7.4; InfoPath.1; SV1; .NET CLR 2.8.52393; WOW64; en-US)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0; chromeframe/11.0.696.57)",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/4.0; GTB7.4; InfoPath.3; SV1; .NET CLR 3.1.76908; WOW64; en-US)",
                "Mozilla/5.0 ( ; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)",
                "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/4.0; GTB7.4; InfoPath.2; SV1; .NET CLR 4.4.58799; WOW64; en-US)",
                "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/4.0; FDM; MSIECrawler; Media Center PC 5.0)",
                "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/4.0; GTB7.4; InfoPath.3; SV1; .NET CLR 3.4.53360; WOW64; en-US)",
                "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; Trident/5.0)",
                "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; .NET4.0C; .NE",
                "Mozilla/4.0 (compatible; MSIE 9.0; Windows 98; .NET CLR 3.0.04506.30)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 7.1; Trident/5.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; AskTB5.5)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; InfoPath.2; .NET4.0C; .NET4.0E)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET4.0C; .NET4.0E; InfoPath.3)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET4.0C)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; FDM; .NET CLR 1.1.4322; .NET4.0C; .NET4.0E; Tablet PC 2.0)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; Tablet PC 2.0; InfoPath.3; .NET4.0E)",
                "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/5.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; FDM; .NET4.0C; .NET4.0E; chromeframe/11.0.696.57)",
                "Mozilla/4.0 (compatible; U; MSIE 9.0; WIndows NT 9.0; en-US)",
                "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; FunWebProducts)",
                "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:25.0) Gecko/20100101 Firefox/25.0",
                "Mozilla/5.0 (Windows NT 6.0; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:24.0) Gecko/20100101 Firefox/24.0",
                "Mozilla/5.0 (Windows NT 6.2; rv:22.0) Gecko/20130405 Firefox/23.0",
                "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20130406 Firefox/23.0",
                "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:23.0) Gecko/20131011 Firefox/23.0",
                "Mozilla/5.0 (Windows NT 6.2; rv:22.0) Gecko/20130405 Firefox/22.0",
                "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:22.0) Gecko/20130328 Firefox/22.0",
                "Mozilla/5.0 (Windows NT 6.1; rv:22.0) Gecko/20130405 Firefox/22.0",
                "Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:16.0.1) Gecko/20121011 Firefox/21.0.1",
                "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:16.0.1) Gecko/20121011 Firefox/21.0.1",
                "Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:21.0.0) Gecko/20121011 Firefox/21.0.0",
                "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20130331 Firefox/21.0",
                "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0",
                "Mozilla/5.0 (X11; Linux i686; rv:21.0) Gecko/20100101 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.2; rv:21.0) Gecko/20130326 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20130401 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20130331 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20130330 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20130401 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20130328 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 5.1; rv:21.0) Gecko/20130401 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 5.1; rv:21.0) Gecko/20130331 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 5.1; rv:21.0) Gecko/20100101 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 5.0; rv:21.0) Gecko/20100101 Firefox/21.0",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0",
                "Mozilla/5.0 (Windows NT 6.2; Win64; x64;) Gecko/20100101 Firefox/20.0",
                "Mozilla/5.0 (Windows NT 6.1; rv:6.0) Gecko/20100101 Firefox/19.0",
                "Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/18.0.1",
                "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0",
                "Mozilla/5.0 (X11; Ubuntu; Linux armv7l; rv:17.0) Gecko/20100101 Firefox/17.0",
                "Mozilla/6.0 (Windows NT 6.2; WOW64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1",
                "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1",
                "Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1",
                "Mozilla/5.0 (Windows NT 6.1; rv:15.0) Gecko/20120716 Firefox/15.0a2",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.16) Gecko/20120427 Firefox/15.0a1",
                "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20120427 Firefox/15.0a1",
                "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:15.0) Gecko/20120910144328 Firefox/15.0.2",
                "Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:15.0) Gecko/20100101 Firefox/15.0.1",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:15.0) Gecko/20121011 Firefox/15.0.1",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.62 Safari/537.36",
                "Mozilla/5.0 (X11; CrOS i686 4319.74.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.57 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.2 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1468.0 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1467.0 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1464.0 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36",
                "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36",
                "Mozilla/5.0 (X11; CrOS i686 3912.101.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.60 Safari/537.17",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1309.0 Safari/537.17",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.15 (KHTML, like Gecko) Chrome/24.0.1295.0 Safari/537.15",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.14 (KHTML, like Gecko) Chrome/24.0.1292.0 Safari/537.14",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13",
                "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1284.0 Safari/537.13",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.6 Safari/537.11",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.26 Safari/537.11",
                "Mozilla/5.0 (Windows NT 6.0) yi; AppleWebKit/345667.12221 (KHTML, like Gecko) Chrome/23.0.1271.26 Safari/453667.1221",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.17 Safari/537.11",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94 Safari/537.4",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_0) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4",
                "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/22.0.1207.1 Safari/537.1",
                "Mozilla/5.0 (X11; CrOS i686 2268.111.0) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1092.0 Safari/536.6",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/19.77.34.5 Safari/537.1",
                "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.9 Safari/536.5",
                "Mozilla/5.0 (Windows NT 6.0) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.36 Safari/536.5",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1063.0 Safari/536.3",
                "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1063.0 Safari/536.3",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_0) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1063.0 Safari/536.3",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1062.0 Safari/536.3",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1062.0 Safari/536.3",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.1 Safari/536.3",
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.1 Safari/536.3",
                "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.1 Safari/536.3",
                "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.0 Safari/536.3",
                "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.24 (KHTML, like Gecko) Chrome/19.0.1055.1 Safari/535.24",
                "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/535.24 (KHTML, like Gecko) Chrome/19.0.1055.1 Safari/535.24",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/535.24 (KHTML, like Gecko) Chrome/19.0.1055.1 Safari/535.24",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.22 (KHTML, like Gecko) Chrome/19.0.1047.0 Safari/535.22",
                "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.21 (KHTML, like Gecko) Chrome/19.0.1042.0 Safari/535.21",
                "Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.21 (KHTML, like Gecko) Chrome/19.0.1041.0 Safari/535.21",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.20 (KHTML, like Gecko) Chrome/19.0.1036.7 Safari/535.20",
                "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/18.6.872.0 Safari/535.2 UNTRUSTED/1.0 3gpp-gba UNTRUSTED/1.0",
                "Mozilla/5.0 (Macintosh; AMD Mac OS X 10_8_2) AppleWebKit/535.22 (KHTML, like Gecko) Chrome/18.6.872",
                "Mozilla/5.0 (X11; CrOS i686 1660.57.0) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.46 Safari/535.19",
                "Mozilla/5.0 (Windows NT 6.0; WOW64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.45 Safari/535.19",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.45 Safari/535.19",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.45 Safari/535.19",
                "Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2",
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10",
                "Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko ) Version/5.1 Mobile/9B176 Safari/7534.48.3",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; de-at) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; tr-TR) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; ko-KR) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; fr-FR) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; cs-CZ) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; ja-JP) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_8; zh-cn) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_8; ja-jp) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; ja-jp) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; zh-cn) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; sv-se) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; ko-kr) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; ja-jp) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; it-it) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; fr-fr) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; es-es) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-gb) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; de-de) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; sv-SE) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; ja-JP) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; de-DE) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; hu-HU) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; de-DE) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; ja-JP) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; it-IT) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-us) AppleWebKit/534.16+ (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; fr-ch) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; de-de) AppleWebKit/534.15+ (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; ar) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Android 2.2; Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-HK) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; tr-TR) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; nb-NO) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; fr-FR) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-TW) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; zh-cn) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5",
                "Mozilla/5.0 (iPod; U; CPU iPhone OS 4_3_3 like Mac OS X; ja-jp) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
                "Mozilla/5.0 (iPod; U; CPU iPhone OS 4_3_1 like Mac OS X; zh-cn) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8G4 Safari/6533.18.5",
                "Mozilla/5.0 (iPod; U; CPU iPhone OS 4_2_1 like Mac OS X; he-il) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; ru; CPU iPhone OS 4_2_1 like Mac OS X; ru) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; ru; CPU iPhone OS 4_2_1 like Mac OS X; fr) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; fr; CPU iPhone OS 4_2_1 like Mac OS X; fr) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_1 like Mac OS X; zh-tw) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8G4 Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3 like Mac OS X; pl-pl) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8F190 Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3 like Mac OS X; fr-fr) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8F190 Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3 like Mac OS X; en-gb) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8F190 Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; ru-ru) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5",
                "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_2_1 like Mac OS X; nb-no) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148a Safari/6533.18.5",
                "Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/533.17.8 (KHTML, like Gecko) Version/5.0.1 Safari/533.17.8",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_4; th-th) AppleWebKit/533.17.8 (KHTML, like Gecko) Version/5.0.1 Safari/533.17.8",
                "Mozilla/5.0 (X11; U; Linux x86_64; en-us) AppleWebKit/531.2+ (KHTML, like Gecko) Version/5.0 Safari/531.2+",
                "Mozilla/5.0 (X11; U; Linux x86_64; en-ca) AppleWebKit/531.2+ (KHTML, like Gecko) Version/5.0 Safari/531.2+",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; ja-JP) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; es-ES) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Windows; U; Windows NT 6.0; ja-JP) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_8; ja-jp) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_4_11; fr) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; zh-cn) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ru-ru) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ko-kr) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; it-it) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; HTC-P715a; en-ca) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; en-us) AppleWebKit/534.1+ (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; en-au) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; el-gr) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; ca-es) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; zh-tw) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; ja-jp) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; it-it) AppleWebKit/533.16 (KHTML, like Gecko) Version/5.0 Safari/533.16",
                "Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14",
                "Mozilla/5.0 (Windows NT 6.0; rv:2.0) Gecko/20100101 Firefox/4.0 Opera 12.14",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0) Opera 12.14",
                "Opera/12.80 (Windows NT 5.1; U; en) Presto/2.10.289 Version/12.02",
                "Opera/9.80 (Windows NT 6.1; U; es-ES) Presto/2.9.181 Version/12.00",
                "Opera/9.80 (Windows NT 5.1; U; zh-sg) Presto/2.9.181 Version/12.00",
                "Opera/12.0(Windows NT 5.2;U;en)Presto/22.9.168 Version/12.00",
                "Opera/12.0(Windows NT 5.1;U;en)Presto/22.9.168 Version/12.00",
                "Mozilla/5.0 (Windows NT 5.1) Gecko/20100101 Firefox/14.0 Opera/12.0",
                "Opera/9.80 (Windows NT 6.1; WOW64; U; pt) Presto/2.10.229 Version/11.62",
                "Opera/9.80 (Windows NT 6.0; U; pl) Presto/2.10.229 Version/11.62",
                "Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; fr) Presto/2.9.168 Version/11.52",
                "Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; de) Presto/2.9.168 Version/11.52",
                "Opera/9.80 (Windows NT 5.1; U; en) Presto/2.9.168 Version/11.51",
                "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; de) Opera 11.51",
                "Opera/9.80 (X11; Linux x86_64; U; fr) Presto/2.9.168 Version/11.50",
                "Opera/9.80 (X11; Linux i686; U; hu) Presto/2.9.168 Version/11.50",
                "Opera/9.80 (X11; Linux i686; U; ru) Presto/2.8.131 Version/11.11",
                "Opera/9.80 (X11; Linux i686; U; es-ES) Presto/2.8.131 Version/11.11",
                "Mozilla/5.0 (Windows NT 5.1; U; en; rv:1.8.1) Gecko/20061208 Firefox/5.0 Opera 11.11",
                "Opera/9.80 (X11; Linux x86_64; U; bg) Presto/2.8.131 Version/11.10",
                "Opera/9.80 (Windows NT 6.0; U; en) Presto/2.8.99 Version/11.10",
                "Opera/9.80 (Windows NT 5.1; U; zh-tw) Presto/2.8.131 Version/11.10",
                "Opera/9.80 (Windows NT 6.1; Opera Tablet/15165; U; en) Presto/2.8.149 Version/11.1",
                "Opera/9.80 (X11; Linux x86_64; U; Ubuntu/10.10 (maverick); pl) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (X11; Linux i686; U; ja) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (X11; Linux i686; U; fr) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 6.1; U; zh-tw) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 6.1; U; zh-cn) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 6.1; U; sv) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 6.1; U; en-US) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 6.1; U; cs) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 6.0; U; pl) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 5.2; U; ru) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 5.1; U;) Presto/2.7.62 Version/11.01",
                "Opera/9.80 (Windows NT 5.1; U; cs) Presto/2.7.62 Version/11.01",
                "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101213 Opera/9.80 (Windows NT 6.1; U; zh-tw) Presto/2.7.62 Version/11.01",
                "Mozilla/5.0 (Windows NT 6.1; U; nl; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 11.01",
                "Mozilla/5.0 (Windows NT 6.1; U; de; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 11.01",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; de) Opera 11.01",
                "Opera/9.80 (X11; Linux x86_64; U; pl) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (X11; Linux i686; U; it) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (Windows NT 6.1; U; zh-cn) Presto/2.6.37 Version/11.00",
                "Opera/9.80 (Windows NT 6.1; U; pl) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (Windows NT 6.1; U; ko) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (Windows NT 6.1; U; fi) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (Windows NT 6.1; U; en-GB) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (Windows NT 6.1 x64; U; en) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (Windows NT 6.0; U; en) Presto/2.7.39 Version/11.00",
                "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.7.39 Version/11.00",
                "Opera/9.80 (Windows NT 5.1; U; MRA 5.5 (build 02842); ru) Presto/2.7.62 Version/11.00",
                "Opera/9.80 (Windows NT 5.1; U; it) Presto/2.7.62 Version/11.00",
                "Mozilla/5.0 (Windows NT 6.0; U; ja; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 11.00",
                "Mozilla/5.0 (Windows NT 5.1; U; pl; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 11.00",
                "Mozilla/5.0 (Windows NT 5.1; U; de; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 11.00",
                "Mozilla/4.0 (compatible; MSIE 8.0; X11; Linux x86_64; pl) Opera 11.00",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; fr) Opera 11.00",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; ja) Opera 11.00",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; en) Opera 11.00",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; pl) Opera 11.00",
                "Opera/9.80 (Windows NT 6.1; U; pl) Presto/2.6.31 Version/10.70",
                "Mozilla/5.0 (Windows NT 5.2; U; ru; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.70",
                "Mozilla/5.0 (Windows NT 5.1; U; zh-cn; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.70",
                "Opera/9.80 (Windows NT 5.2; U; zh-cn) Presto/2.6.30 Version/10.63",
                "Opera/9.80 (Windows NT 5.2; U; en) Presto/2.6.30 Version/10.63",
                "Opera/9.80 (Windows NT 5.1; U; MRA 5.6 (build 03278); ru) Presto/2.6.30 Version/10.63",
                "Opera/9.80 (Windows NT 5.1; U; pl) Presto/2.6.30 Version/10.62",
                "Mozilla/5.0 (X11; Linux x86_64; U; de; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.62",
                "Mozilla/4.0 (compatible; MSIE 8.0; X11; Linux x86_64; de) Opera 10.62",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; en) Opera 10.62",
                "Opera/9.80 (X11; Linux i686; U; pl) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (X11; Linux i686; U; es-ES) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (Windows NT 6.1; U; zh-cn) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (Windows NT 6.1; U; en) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (Windows NT 6.0; U; it) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (Windows NT 5.2; U; ru) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (Windows 98; U; de) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (Macintosh; Intel Mac OS X; U; nl) Presto/2.6.30 Version/10.61",
                "Opera/9.80 (X11; Linux i686; U; en) Presto/2.5.27 Version/10.60",
                "Opera/9.80 (Windows NT 6.0; U; nl) Presto/2.6.30 Version/10.60",
                "Opera/10.60 (Windows NT 5.1; U; zh-cn) Presto/2.6.30 Version/10.60",
                "Opera/10.60 (Windows NT 5.1; U; en-US) Presto/2.6.30 Version/10.60",
                "Opera/9.80 (X11; Linux i686; U; it) Presto/2.5.24 Version/10.54",
                "Opera/9.80 (X11; Linux i686; U; en-GB) Presto/2.5.24 Version/10.53",
                "Mozilla/5.0 (Windows NT 5.1; U; zh-cn; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.53",
                "Mozilla/5.0 (Windows NT 5.1; U; Firefox/5.0; en; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.53",
                "Mozilla/5.0 (Windows NT 5.1; U; Firefox/4.5; en; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.53",
                "Mozilla/5.0 (Windows NT 5.1; U; Firefox/3.5; en; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.53",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; ko) Opera 10.53",
                "Opera/9.80 (Windows NT 6.1; U; fr) Presto/2.5.24 Version/10.52",
                "Opera/9.80 (Windows NT 6.1; U; en) Presto/2.5.22 Version/10.51",
                "Opera/9.80 (Windows NT 6.0; U; cs) Presto/2.5.22 Version/10.51",
                "Opera/9.80 (Windows NT 5.2; U; ru) Presto/2.5.22 Version/10.51",
                "Opera/9.80 (Linux i686; U; en) Presto/2.5.22 Version/10.51",
                "Mozilla/5.0 (Windows NT 6.1; U; en-GB; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.51",
                "Mozilla/5.0 (Linux i686; U; en; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 Opera 10.51",
                "Mozilla/4.0 (compatible; MSIE 8.0; Linux i686; en) Opera 10.51",
                "Opera/9.80 (Windows NT 6.1; U; zh-tw) Presto/2.5.22 Version/10.50",
                "Opera/9.80 (Windows NT 6.1; U; zh-cn) Presto/2.5.22 Version/10.50",
                "Opera/9.80 (Windows NT 6.1; U; sk) Presto/2.6.22 Version/10.50",
                "Opera/9.80 (Windows NT 6.1; U; ja) Presto/2.5.22 Version/10.50",
                "Opera/9.80 (Windows NT 6.0; U; zh-cn) Presto/2.5.22 Version/10.50",
                "Opera/9.80 (Windows NT 5.1; U; sk) Presto/2.5.22 Version/10.50",
                "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.5.22 Version/10.50",
                "Opera/10.50 (Windows NT 6.1; U; en-GB) Presto/2.2.2",
                "Opera/9.80 (S60; SymbOS; Opera Tablet/9174; U; en) Presto/2.7.81 Version/10.5",
                "Opera/9.80 (X11; U; Linux i686; en-US; rv:1.9.2.3) Presto/2.2.15 Version/10.10",
                "Opera/9.80 (X11; Linux x86_64; U; it) Presto/2.2.15 Version/10.10",
                "Opera/9.80 (Windows NT 6.1; U; de) Presto/2.2.15 Version/10.10",
                "Opera/9.80 (Windows NT 6.0; U; Gecko/20100115; pl) Presto/2.2.15 Version/10.10",
                "Opera/9.80 (Windows NT 6.0; U; en) Presto/2.2.15 Version/10.10",
                "Opera/9.80 (Windows NT 5.1; U; de) Presto/2.2.15 Version/10.10",
                "Opera/9.80 (Windows NT 5.1; U; cs) Presto/2.2.15 Version/10.10",
                "Mozilla/5.0 (Windows NT 6.0; U; tr; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 10.10",
                "Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686; de) Opera 10.10",
                "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.0; tr) Opera 10.10",
                "Opera/9.80 (X11; Linux x86_64; U; en-GB) Presto/2.2.15 Version/10.01",
                "Opera/9.80 (X11; Linux x86_64; U; en) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux x86_64; U; de) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; ru) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; pt-BR) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; pl) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; nb) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; en-GB) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; en) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; Debian; pl) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (X11; Linux i686; U; de) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 6.1; U; zh-cn) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 6.1; U; fi) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 6.1; U; en) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 6.1; U; de) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 6.1; U; cs) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 6.0; U; en) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 6.0; U; de) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 5.2; U; en) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 5.1; U; zh-cn) Presto/2.2.15 Version/10.00",
                "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.2.15 Version/10.00"
        );

        $rand_index = array_rand($browser_strings);
        return $browser_strings[$rand_index];
         */
    }
    
    /**
     * Check end wright to log proxy reply
     * 
     * @param array $proxy
     * @param array $curl_info - information about curl reply (included type: 302, 500 etc.)
     * @param int $timeout - pause in seconds (default 1 hour)
     * @return boolean
     */
    public static function checkProxyResponseCode($proxy_ip, $curl_info, $search_engine = 'google', $timeout = 3600) {
        global $proxy;
        $proxy_ip = trim($proxy_ip);
        if($curl_info['http_code'] != 200 && $proxy_ip != '') {
            $model = new ProxyLog();
            $model->ip = $proxy_ip;
            $model->search_engine = $search_engine;
            $model->code = $curl_info['http_code'];
            $model->dt = date('Y-m-d H:i:s', time());
            $model->dt_unblock = date('Y-m-d H:i:s', (time() + $timeout));
            $model->save();
                        
            foreach ($proxy as $key => $val) {
                if($val['host'] == $proxy_ip && $curl_info['http_code'] != 0) {
                    unset($proxy[$key]);
                }
            }
                        
            sleep(1);
            return false;
        }

        return true;
    }
}

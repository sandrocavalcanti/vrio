<?php
class RssController extends BaseController
{
    public function indexAction() {
    	$m = new MNoticia();
        $feedData = array(
            'title'=>'Instituto Salesiano de Fisolofia',
            'description'=>'Instituto Salesiano de Fisolofia',
            'link'=>'http://www.insaf.com.br',
            'charset'=>'utf8',
            'entries'=>$m->montarRSS()
        );
        $feed = Zend_Feed::importArray ( $feedData, 'rss' );
 
        header ( 'Content-type: text/xml' );
 
        echo $feed->send();
        exit;
    }
}

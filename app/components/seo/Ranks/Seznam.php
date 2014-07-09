<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Seznam
 *
 * @author wossa
 */
namespace Components\Seo\Ranks;

class Seznam extends \Nette\Object{

    private $populatitySite;
    private $indexPages;
    private $htmlSource;
    private $url;
    private $sRank;

    function __construct($url) {
        $this->url = $url;
    }


    public function getPopulatitySite() {
        return $this->populatitySite;
    }

    public function getIndexPages() {
        return $this->indexPages;
    }

    public function setParamIndex() {
        $url = $this->url;
        $urlElement = str_replace("http://", "", $url);
        $urlElement = str_replace("www.", "", $urlElement);
        $urlElement = "http://search.seznam.cz/?q=site:" . $urlElement;
        return \file_get_html($urlElement);
    }

    public function setParamUrlInPages() {
        $url = $this->url;
        $urlElement = str_replace("http://", "", $url);
        $urlElement = str_replace("www.", "", $urlElement);
        $urlElement = "http://search.seznam.cz/?sourceid=szn-HP&thru=&q=%22".$urlElement."%22+-site:" . $urlElement;
        return \file_get_html($urlElement);
    }


    private static function findLinks(\simple_html_dom $dom) {
//        $backlinks = $dom->find("span[class=zobrazuji]", 0);
//        if ($backlinks !== NULL) {
//            $backlinks = $backlinks->plaintext;
//            $backlinks = str_replace("Zobrazujeme 1 - 10 z", "", $backlinks);
//            $backlinks = str_replace("nalezených", "", $backlinks);
//            $backlinks = trim($backlinks);
//
//            return $backlinks;
//        }
        $backlinks = $dom->find("p[id=resultCount]", 0);
        if($backlinks !== null){
            $backlinks = \str_get_html($backlinks->innertext);
            $backlinks = $backlinks->find("strong", 2);

            $backlinks = $backlinks->plaintext;
            $backlinks = \str_replace("&nbsp;", "", $backlinks);
            return \trim($backlinks);
        }

        return 0;
    }

    public function checkPopularitySite(){
        $this->populatitySite = $this->findLinks($this->setParamUrlInPages());
    }

    public function checkIndexPages(){
        $this->indexPages = $this->findLinks($this->setParamIndex());
    }


    public function getSRank() {
        return $this->sRank;
    }

    
    public function checkSRank(){
        $srank = -1;
        $page = $this->url;
        if(substr($page, 0, 4) !== "http" ){
            $page = "http://".$page;
        }
        $params = array("0", htmlspecialchars($page), 0);
        $request = xmlrpc_encode_request("getRank", $params);
        $context = stream_context_create(array('http' => array('method'=>"POST",'header'=>"Content-Type: text/xml",'content'=>$request)));
        $file = file_get_contents('http://srank.seznam.cz', false, $context);
        $response = xmlrpc_decode($file);
        if ($response['status'] == 200) {
            $this->sRank = (round($response['rank'] / 2.55,0));
        }

        if($this->sRank === null){
            $this->sRank = $srank;
        }
    }

}
?>

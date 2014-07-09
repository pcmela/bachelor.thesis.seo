<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FindKeywordsModel
 *
 * @author wossa
 */
namespace Components\Seo\SearchEngine;

use Nette\Object, Nette\Debug, AdminModule\models\database\Slovo,
    \AdminModule\models\database\Web,
    \AdminModule\models\database\Test;

class FindKeywordsSeznam extends FindKeywordsBase implements FindKeywordsInterface{

    private $urlEngine;
    private $keywordPosition;
    const HLOUBKA = 10;
    const ENGINE_ID = 1;

    public function __construct($keywords,$url = null){
        parent::__construct($url, $this->validateKeywords($keywords));
        

        $this->urlEngine = "http://search.seznam.cz/?q=/*keyvord*/&count=20&from=";
    }


    /**
     *
     * @param type $keyword
     * @param type $id
     * @return type 
     */
    public function getPositionKeywords($id = null, $store = true, $results = null) {
        $data = array();
        $countData = 0;
        foreach ($this->keywords as $value){
            //\dibi::begin();
            //\dump($results);
            $count = 0;
            if($store){
                $validate = $this->checkKeywords($id, $value, self::ENGINE_ID);
            
                if($validate["already"] === true && $validate["update"] === false){
                    continue;
                }
            }

            $this->keywordPosition = array();

            if($results === null){
                $content = $this->getYqlContent($this->urlEngine, $value);
            }else{
                //\dump($results[$countData]['data']);
                $content = $results[$countData]['data'];
            }
            
            foreach ($content as $html) {
                $match = $this->findListOfUrl($html);
                $j = 1;
                foreach ($match[0] as $element) {
                    $element = \strip_tags($element);
                    $element = \str_replace("\n", "", $element);
                    $element = \str_replace(" ", "", $element);
                    $element = \str_replace("www.", "", $element);
                    $element = \trim($element);
                    $domain = $this->getDomainName($element);
                    //$domain = $element;
                    
                    $count++;
                    if($store){
                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain);
                    }else{

                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain, "word" => $value, "engine" => "seznam");
                    }
                }
            }

            if($store){
                $this->insertToDatabase($value, $this->keywordPosition, 10, $validate, $id, self::ENGINE_ID);
            }else{
                $data[$value] = $this->keywordPosition;
            }
            $this->keywordPosition = null;
            $countData++;
            //\dibi::commit();
        }
        if(!$store){
            return $data;
        }
        return false;
    }
    
    /**
     * Find Url's from html source
     * @param type html source $source
     * @return array of url's
     */
    protected function findListOfUrl($source){
        
        preg_match_all("/<p class=\"info\">[.\s]{0,20}<span class=\"url\">(www.){0,1}([\sa-zA-Z0-9-(<strong>)(<\/strong>)]*\.[.\s]{0,20})+(<strong>)?.*(<\/strong>)?/i", $source, $match);
       
        return $match;
    }

    /**
     *
     * @param type $id
     * @return type boolean
     */
//    public function getPositionKeywords($id) {
//        return $this->findKeyword($this->keywords, $id);
//    }

    private function getYqlContent($urlEngine, $keyword){
        $curl = new \Components\Seo\Curl\cURL();
        $kw = \urlencode($keyword);
        $urlElement = \str_replace('/*keyvord*/', $kw, $urlEngine);
        $pages = array(1, 21, 41, 61, 81);

        $data = array();
        for($i=0; $i<5; $i++){
            $yqlUrl = "http://query.yahooapis.com/v1/public/yql";
            $query = "select * from html where url='".$urlElement.$pages[$i]."'";
            //\dump($query);
            $queryUrl = $yqlUrl . "?q=" . urlencode($query) . "&format=xml";

            $session = curl_init($queryUrl);
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_TIMEOUT, 15);
            $xml = curl_exec($session);
            if(!\strlen($xml) > 1000){
//                echo \htmlspecialchars($xml);
//                echo "<br /><br /><br />";
            }
            if($xml === null || $xml === ""){
                $xml = $curl->get($urlElement.$pages[$i]);
            }
            $data[$i] = $xml;
            curl_close($session);
        }
        //echo \htmlspecialchars($json);
        return $data;
    }
    
   
    
}
?>

<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FindKeywordsBaseModel
 *
 * @author wossa
 */
namespace Components\Seo\SearchEngine;

use Nette\Object, Nette\Debug, AdminModule\models\database\Slovo,
    \AdminModule\models\database\Web,
    \AdminModule\models\database\Test;

//require_once LIBS_DIR . "/Ext/simple_html_dom.php";

abstract class FindKeywordsBase extends \BaseModel implements FindKeywordsInterface{


    protected $url;
    protected  $keywords;
    protected $hloubka = 10;
    
    const PAGE_1 = "1";
    const PAGE_2 = "11";
    const PAGE_3 = "21";
    const PAGE_4 = "31";
    const PAGE_5 = "41";
    const PAGE_6 = "51";
    const PAGE_7 = "61";
    const PAGE_8 = "71";
    const PAGE_9 = "81";
    const PAGE_10 = "91";

    const GOOG_PAGE_1 = "0";
    const GOOG_PAGE_2 = "10";
    const GOOG_PAGE_3 = "20";
    const GOOG_PAGE_4 = "30";
    const GOOG_PAGE_5 = "40";
    const GOOG_PAGE_6 = "50";
    const GOOG_PAGE_7 = "60";
    const GOOG_PAGE_8 = "70";
    const GOOG_PAGE_9 = "80";
    const GOOG_PAGE_10 = "90";

    public function __construct($url, $keyWords){
        parent::__construct();
        $this->keywords = $keyWords;
        $this->url = $url;
    }


    //public abstract function getPositionKeywords();

    //protected abstract function findKeyword(String $keyword);

    /**
     * throw CannotLoadHtmlException
     * @param string $url
     * @return array
     */
    protected function loadHtml($url) {
        //$html = \file_get_html($url);
        
        $data = array();
        $http = new \mixed\CurlAsync();
        
//        $starttime = microtime();
//        $startarray = explode(" ", $starttime);
//        $starttime = $startarray[1] + $startarray[0];
        
        $http->page1($url . self::PAGE_1);
        $http->page2($url . self::PAGE_2);
        $http->page3($url . self::PAGE_3);
        $http->page4($url . self::PAGE_4);
        $http->page5($url . self::PAGE_5);
        $http->page6($url . self::PAGE_6);
        $http->page7($url . self::PAGE_7);
        $http->page8($url . self::PAGE_8);
        $http->page9($url . self::PAGE_9);
        $http->page10($url . self::PAGE_10);
        
        $data[0] = $http->page1();
        $data[1] = $http->page2();
        $data[2] = $http->page3();
        $data[3] = $http->page4();
        $data[4] = $http->page5();
        $data[5] = $http->page6();
        $data[6] = $http->page7();
        $data[7] = $http->page8();
        $data[8] = $http->page9();
        $data[9] = $http->page10();
        
        
        return $data;
        
    }


    protected function loadHtmlGoogle($url) {

        $data = array();
        $http = new \mixed\CurlAsync();


        $http->page1($url . self::GOOG_PAGE_1);
        $http->page2($url . self::GOOG_PAGE_2);
        $http->page3($url . self::GOOG_PAGE_3);
        $http->page4($url . self::GOOG_PAGE_4);
        $http->page5($url . self::GOOG_PAGE_5);
        $http->page6($url . self::GOOG_PAGE_6);
        $http->page7($url . self::GOOG_PAGE_7);
        $http->page8($url . self::GOOG_PAGE_8);
        $http->page9($url . self::GOOG_PAGE_9);
        $http->page10($url . self::GOOG_PAGE_10);

        $data[0] = $http->page1();
        $data[1] = $http->page2();
        $data[2] = $http->page3();
        $data[3] = $http->page4();
        $data[4] = $http->page5();
        $data[5] = $http->page6();
        $data[6] = $http->page7();
        $data[7] = $http->page8();
        $data[8] = $http->page9();
        $data[9] = $http->page10();

        return $data;
    }
    
    /**
     * Split string of keywords
     * @param type string $keywords
     * @return array 
     */
    protected function validateKeywords($keywords){
        if(!\is_array($keywords)){
            $keywords = \explode(",", $keywords);
            for($i = 0; $i < \count($keywords); $i++){
                $keywords[$i] = trim($keywords[$i]);
            }
        }
        return $keywords;
    }
    
    
    /**
     * return damain name
     * @param type $url
     * @return string 
     */
    protected function getDomainName($url){
        $result = "";
        //Debug::fireLog("URL URL URL URL -----". $url);
        if(preg_match('/www./', $url)){
            $url = preg_replace('/(www.)(\.*)/', "", $url);
            preg_match('/([a-zA-Z0-9-]*\.)+[a-zA-Z]{2,4}/', $url, $result);
        }else{
            preg_match('/([a-zA-Z0-9-]*\.)+[a-zA-Z]{2,4}/', $url, $result);
        }
        return $result[0];
    }
    
    /**
     * Check keyword if exist in database
     * @param type string $keyword
     * @return type array
     */
    protected function checkKeywords($id, $keyword, $id_engine){
        $fetchDataModel = new FetchData();
//        $result = $fetchDataModel->getTestEntity($keyword);
        $result = null;
        if(CheckKeyword::$word === $keyword){
            $result = CheckKeyword::$already;
        }else{
            $result = CheckKeyword::checkWord($id, $keyword, $id_engine);
        }

        if($result){
            $date = new \DateTime();
            $date = $date->format('Y-m-d H:i:s');
//
//            $diff = $this->dateDifference($date, $result->es_word_last_test);
//            \dump($diff);
//            $count_row = $fetchDataModel->getCountTests($result->es_word_id, $id_engine);
            
            if(CheckKeyword::$update){
                /**
                 * ------------------------------------------------------------------------------------------
                 *  Zkopirovani hodnot do historie zaznamu...
                 *  Smazani hodnot
                 * ------------------------------------------------------------------------------------------
                 */
                 $return = $fetchDataModel->copyToArchive(CheckKeyword::$wordId, $id_engine, $date, $this->url);
                 return array("already" => true, "update" => true, "pozice" => $return["pozice"], "url" => $return["url"]);
            }
            if(!CheckKeyword::$update){
                return array("already" => true, "update" => false, "pozice" => null, "url" => null);
            }else{
                return array("already" => true, "update" => true, "pozice" => null, "url" => null);
            }
        }else{
            CheckKeyword::$already = true;
            return array("already" => false, "update" => null, "pozice" => null, "url" => null);
            //CheckKeyword::$already = true;
        }        
    }
    
    /**
     *
     * @param type Date $new
     * @param type Date $old
     * @return int 
     */
    private function dateDifference($new, $old){
        $diff = strtotime($new) - strtotime($old);
        //$diff = (($diff / 60) / 60);
        return $diff;
    }
    
    /**
     *
     * @param type $keyword
     * @return array of html page's 
     */
    protected function getHtmlCode($urlEngine, $keyword, $google = true){
        $kw = \urlencode($keyword);
        $urlElement = \str_replace('/*keyvord*/', $kw, $urlEngine);
        if($google == null){
            return $this->loadHtml($urlElement);
        }else{
            return $this->loadHtmlGoogle($urlElement);
        }
    }
    
    /**
     *
     * @param type $keyword
     * @param type $keywordsTest
     * @param type $hloubka
     * @param type $validate
     * @param type $web 
     */
    protected function insertToDatabase($keyword, $keywordsTest, $hloubka, $validate, $web, $engine_id){
        try{
            $date = new \DateTime();
            $date = $date->format('Y-m-d H:i:s');
            $slovo_id = null;
            
            if(!$validate["already"]){
                Slovo::insert($date, $hloubka, $keyword);
                $slovo_id = Slovo::getWordId($keyword);
                Web::addWord($web, $slovo_id);
            }else{
                $slovo_id = Slovo::getWordId($keyword);
            }

            $insert = "INSERT INTO elpod_seo_test VALUES";
            $i = 0;
            foreach ($keywordsTest as $row){
                if($i !== 0){
                    $insert .= ",";
                }
                if($validate["url"] !== $row["domain"]){
                    $insert .= "(".\intval($slovo_id).",".\intval($engine_id).","
                        .\intval($row["position"]).",null, null, '".\mysql_escape_string($row["domain"])."','"
                        .\DATE_NOW."')";
                    //Test::insert($slovo_id, $engine_id, $row["position"], null, $row["domain"], $date);
                }else{
                    $insert .= "(".\intval($slovo_id).",".\intval($engine_id).","
                        .\intval($row["position"]).",".\intval($validate["pozice"]).", null, '"
                        .\mysql_escape_string($row["domain"])."','"
                        .\DATE_NOW."')";
                    //Test::insert($slovo_id, $engine_id, $row["position"], $validate["pozice"], $row["domain"], $date);
                }
                //\dibi::test("INSERT INTO test VALUES(%t)", $date);
                $i++;
            }
            if($i !== 0){
                Test::insert($insert);
            }
            //echo \htmlspecialchars($insert);
            
        }catch(Exception $exception){
            throw $exception;
        }
    }
    
    protected abstract function findListOfUrl($source);
}
?>

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

class FindKeywordsJyxo extends FindKeywordsBase implements FindKeywordsInterface{

    private $urlEngine;
    private $keywordPosition;
    const HLOUBKA = 10;
    const ENGINE_ID = 5;

    public function __construct($keywords,$url = null){
        parent::__construct($url, $this->validateKeywords($keywords));


        $this->urlEngine = "http://jyxo.1188.cz/s?q=/*keyvord*/&d=cz&cnt=100";
    }


    /**
     *
     * @param type $keyword
     * @param type $id
     * @return type
     */
    public function getPositionKeywords($id = null, $store = true) {

        $data = array();

        foreach ($this->keywords as $value){
            //\dibi::begin();
            $count = 0;
            if($store){
                $validate = $this->checkKeywords($id, $value, self::ENGINE_ID);

                if($validate["already"] === true && $validate["update"] === false && CheckKeyword::$addWebWord === true){
                    continue;
                }
            }

            $this->keywordPosition = array();

            //$content = $this->getHtmlCode($this->urlEngine, $value);
            //foreach ($content as $html) {

            $urlElement = \str_replace('/*keyvord*/', \urlencode($value), $this->urlEngine);
            $html = $this->downloadSource($urlElement);
                $match = $this->findListOfUrl($html);
                $j = 1;

                //Debug::fireLog("------------------------- PRUCHOD -----------------------");
                foreach ($match[0] as $element) {
                    //$element = \strip_tags($element);
                    $domain = $this->getDomainName($element);
                    $element = str_replace("<div class=ro>http://", "", $element);

                    $count++;
                    //Debug::fireLog($element);
                    //Debug::fireLog($count);
                    if ($store) {
                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain);
                    } else {

                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain, "word" => $value, "engine" => "centrum");
                    }
                }
            //}

            if($store){
                $this->insertToDatabase($value, $this->keywordPosition, 10, $validate, $id, self::ENGINE_ID);
            }else{
                $data[] = $this->keywordPosition;
            }
            $this->keywordPosition = null;
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
        //preg_match_all("/<p class=\"info\"> <span class=\"url\">[a-zA-Z\.\/(<b>)(<\/b>)0-9\-#&_;+?=\[\]\(\)\"\'\$\:%áčďéěíňóřšťúůýžÁČĎÉĚÍŇÓŘŠŤÚŮÝŽ\<\>~,!]*<\/span>/i", $source, $match);
        preg_match_all("/<div class=ro>http:\/\/([a-zA-Z0-9-]*\.)+[a-zA-Z]{2,4}/i", $source, $match);
        return $match;
    }

    private function downloadSource($url){
        $curl = new \Components\Seo\Curl\cURL();
        return $curl->get($url);
    }

    /**
     *
     * @param type $id
     * @return type boolean
     */
//    public function getPositionKeywords($id) {
//        return $this->findKeyword($this->keywords, $id);
//    }



}
?>

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

class FindKeywordsCentrum extends FindKeywordsBase implements FindKeywordsInterface{

    private $urlEngine;
    private $keywordPosition;
    const HLOUBKA = 10;
    const ENGINE_ID = 4;

    public function __construct($keywords,$url = null){
        parent::__construct($url, $this->validateKeywords($keywords));


        $this->urlEngine = "http://search.centrum.cz/index.php?q=/*keyvord*/&sec=mix&offset=0&l=cs&from=";
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

                if($validate["already"] === true && $validate["update"] === false  && CheckKeyword::$addWebWord === true){
                    continue;
                }
            }

            $this->keywordPosition = array();


            $content = $this->getHtmlCode($this->urlEngine, $value);
            
            foreach ($content as $html) {
                $match = $this->findListOfUrl($html);
                $j = 1;

                //Debug::fireLog("------------------------- PRUCHOD -----------------------");
                foreach ($match[0] as $element) {
                    //$element = \strip_tags($element);
                    $domain = $this->getDomainName($element);
                    $element = str_replace("<a class=\"uri\" href=\"http://", "", $element);
                    if($element !== "cz.hit.gemius.pl" && $element !== "centrumcz.hit.gemius.pl"){

                        $count++;
                        //Debug::fireLog($element);
                        //Debug::fireLog($count);
                        if($store){
                            $this->keywordPosition[] = array("position" => $count, "domain" => $domain);
                        }else{

                            $this->keywordPosition[] = array("position" => $count, "domain" => $domain, "word" => $value, "engine" => "centrum");
                        }
                    }
                }
            }

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
        preg_match_all("/<a class=\"uri\" href=\"http:\/\/([a-zA-Z0-9-]*\.)+[a-zA-Z]{2,4}/i", $source, $match);
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



}
?>

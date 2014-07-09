<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FindKeywordsBingModel
 *
 * @author WoSSa
 */
namespace Components\Seo\SearchEngine;

//use AdminModule\KeywordsModule\FindKeywordsBaseModel, AdminModule\KeywordsModule\FindKeywordsInterface, Nette\Debug;
use Nette\Debug;

class FindKeywordsBing extends FindKeywordsBase implements FindKeywordsInterface{
    
    private $urlEngine;
    private $keywordPosition;
    const HLOUBKA = 10;
    const ENGINE_ID = 3;
    
    public function __construct($keywords,$url = null){
        parent::__construct($url, $this->validateKeywords($keywords));
        

        $this->urlEngine = "http://www.bing.com/search?q=/*keyvord*/&go=&qs=n&sk=&first=";
    }

    public function getPositionKeywords($id, $store = true) {
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

                foreach ($match[0] as $element) {
                    $element = \strip_tags($element);
                    $domain = $this->getDomainName($element);
                    
                    $count++;
                    if($store){
                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain);
                    }else{
                        
                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain, "word" => $value, "engine" => "bing");
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
    
    protected function findListOfUrl($source) {
        preg_match_all("/<div class=\"sb_meta\"><cite>([a-zA-Z0-9-(<b>)(<\/b>)]*\.)+(<b>)?[a-zA-Z]{2,4}(<\/b>)?/i", $source, $match);
    
        return $match;
    }
}

?>

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FindKeywordsGoogleModel
 *
 * @author WoSSa
 */
namespace Components\Seo\SearchEngine;

//use AdminModule\KeywordsModule\FindKeywordsBaseModel, AdminModule\KeywordsModule\FindKeywordsInterface, Nette\Debug;
use Nette\Debug;

class FindKeywordsGoogle extends FindKeywordsBase implements FindKeywordsInterface{
    
    private $urlEngine;
    private $keywordPosition;
    const HLOUBKA = 10;
    const ENGINE_ID = 2;

    public function __construct($keywords,$url = null){
        parent::__construct($url, $this->validateKeywords($keywords));
        

        $this->urlEngine = "http://www.google.cz/search?q=/*keyvord*/&hl=cs&start=";
    }

    public function getPositionKeywords($id = null, $store = true) {
        $data = array();
        foreach ($this->keywords as $value){
            //\dibi::begin();
            $count = 0;
            if($store){
                $validate = $this->checkKeywords($id, $value, self::ENGINE_ID);

                \dump(CheckKeyword::$update);
                if($validate["already"] === true && $validate["update"] === false && CheckKeyword::$update !== true && CheckKeyword::$addWebWord === true){
                    continue;
                }
            }

            $this->keywordPosition = array();

            $content = $this->getHtmlCode($this->urlEngine, $value);

            foreach ($content as $html) {
                //\dump($content);
                $match = $this->findListOfUrl($html);
                $j = 1;

                foreach ($match[0] as $element) {
                    $element = \strip_tags($element);
                    $domain = $this->getDomainName($element);
                    
                    
                    $count++;
                    Debug::fireLog($element);
                    Debug::fireLog($count);
                    if($store){
                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain);
                    }else{
                        $this->keywordPosition[] = array("position" => $count, "domain" => $domain, "word" => $value, "engine" => "google");
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
        preg_match_all("/<span class=f><cite>(<span class=bc>)?([a-zA-Z0-9-(<b>)(<\/b>)]*\.)+(<b>)?[a-zA-Z]{2,4}(<\/b>)?/i", $source, $match);
        //Debug::dump($match);
        return $match;
    }
}
//
//
//select `web`.`id_web` AS `id_web`,`web`.`id_uzivatel` AS `id_uzivatel`,`web`.`web_url` AS `web_url`,
//    `slovo`.`slovo_id` AS `slovo_id`,`slovo`.`posledni_test` AS `posledni_test`,`slovo`.`hloubka` AS `hloubka`,
//    `slovo`.`slovo` AS `slovo`,`test`.`pozice` AS `pozice`,`test`.`stara_pozice` AS `stara_pozice`,`vyhledavac`.`nazev` AS `nazev`
//    
//    from ((((`web` join `web_slovo` on((`web`.`id_web` = `web_slovo`.`id_web`))) join `slovo` on((`web_slovo`.`id_slovo` = `slovo`.`slovo_id`)))
//    left join `test` on( ((`slovo`.`slovo_id` = `test`.`id_slovo`) and (`web`.`web_url` = `test`.`url`))
//        AND ( (`test`.`id_vyhledavac` = 1) OR (`test`.`id_vyhledavac` = 2) ) ))
//    left join `vyhledavac` on((`test`.`id_vyhledavac` = `vyhledavac`.`id_vyhledavac`))) 
//    
//    group by `vyhledavac`.`nazev`,`slovo`.`slovo` 
//    order by `slovo`.`slovo_id`
//    
//    
//    
//select `web`.`id_web` AS `id_web`,`web`.`id_uzivatel` AS `id_uzivatel`,`web`.`web_url` AS `web_url`,`slovo`.`slovo_id` AS `slovo_id`,`slovo`.`posledni_test` AS `posledni_test`,`slovo`.`hloubka` AS `hloubka`,`slovo`.`slovo` AS `slovo`,`test`.`pozice` AS `pozice`,`test`.`stara_pozice` AS `stara_pozice`,`vyhledavac`.`nazev` AS `nazev` from ((((`web` join `web_slovo` on((`web`.`id_web` = `web_slovo`.`id_web`))) join `slovo` on((`web_slovo`.`id_slovo` = `slovo`.`slovo_id`))) left join `test` on(((`slovo`.`slovo_id` = `test`.`id_slovo`) and (`web`.`web_url` = `test`.`url`)))) left join `vyhledavac` on((`test`.`id_vyhledavac` = `vyhledavac`.`id_vyhledavac`))) group by `vyhledavac`.`nazev`,`slovo`.`slovo` order by `slovo`.`slovo_id`


?>


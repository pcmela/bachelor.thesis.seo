<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Test
 *
 * @author WoSSa
 */
namespace AdminModule\models\database;

class Test extends \BaseModel{
    
    public static function insert($query){
        //\dibi::test($query);
        self::$defaultConnection->query($query);        
    }

    public static function lastTest($webId, $domain){
        
        try{


            return \dibi::fetchAll("select word.es_word_id as word_id, test.es_test_search_engine_id, test.es_test_date, test.es_test_position, web_word.es_web_word_weight as weight from elpod_seo_web as web
                join elpod_seo_web_word as web_word on web.es_web_id = web_word.es_web_word_web_id and web.es_web_id = %i",$webId,"
                join elpod_seo_word as word on web_word.es_web_word_word_id = word.es_word_id
                join elpod_seo_test as test on test.es_test_word_id = word.es_word_id and test.es_test_domain = %s",$domain,"
                group by test.es_test_date, test.es_test_search_engine_id, word.es_word_id");
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }
}

?>




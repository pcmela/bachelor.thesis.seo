<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Web
 *
 * @author WoSSa
 */
namespace AdminModule\models\database;

class Web extends \BaseModel{
    
    public static function addWord($web, $word){
        self::$defaultConnection->query("INSERT INTO elpod_seo_web_word VALUES (%i", $web ,", %i", $word ,", 10)");
    }

    public static function getUrl($web){
        return self::$defaultConnection->fetch("SELECT es_web_url FROM elpod_seo_web WHERE es_web_id =%i", $web);
    }

    public static function getAllWord($id){
        try{
            return self::$defaultConnection->fetchAll("select word.es_word_id as word_id, word.es_word_word as word_word, web_word.es_web_word_weight as weight from elpod_seo_web as web
                join elpod_seo_web_word as web_word on web.es_web_id = web_word.es_web_word_web_id and web.es_web_id = ".$id."
                join elpod_seo_word as word on web_word.es_web_word_word_id = word.es_word_id");
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function getDomain($id){
        try{
            return self::$defaultConnection->fetch("select es_web_url from elpod_seo_web where es_web_id = %i", $id);
        }catch(\DibiException $exception){
            throw $exception;
        }
    }

    public static function updateWeightWebWord($idWeb, $idWord, $weight){
        try{
            self::$defaultConnection->query("update elpod_seo_web_word set es_web_word_weight = %i",$weight,"
                    where es_web_word_web_id = %i",$idWeb," and es_web_word_word_id = %i",$idWord);
        }catch(\DibiException $exception){
            throw $exception;
        }
    }
}

?>

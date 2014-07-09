<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Slovo
 *
 * @author WoSSa
 */
namespace AdminModule\models\database;

class Slovo extends \BaseModel{
    
    public static function insert($date, $deep, $word){
        self::$defaultConnection->query("INSERT INTO elpod_seo_word(es_word_last_test, es_word_depth, es_word_word) VALUES (%t", $date, ", %i", $deep, ", %s", $word, ")");
    }
    
    public static function getWordId($word){
        $slovo = self::$defaultConnection->fetch("SELECT es_word_id FROM elpod_seo_word WHERE es_word_word = %s", $word)->es_word_id;
       
        return \intval($slovo);
    }

    public static function deleteWord($idWord, $idWeb){
        try{
            $countWeb = self::$defaultConnection->fetch("SELECT count(*) as count from elpod_seo_web_word where es_web_word_word_id = %i", $idWeb);
            if($countWeb->count > 0){
               self::$defaultConnection->query("DELETE FROM elpod_seo_web_word WHERE es_web_word_word_id = %i", $idWeb, "
                   AND es_web_word_word_id = %i", $idWord);
               return true;
            }

            self::$defaultConnection->query("DELETE FROM elpod_seo_web_word WHERE es_web_word_word_id = %i", $idWord, "
                   AND es_web_word_web_id = %i", $idWeb);
            self::$defaultConnection->query("DELETE FROM elpod_seo_archive WHERE es_archive_word_id = %i", $idWord);
            self::$defaultConnection->query("DELETE FROM elpod_seo_test WHERE es_test_word_id = %i", $idWord);
            self::$defaultConnection->query("DELETE FROM elpod_seo_word WHERE es_word_id = %i", $idWord);

            return true;
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }
}

?>

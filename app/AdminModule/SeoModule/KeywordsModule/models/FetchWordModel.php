<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FecthWordModel
 *
 * @author wossa
 */
namespace AdminModule\SeoModule\KeywordsModule;

class FetchWordModel extends \BaseModel{

    /**
     *
     * @param int $id
     * @return array
     */
    public function fetchUpdatesWords($id){
        try{
            return $this->connection->fetchAll("select word.es_word_word, word.es_word_last_test from elpod_seo_web as web
                join elpod_seo_web_word as web_word on web.es_web_id = web_word.es_web_word_web_id and
                web.es_web_id = %i", \intval($id) ," join elpod_seo_word as word on
                web_word.es_web_word_word_id = word.es_word_id");
        }catch(DibiException $exception){
            throw $exception;
        }
    }

    /**
     *
     * @param int $id
     * @return array
     */
    public function getAllWordsFromUser($id){
        try{

            return $this->connection->fetchAll("select elpod_seo_word.es_word_word
                FROM elpod_seo_web
                 JOIN elpod_seo_web_word ON elpod_seo_web.es_web_id = elpod_seo_web_word.es_web_word_web_id and elpod_seo_web.es_web_id = %i",$id,"
                 JOIN elpod_seo_word ON elpod_seo_web_word.es_web_word_word_id = elpod_seo_word.es_word_id");
        }catch(DibiException $exception){

        }
    }

    /**
     *
     * @param string $word
     * @param id $idWeb
     * @return array
     */
    public function deleteWord($word, $idWeb){
        try{
            $idWord = $this->connection->fetch("SELECT es_word_id FROM elpod_seo_word WHERE es_word_word = %s", $word);
            $idWord->es_word_id;
            return \AdminModule\models\database\Slovo::deleteWord($idWord, $idWeb);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    
}
?>

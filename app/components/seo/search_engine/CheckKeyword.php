<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CheckKeyword
 *
 * @author wossa
 */
namespace Components\Seo\SearchEngine;

final class CheckKeyword extends \BaseModel{

    public static $word;
    public static $update;
    public static $already;
    public static $wordId;
    public static $countRow;
    public static $addWebWord = false;
    /**
     *
     * @var FetchData
     */
    private static $fetchModel;

    public static function checkWord($id, $word, $engine){
        if($word === self::$word){
            return self::$already;
        }else{
            self::$word = $word;
            self::check($id, $word, $engine);
            return self::$already;
        }
    }

    private static function check($id, $word, $engine){
        if(self::$fetchModel === null){
                self::getModel();
            }

        $already = self::$fetchModel->getTestEntity($word);
        if($already){
            
            self::$wordId = $already->es_word_id;
            self::$already = true;
            $webWord = self::getModel()->getWordWeb($id, self::$wordId);

            if($webWord === false){
                self::getModel()->insertWordWeb($id, self::$wordId);
                self::$update = false;
                self::$addWebWord = true;
            }else{

                $date = new \DateTime();
                $date = $date->format('Y-m-d H:i:s');

                $diff = self::dateDifference($date, $already->es_word_last_test);
                //\dump($diff);

                self::$countRow = self::$fetchModel->getCountTests($already->es_word_id, $engine);

                if(self::$update === null){
                    if($diff){
                        self::$update = true;
                    }else{
                        self::$update = false;
                    }
                }
            }
        }else{
            self::$already = false;
        }
    }

    private static function getModel(){
        if(self::$fetchModel === null){
            self::$fetchModel = new FetchData();
        }

        return self::$fetchModel;
    }

    private static function dateDifference($new, $old){
        $diff = strtotime($new) - strtotime($old);
        //$diff = (($diff / 60) / 60);
        return $diff;
    }
}
?>

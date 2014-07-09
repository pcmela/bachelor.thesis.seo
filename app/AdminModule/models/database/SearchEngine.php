<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vyhledavac
 *
 * @author WoSSa
 */
namespace AdminModule\models;

class SearchEngine extends \BaseModel {


    public static function getAll(){
        try{
            return \dibi::fetchAll("select es_search_engine_id, es_search_engine_name from elpod_seo_search_engine");
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }
}

?>

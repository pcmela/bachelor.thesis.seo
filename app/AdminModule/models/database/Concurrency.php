<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AdminModule\models\database;

class Concurrency extends \BaseModel{

    public static function get($id){
        try{
            return \dibi::fetchAll("select concurrency.es_concurrency_domain
                from elpod_seo_web as web
                join elpod_seo_concurrency as concurrency on web.es_web_id = concurrency.es_concurrency_web_id and web.es_web_id = %i", $id);
        }catch(\DibiDriverException $exception){

        }
    }
}
?>

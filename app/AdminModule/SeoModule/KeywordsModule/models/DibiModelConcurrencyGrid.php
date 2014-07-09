<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DibiModelConcurrencyGrid
 *
 * @author wossa
 */
namespace AdminModule\SeoModule\KeywordsModule;

class DibiModelConcurrencyGrid extends \BaseModel{

    /**
     *
     * @param int $web
     */
    public function __construct($web){
        parent::__construct();

        $this->fluent = $this->connection->select("es_concurrency_domain")->from("elpod_seo_concurrency")->where("es_concurrency_web_id = %i", $web);
    }
}
?>

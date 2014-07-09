<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DibiModel
 *
 * @author wossa
 */
namespace Components\Seo\SearchEngine;

class DibiModel extends \Gridito\DibiFluentModel{

    
    public function __construct($fluent){
        parent::__construct($fluent);
    }

    public function getItems(){
        return $this->fluent;
    }
}
?>

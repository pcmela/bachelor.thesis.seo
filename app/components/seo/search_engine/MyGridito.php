<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyGridito
 *
 * @author wossa
 */
namespace Components\Seo\SearchEngine;

class MyGridito extends \Gridito\Grid{

    protected function createTemplate(){
		return parent::createTemplate()->setFile(COMPONENTS_DIR . "/seo/search_engine/grid.phtml");
    }
}
?>

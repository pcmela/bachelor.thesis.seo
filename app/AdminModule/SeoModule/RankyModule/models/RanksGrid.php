<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyWebsGrid
 *
 * @author WoSSa
 */
namespace AdminModule\SeoModule\RankyModule;

class RanksGrid extends \Gridito\Grid {

    public function __construct(\Nette\IComponentContainer $parent = null, $name = null){
        parent::__construct($parent, $name);
    }
    
    protected function createTemplate() {
        return parent::createTemplate()->setFile(__DIR__ . "/../templates/grid.phtml");
    }

}

?>

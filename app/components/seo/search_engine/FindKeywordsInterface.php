<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author WoSSa
 */
namespace Components\Seo\SearchEngine;

interface FindKeywordsInterface {
    
    /**
     *
     * @param type $keyword
     * @param type $id
     * @return type 
     */
    public function getPositionKeywords($id);
}

?>

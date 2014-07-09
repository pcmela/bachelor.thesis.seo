<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Archive
 *
 * @author WoSSa
 */
class Archive {
    private $id;
    private $slovo;
    private $vyhledavac;
    private $pozice;
    private $url;
    private $datum;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getSlovo() {
        return $this->slovo;
    }

    public function setSlovo($slovo) {
        $this->slovo = $slovo;
    }

    public function getPozice() {
        return $this->pozice;
    }

    public function setPozice($pozice) {
        $this->pozice = $pozice;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getDatum() {
        return $this->datum;
    }

    public function setDatum($datum) {
        $this->datum = $datum;
    }

    public function getVyhledavac() {
        return $this->vyhledavac;
    }

    public function setVyhledavac($vyhledavac) {
        $this->vyhledavac = $vyhledavac;
    }


}

?>

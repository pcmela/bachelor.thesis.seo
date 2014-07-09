<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Engine
 *
 * @author wossa
 */
namespace AdminModule\SeoModule\KeywordsModule;


class Engine extends \Nette\Object{

    private $engineId;
    private $word;
    private $lastPosition;
    private $lastDate;
    private $name;
    private $webName;

    /**
     *
     * @return int
     */
    public function getEngineId() {
        return $this->engineId;
    }

    /**
     *
     * @param int $engineId
     */
    public function setEngineId($engineId) {
        $this->engineId = $engineId;
    }

    /**
     *
     * @return string
     */
    public function getWord() {
        return $this->word;
    }

    /**
     *
     * @param string $word
     */
    public function setWord($word) {
        $this->word = $word;
    }

    /**
     *
     * @return int
     */
    public function getLastPosition() {
        return $this->lastPosition;
    }

    /**
     *
     * @param int $lastPosition
     */
    public function setLastPosition($lastPosition) {
        $this->lastPosition = $lastPosition;
    }

    /**
     *
     * @return DateTime
     */
    public function getLastDate() {
        return $this->lastDate;
    }

    /**
     *
     * @param DateTime $lastDate
     */
    public function setLastDate($lastDate) {
        $this->lastDate = $lastDate;
    }

    /**
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     *
     * @return <string
     */
    public function getWebName() {
        return $this->webName;
    }

    /**
     *
     * @param string $webName
     */
    public function setWebName($webName) {
        $this->webName = $webName;
    }



}
?>

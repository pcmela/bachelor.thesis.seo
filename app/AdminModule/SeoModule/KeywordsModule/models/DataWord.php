<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AdminModule\SeoModule\KeywordsModule;

class DataWord{
    private $word;
    private $data;

    /**
     *
     * @param string $word
     */
    function __construct($word) {
        $this->word = $word;
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
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     *
     * @param array $data
     */
    public function setData($data) {
        $this->data = $data;
    }
}

class ArticleWord{
    private $date;
    private $position;


    /**
     *
     * @return DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     *
     * @param DateTime $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     *
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     *
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }
}
?>

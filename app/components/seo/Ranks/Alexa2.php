<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Alexa
 *
 * @author wossa
 */
namespace Components\Seo\Ranks;

class Alexa2 extends \Nette\Object{
    /**
     *
     * @var string
     */
    private $xml;

    /**
     * @var int
     */
    private $alexaRank;
    /**
     *
     * @param <type> string
     */
    public function __construct($url) {
        $this->xml = simplexml_load_file("http://data.alexa.com/data?cli=10&dat=s&url=".$url);
        //\dump("http://data.alexa.com/data?cli=10&dat=s&url=".$url);
    }

    /**
     *
     * @return int
     */
    public function checkAlexaRank() {

        $rank = NULL;
        if($this->xml !== null){
            foreach ($this->xml->SD[1]->POPULARITY[0]->attributes() as $name => $desc) {
                $rank = $desc;
            }
        }
        if($rank === null){
            $rank = -1;
        }

        $this->alexaRank = $rank;
    }

    public function getAlexaRank() {
        return $this->alexaRank;
    }


}
?>

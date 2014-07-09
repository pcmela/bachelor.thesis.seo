<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bing
 *
 * @author wossa
 */
namespace Components\Seo\Ranks;

class Bing extends \Nette\Object{
    /**
     *
     * @var int
     */
    private $populatitySite;
    /**
     *
     * @var int
     */
    private $indexPages;
    /**
     *
     * @var string
     */
    private $htmlSource;
    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @param string $url
     */
    function __construct($url) {
        $this->url = $url;
    }

    /**
     *
     * @return int
     */
    public function getPopulatitySite() {
        $this->populatitySite = \str_replace(",", "", $this->populatitySite);
        return $this->populatitySite;
    }

    /**
     *
     * @return int
     */
    public function getIndexPages() {
        $this->indexPages = \str_replace(",", "", $this->indexPages);
        return $this->indexPages;
    }

    /**
     * @access private
     * @param String $url
     * Set property url and content for search engine Bing - Index pages
     */
    private function setParamIndex() {
        $urlElement = str_replace("http://", "", $this->url);
        $urlElement = str_replace("www.", "", $urlElement);
        $urlElement = "http://www.bing.com/search?q=site:" . $urlElement;
        return \file_get_html($urlElement);
    }

    private function setParamUrlInPages() {
        $urlElement = str_replace("http://", "", $this->url);
        $urlElement = str_replace("www.", "", $urlElement);
        $urlElement = "http://www.bing.com/search?q=". $urlElement ."+-site:" . $urlElement;
        return \file_get_html($urlElement);
    }

    /**
     * @access private
     * @return Bing $backlinks
     * Search stats in Bing
     */
    private function findLinks(\simple_html_dom $dom) {
        $backlinks = $dom->find("span[id=count]", 0);
        if ($backlinks !== NULL) {
            $backlinks = $backlinks->plaintext;
            //\dump($backlinks);
            $pattern = '/[0-9]-[0-9](0)?/';
            $replacement = '';
            $backlinks = \preg_replace($pattern, $replacement, $backlinks);
            //$backlinks = str_replace("1-10 of", "", $backlinks);
            $backlinks = str_replace("of", "", $backlinks);
            $backlinks = str_replace("results", "", $backlinks);
            $backlinks = trim($backlinks);
            //\dump($backlinks);

            return $backlinks;
        }

        return 0;
    }

    public function checkIndexPages(){
        $this->indexPages = $this->findLinks($this->setParamIndex());
    }

    public function checkPopularitySite(){
        $this->populatitySite = $this->findLinks($this->setParamUrlInPages());
    }
}
?>

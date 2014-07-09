<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Yahoo
 *
 * @author wossa
 */
namespace Components\Seo\Ranks;

class Yahoo extends \Nette\Object{

    private $url;
    private $backlinks;

    function __construct($url) {
        $this->url = $url;
    }

    public function getBacklinks() {
        return $this->backlinks;
    }

    private function findBackLinks(\simple_html_dom $dom) {
        $backlinks = $dom->find("a[class=btn]", 0);

        if ($backlinks !== NULL) {
            $backlinks = $backlinks->plaintext;
            $backlinks = str_replace("Inlinks", "", $backlinks);
            $backlinks = str_replace("(", "", $backlinks);
            $backlinks = str_replace(")", "", $backlinks);

            $backlinks = \str_replace(",", "", $backlinks);
            $this->backlinks = $backlinks;
        }else{
            $this->backlinks = 0;
        }
    }

    public function checkYahooSiteExplorer(){
        $url = str_replace("http://", "", $this->url);
        $url = "http://siteexplorer.search.yahoo.com/search?p=" . $url;
        //echo \htmlspecialchars(\file_get_contents($url));
        $this->findBackLinks(file_get_html($url));
    }

}
?>

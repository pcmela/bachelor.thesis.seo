<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Google
 *
 * @author wossa
 */
namespace Components\Seo\Ranks;

class Google extends \Nette\Object{

    private $populatitySite;
    private $indexPages;
    private $url;
    private $pageRank;
    private $wiki;

    function __construct($url) {
        $this->url = $url;
    }

    public function getPopulatitySite() {
        return $this->populatitySite;
    }

    public function getIndexPages() {
        return $this->indexPages;
    }


    private function setParamUrlInPages() {
        $urlElement = str_replace("http://", "", $this->url);
        $urlElement = str_replace("www.", "", $urlElement);
        $urlElement = "http://www.google.com/search?hl=en&q=%22" . $urlElement . "%22+-site:" . $urlElement;
        return \file_get_html($urlElement);
    }


    private function setParamIndex() {
        $urlElement = str_replace("http://", "", $this->url);
        $urlElement = str_replace("www.", "", $urlElement);
        $urlElement = "http://www.google.cz/search?q=site:" . $urlElement;
        return \file_get_html($urlElement);
    }

    private function setParamWiki() {
        $urlElement = str_replace("http://", "", $this->url);
        $urlElement = str_replace("www.", "", $urlElement);
        $urlElement = "http://www.google.cz/search?q=site:wikipedia.org+\"". $urlElement ."\"";

        return \file_get_html($urlElement);
    }


    private function findLinks(\simple_html_dom $dom) {
        $backlinks = $dom->find("div[id=resultStats]", 0);
        if ($backlinks !== NULL) {
            $backlinks = $backlinks->innertext;
            $backlinksEnd = str_get_html($backlinks)->find("nobr", 0)->plaintext;
//            if (substr($backlinks, 0, 5) === "About") {
//                $backlinks = explode(" ", $backlinks);
//            } else {
//                $backlinks = explode(":", $backlinks);
//            }
//            $backlinks = $backlinks[1];
            $backlinks = trim(str_replace($backlinksEnd, "", $backlinks));
            $backlinks = str_replace("&nbsp;", "", $backlinks);
            $backlinks = str_replace(",", "", $backlinks);
            //$backlinks = \utf8_decode($backlinks);
            $backlinks = \Nette\String::match($backlinks, '/[0-9][0-9]*/');
            if(\is_array($backlinks)){
                return $backlinks[0];
            }else{
                return 0;
            }
        }

        return 0;
    }

  

    public function checkPopularitySite(){
        $this->populatitySite = $this->findLinks($this->setParamUrlInPages());
    }

    public function checkIndexPages(){
        $this->indexPages = $this->findLinks($this->setParamIndex());
    }

    public function checkIndexPagesWiki(){
        $this->wiki = $this->findLinks($this->setParamWiki());
    }


    const GOOGLEHOST = 'toolbarqueries.google.com';
    const GOOGLEUA = 'Opera/9.63 (X11; Linux i686; U; en) Presto/2.1.1';

    private function strToNum($Str, $Check, $Magic) {
        $Int32Unit = 4294967296;  // 2^32
        $length = strlen($Str);
        for ($i = 0; $i < $length; $i++) {
            $Check *= $Magic;
            // If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31),
            //  the result of converting to integer is undefined
            //  refer to http://www.php.net/manual/en/language.types.integer.php
            if ($Check >= $Int32Unit) {
                $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
                //if the check less than -2^31
                $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
            }
            $Check += ord($Str{$i});
        }
        return $Check;
    }

    /**
     * genearate a hash for a url
     */
    private function  hashUrl($String) {
        $Check1 = $this->strToNum($String, 0x1505, 0x21);
        $Check2 = $this->strToNum($String, 0, 0x1003F);

        $Check1 >>= 2;
        $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
        $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
        $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);

        $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
        $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );

        return ($T1 | $T2);
    }

    /**
     * genearate a checksum for the hash string
     */
    private function checkHash($Hashnum) {
        $CheckByte = 0;
        $Flag = 0;

        $HashStr = sprintf('%u', $Hashnum) ;
        $length = strlen($HashStr);

        for ($i = $length - 1;  $i >= 0;  $i --) {
            $Re = $HashStr{$i};
            if (1 === ($Flag % 2)) {
                $Re += $Re;
                $Re = (int)($Re / 10) + ($Re % 10);
            }
            $CheckByte += $Re;
            $Flag ++;
        }

        $CheckByte %= 10;
        if (0 !== $CheckByte) {
            $CheckByte = 10 - $CheckByte;
            if (1 === ($Flag % 2) ) {
                if (1 === ($CheckByte % 2)) {
                    $CheckByte += 9;
                }
                $CheckByte >>= 1;
            }
        }

        return '7'.$CheckByte.$HashStr;
    }

    /**
     * return the pagerank checksum hash
     */
    private function getch($url) {
        return $this->checkHash($this->hashUrl($url));
    }

    /**
     * return the pagerank figure
     */
    private function getRank($url) {
        $ch = $this->getch($url);
        $fp = fsockopen(self::GOOGLEHOST, 80, $errno, $errstr, 30);
        if ($fp) {
            $out = "GET /search?client=navclient-auto&ch=$ch&features=Rank&q=info:$url HTTP/1.1\r\n";
            //echo "<pre>$out</pre>\n"; //debug only
            $out .= "User-Agent: " . self::GOOGLEUA . "\r\n";
            $out .= "Host: " . self::GOOGLEHOST . "\r\n";
            $out .= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);

            //$pagerank = substr(fgets($fp, 128), 4); //debug only
            //echo $pagerank; //debug only
            while (!feof($fp)) {
                $data = fgets($fp, 128);
                //echo $data;
                $pos = strpos($data, "Rank_");
                if($pos === false){} else{
                    $pr=substr($data, $pos + 9);
                    $pr=trim($pr);
                    $pr=str_replace("\n",'',$pr);
                    return $pr;
                }
            }
            //else { echo "$errstr ($errno)<br />\n"; } //debug only
            fclose($fp);
        }
    }

    public function checkRank(){
        $this->pageRank =  $this->getRank($this->url);
    }

    public function getPageRank() {
        return $this->pageRank;
    }

    public function getWiki() {
        return $this->wiki;
    }





}
?>

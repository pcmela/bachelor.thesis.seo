<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FactorsModule
 *
 * @author wossa
 */
namespace FrontModule\SeoModule\OnPageFactorsModule;

include LIBS_DIR . "/Ext/simple_html_dom.php";


class FactorsModel extends \BaseModel{


    /**
     *
     * @var String
     */
    private $htmlSource;
    /**
     *
     * @var Simple_HTML_Dom
     */
    private $htmlDom;
    /**
     *
     * @var Simple_HTML_Dom
     */
    private $htmlDomHead;
    /**
     *
     * @var Simple_HTML_Dom
     */
    private $htmlDomBody;

    /**
     *
     * @var String
     */
    private $url;

    // <editor-fold defaultstate="collapsed" desc="head">
    /**
     *
     * @var array
     */
    private $title;
    /**
     *
     * @var array
     */
    private $description;
    /**
     *
     * @var array
     */
    private $keywords;
    /**
     *
     * @var array
     */
    private $robotsInfo;
    /**
     *
     * @var array
     */
    private $doctype;
    /**
     *
     * @var array
     */
    private $charset;
    /**
     *
     * @var array
     */
    private $author;
    // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc="Files for search engine">
    /**
     *
     * @var boolean
     */
    private $robots_txt;
    /**
     *
     * @var boolean
     */
    private $sitemap;
    // </editor-fold>


    // <editor-fold defaultstate="collapsed" desc="HTML">
    /**
     *
     * @var array
     */
    private $validHtml;
    /**
     *
     * @var array
     */
    private $headlines;
    /**
     *
     * @var int
     */
    private $codeLarge;
    /**
     *
     * @var array;
     */
    private $unobtrusiveJavascript;
    /**
     *
     * @var array
     */
    private $extCss;
    /**
     *
     * @var int
     */
    private $htmlSize;

    /**
     *
     * @var boolean
     */
    private $nestedTables;
    /**
     *
     * @var boolean
     */
    private $altContent;
    /**
     *
     * @var int
     */
    private $textSize;
    /**
     *
     * @var array
     */
    private $links;
    // </editor-fold>


    public function __construct($url) {
        parent::__construct();

        $this->url = $this->validateUrl($url);
        $this->htmlSource = $this->getHtmlSource();
        $this->htmlDom = $this->getDom();
        $this->htmlDomHead = str_get_dom($this->htmlDom->find("head", 0)->outertext);
        $this->htmlDomBody = str_get_dom($this->htmlDom->find("body", 0)->outertext);

    }

    // <editor-fold defaultstate="collapsed" desc="Getters">

    public function getUrl() {
        return $this->url;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getRobotsInfo() {
        return $this->robotsInfo;
    }

    public function getDoctype() {
        return $this->doctype;
    }

    public function getCharset() {
        return $this->charset;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getRobots_txt() {
        return $this->robots_txt;
    }

    public function getSitemap() {
        return $this->sitemap;
    }

    public function getValidHtml() {
        return $this->validHtml;
    }

    public function getHeadlines() {
        return $this->headlines;
    }

    public function getCodeLarge() {
        return $this->codeLarge;
    }

    public function getUnobtrusiveJavascript() {
        return $this->unobtrusiveJavascript;
    }

    public function getExtCss() {
        return $this->extCss;
    }

    public function getHtmlSize() {
        return $this->htmlSize;
    }

    public function getNestedTables() {
        return $this->nestedTables;
    }

    public function getAltContent() {
        return $this->altContent;
    }

    public function getTextSize() {
        return $this->textSize;
    }

    public function getLinks() {
        return $this->links;
    }



        // </editor-fold>


    public function checkFactors(){
        $this->header();
        $this->htmlCode();
        $this->otherInfo();
    }

    private function getHtmlSource(){
        $curl = new \Components\Seo\Curl\cURL();
        return $curl->get($this->url);
    }

    private function validateUrl($url){
        $url = \str_replace("http://", "", $url);
//        if(!$pos){
//            $url = "http://".$url;
//        }
        $url = \str_replace("www.", "", $url);
        $url = \str_replace("/", "", $url);

        return $url;
    }

    private function getDom(){
        return \str_get_html($this->htmlSource);
    }

    private function header(){
        $this->title = $this->getTitleInfo();
        $this->description = $this->getDescriptionInfo();
        $this->keywords = $this->getKeywordsInfo();
        $this->author = $this->getAuthorInfo();
        $this->doctype = $this->getDoctypeInfo();
        $this->charset = $this->getCharsetInfo();
        $this->robotsInfo = $this->getRobotsMetaInfo();
    }

    private function htmlCode(){
        $this->validHtml = $this->validateHtmlSource();
        $this->headlines = $this->structHeadlines();
        $this->htmlSize = $this->getHtmlSizeInfo();
        $this->unobtrusiveJavascript = $this->findJavascriptCode();
        $this->extCss = $this->findCssCode();
    }

    private function otherInfo(){
        $this->nestedTables = $this->findNestedTables();
        $this->altContent = $this->findAlternateContent();
        $this->textSize = $this->textSize();
        $this->links = $this->countLinks();
        $this->sitemap = $this->getSitemapResponse();

    }

    /**
     *
     * @return String
     */
    private function getDoctypeInfo(){
        $array = array();
        preg_match("/<!DOCTYPE.*\.dtd\">/", $this->htmlSource, $match);
        if(isset($match[0])){
            if(\strlen($match[0]) > 0){
                $array["status"] = "ok";
                $array["data"] = "Stránka obsahuje definici dokumentu.";
            }else{
                $array["status"] = "error";
                $array["data"] = "Stránka neobsahuje definici dokumentu.";
            }
        }else{
            $array["status"] = "error";
            $array["data"] = "Stránka neobsahuje definici dokumentu.";
        }

        return $array;
    }

    private function getSitemapResponse(){
        $response = \get_headers("http://".$this->url."/sitemap.xml", 1);
        return $response[0];
    }

    /**
     *
     * @return String
     */
    private function getTitleInfo(){
        $array = array();

        preg_match("/<title.*<\/title>/", $this->htmlSource, $match2);
        if(strlen(strip_tags($match2[0])) > 0){
            $array["status"] = "ok";
            $array["data"] = "Stránka obsahuje Nadpis.";
        }else{
            $array["status"] = "error";
            $array["data"] = "Stránka neobsahuje Nadpis.";
        }

        return $array;
    }

    /**
     *
     * @return String
     */
    private function getDescriptionInfo(){
        $array = array();

        $dom = $this->htmlDomHead->find('meta[name=description]', 0);
        if(count($dom)>0){
            $array["status"] = "ok";
            $array["data"] = "Stránka obsahuje popis.";
        }else{
            $array["status"] = "error";
            $array["data"] = "Stránka neobsahuje popis.";
        }

        return $array;
    }

    /**
     *
     * @return String
     */
    private function getKeywordsInfo(){
        $array = array();

        $dom = $this->htmlDomHead->find('meta[name=keywords]', 0);
        if(count($dom)>0){
            $array["status"] = "ok";
            $array["data"] = "Stránka obsahuje v metaznačce klíčová slova.";
        }else{
            $array["status"] = "error";
            $array["data"] = "Stránka neobsahuje v metaznačce klíčová slova.";
        }

        return $array;
    }

    /**
     *
     * @return String
     */
    private function getAuthorInfo(){
        $array = array();

        $dom = $this->htmlDomHead->find('meta[name=author]', 0);
        if(count($dom)>0){
            $array["status"] = "ok";
            $array["data"] = "Stránka obsahuje v metaznačce autora.";
        }else{
            $array["status"] = "error";
            $array["data"] = "Stránka neobsahuje v metaznačce autora.";
        }

        return $array;
    }


    private function getCharsetInfo(){
        $array = array();

        $dom = $this->htmlDomHead->find('meta[http-equiv=Content-Type]', 0);
        if(count($dom)>0){
            $array["status"] = "ok";
            $array["data"] = "Stránka má specifikovanou znakovou sadu.";
        }else{
            $dom = $this->htmlDomHead->find('meta[http-equiv=content-Type]', 0);
            if(count($dom)>0){
                $array["status"] = "ok";
                $array["data"] = "Stránka má specifikovanou znakovou sadu.";
            }else{
                $array["status"] = "error";
                $array["data"] = "Stránka nemá specifikovanou znakovou sadu.";
            }
        }

        return $array;
    }


    private function getRobotsMetaInfo(){
        $array = array();

        $dom = $this->htmlDomHead->find('meta[name=robots]', 0);
        if(count($dom)>0){
            $array["status"] = "ok";
            $array["data"] = "Stránka obsahuje v metaznačce informace pro roboty.";
        }else{
            $array["status"] = "error";
            $array["data"] = "Stránka neobsahuje v metaznačce informace pro roboty.";
        }

        return $array;
    }

    private function validateHtmlSource(){
        $array = array();

        $validator = new \Components\Seo\W3C\Services_W3C_HTMLValidator();

        $val = $validator->validate($this->url);

        if($val->isValid()){
            $array["status"] = "ok";
            $array["data"] = "HTML kód je validní";
            $array["errors"] = 0;
            $array["warnings"] = 0;
            $array["uri"] = $val->uri;
            $array["doctype"] = $val->doctype;
        }else{
            $array["status"] = "error";
            $array["data"] = "HTML kód není validní";
            $array["errors"] = count($val->errors);
            $array["warnings"] = count($val->warnings);
            $array["uri"] = $val->checkedby;
            $array["doctype"] = $val->doctype;
        }

        return $array;
    }

    private function getHtmlSizeInfo(){
        return \strlen($this->htmlSource) / 1000;
    }


    private function findJavascriptCode(){
        $array = array();
        $size = 0;

        $dom = $this->htmlDom->find('script');
        if(count($dom) > 0){
            foreach ($dom as $script){
                $size += strlen($script->innertext);
            }
            $array["data"] = "Ve zdrojovém kódu je javascript";
            $array["size"] = $size / 1000;
        }else{
            $array["data"] = "Ve zdrojovém kódu není žádný javascript";
            $array["size"] = 0;
        }

        return $array;
    }

    private function findCssCode(){
        $array = array();
        $size = 0;

        $dom = $this->htmlDom->find('style');
        if(count($dom) > 0){
            foreach ($dom as $style){
                $size += strlen($style->innertext);
            }
            $array["data"] = "Ve zdrojovém kódu je css kód.";
            $array["size"] = $size / 1000;
        }else{
            $array["data"] = "Ve zdrojovém kódu není žádný css kód.";
            $array["size"] = 0;
        }

        return $array;
    }

    private function structHeadlines(){
        $array = array();

        $dom = $this->htmlDom->find("h1, h2, h3, h4, h5, h6. h7, h8");

        foreach ($dom as $element){
            $title = $this->formatTitle($element->outertext);

            $array[] = $title;
        }
        $array = $this->validateHeadlinesLevel($array);

        return $array;
    }

    private function formatTitle($title) {
        //\dump(\substr($title, 0, 2));
        $array = array();
        $array["status"] = null;
        switch (\substr($title, 0, 3)) {
            case "<h1":
                $array["level"] = 1;
                $array["data"] = "<h1>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h2":
                $array["level"] = 2;
                $array["data"] = "<h2>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h3":
                $array["level"] = 3;
                $array["data"] = "<h3>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h4":
                $array["level"] = 4;
                $array["data"] = "<h4>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h5":
                $array["level"] = 5;
                $array["data"] = "<h5>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h6":
                $array["level"] = 6;
                $array["data"] = "<h6>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h7":
                $array["level"] = 7;
                $array["data"] = "<h7>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h8":
                $array["level"] = 8;
                $array["data"] = "<h8>" . \substr(\strip_tags($title), 0, 30);
                break;
            case "<h9":
                $array["level"] = 9;
                $array["data"]= "<h9>" . \substr(\strip_tags($title), 0, 30);
                break;
        }

        return $array;
    }

    private function validateHeadlinesLevel($data){
        $last = null;
        for($i = 0; $i < count($data); $i++){
            $element = $data[$i];
            if($last === null){
                $element["status"] = true;
                $data[$i] = $element;
            }else{
                if($element["level"] == $last + 1 || $element["level"] <= $last){
                    $element["status"] = true;
                }else{
                    $element["status"] = false;
                }
                $data[$i] = $element;
            }
            $last = $element["level"];
        }
        return $data;
    }


    private function findNestedTables(){

        $dom = $this->htmlDomBody->find('table');
        foreach ($dom as $child){
            $child = $child->innertext;
            $child = \str_get_html($child)->find('table');

            if(\count($child) > 0){
                return true;
            }
        }
        return false;
    }

    private function findAlternateContent(){
        $dom = $this->htmlDomBody->find('img');

        foreach ($dom as $child){
            if($child->alt === false){
                return false;
            }
        }

        return true;
    }

    private function countLinks(){
        $array = array();

        $dom = $this->htmlDomBody->find('a');

        $countExt = 0;
        $countAll = \count($dom);

        foreach ($dom as $child){
            if(substr($child->href, 0, 4) === "http"){
                $countExt++;
            }
        }
        $array["external"] = $countExt;
        $array["internal"] = $countAll - $countExt;

        return $array;
    }

    private function textSize(){
        return \strlen(\strip_tags($this->htmlSource)) / 1000;
    }

}
?>

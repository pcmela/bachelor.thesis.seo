<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FindKeywordsModel
 *
 * @author wossa
 */
namespace AdminModule\SeoModule\KeywordsModule;

require_once LIBS_DIR . "/Ext/simple_html_dom.php";

class FindKeywordsModel extends \BaseModel{

    function __construct() {
        parent::__construct();
    }

    /**
     *
     * @param string $word
     * @return array
     */
    public function findSimiliarWords($word){
        $data = array();
        $curl = new \Components\Seo\Curl\cURL();
        $source = $curl->get("http://search.seznam.cz/stats?collocation=". \urlencode($word) . "&submit=Vyhledat+Seznamem");
        $dom = \str_get_html($source);

        if($dom){
            $dom = $dom->find("span[class=collocation]");
            if($dom){
                foreach ($dom as $row){
                    $data[] = $row->plaintext;
                }
            }
        }

        return $data;
    }

}
?>

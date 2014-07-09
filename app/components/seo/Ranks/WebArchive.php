<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WebArchive
 *
 * @author wossa
 */
namespace Components\Seo\Ranks;

class WebArchive extends \Nette\Object{

    private $time;
    private $url = 'http://web.archive.org/web/*/';

    public function checkTime($web){
        $web = \str_replace("http://", "", $web);
        $this->url = $this->url . $web;

        $content = \file_get_html($this->url);

        if($content !== ''){
            $content = $content->find('tr[bgcolor=#EBEBEB]', 0)->innertext;
            if($content !== null){
                $content = str_get_html($content);
                $content = $content->find('a', 0)->plaintext;
                $date = new \DateTime($content);
                $date = $date->format('Y-m-d H:i:s');
                $date2 = new \DateTime();
                $date2 = $date2->format('Y-m-d H:i:s');


                $date = (((strtotime($date2) - strtotime($date))/(60*60*24*30)));
                
                $this->time = \round($date);
            }else{
                $this->time = null;
            }
        }else{
            $this->time = null;
        }

    }


    public function getTime() {
        return $this->time;
    }


}
?>

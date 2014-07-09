<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CheckPresenter
 *
 * @author wossa
 */
namespace FrontModule\SeoModule\RanksModule;

require LIBS_DIR . '/Ext/simple_html_dom.php';

use Nette\Forms\Form;

class CheckPresenter extends \BasePresenter{

    /**
     *
     * @var int
     */
    protected $popularitySeznam;
    /**
     *
     * @var int
     */
    protected  $popularityGoogle;
    /**
     *
     * @var int
     */
    protected $popularityBing;

    /**
     *
     * @var int
     */
    protected  $backlinks;

    /**
     *
     * @var int
     */
    protected $indexPagesSeznam;
    /**
     *
     * @var int
     */
    protected $indexPagesGoogle;
    /**
     *
     * @var int
     */
    protected $indexPagesBing;

    /**
     *
     * @var date
     */
    protected $age;

    /**
     *
     * @var int
     */
    protected $alexaRank;

    /**
     *
     * @var int
     */
    protected $pageRank;
    /**
     *
     * @var int
     */
    protected $sRank;

    /**
     *
     * @var int
     */
    protected $wikiBackLinks;

    /**
     *
     * @var Seznam
     */
    protected $seznam;
    /**
     *
     * @var Google
     */
    protected $google;
    /**
     *
     * @var Bing
     */
    protected $bing;
    /**
     *
     * @var Yahoo
     */
    protected $yahoo;

    /**
     *
     * @var Alexa2
     */
    protected $alexa;

    /**
     *
     * @var WebArchive
     */
    protected $webArchive;

    /**
     *
     * @var boolean
     */
    protected $submit = false;

    protected function createComponentWebForm($name){
        $form = new \Nette\Application\AppForm($this, $name);

        $form->addText("url", "Adresa webu:")
                ->addRule(Form::FILLED, "Adresa webu musí být vyplněna")
                ->addRule(Form::URL, "Neplatná adresa stránky");

        $form->addSubmit("submit", "Odeslat");

        $form->onSubmit[] = \callback($this, "webFormSubmitted");

        return $form;
    }

    public function webFormSubmitted(\Nette\Application\AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $this->submit = true;
                $url = $form->values["url"];

                $this->seznam = new \Components\Seo\Ranks\Seznam($url);
                $this->google = new \Components\Seo\Ranks\Google($url);
                $this->bing = new \Components\Seo\Ranks\Bing($url);
                $this->yahoo = new \Components\Seo\Ranks\Yahoo($url);

                $this->alexa = new \Components\Seo\Ranks\Alexa2($url);


//                \Nette\Debug::timer();
                $this->seznam->checkIndexPages();
//                \dump(\Nette\Debug::timer() . "SeznamIndex");
//                \Nette\Debug::timer();
                $this->seznam->checkPopularitySite();
//                \dump(\Nette\Debug::timer() . "SeznamPop");
//                \Nette\Debug::timer();
                $this->seznam->checkSRank();
//                \dump(\Nette\Debug::timer() . " srank");

//                \Nette\Debug::timer();
                $this->google->checkIndexPages();
//                \dump(\Nette\Debug::timer() . " google ind");
//                \Nette\Debug::timer();
                $this->google->checkPopularitySite();
//                \dump(\Nette\Debug::timer() . " google pop");
//                \Nette\Debug::timer();
                $this->google->checkRank();
//                \dump(\Nette\Debug::timer() . " google rank");
//                \Nette\Debug::timer();
                $this->google->checkIndexPagesWiki();
//                \dump(\Nette\Debug::timer() . " google wiki back");

//                \Nette\Debug::timer();
                $this->bing->checkIndexPages();
//                \dump(\Nette\Debug::timer() . " bing index");
//                \Nette\Debug::timer();
                $this->bing->checkPopularitySite();
//                \dump(\Nette\Debug::timer() . " bing pop");

//                \Nette\Debug::timer();
                $this->yahoo->checkYahooSiteExplorer();
//                \dump(\Nette\Debug::timer() . " yahoo back");

//                \Nette\Debug::timer();
                $this->alexa->checkAlexaRank();
//                \dump(\Nette\Debug::timer() . " alexa rank");


//                \Nette\Debug::timer();
                $this->webArchive = new \Components\Seo\Ranks\WebArchive();
                $this->webArchive->checkTime($url);
//                \dump(\Nette\Debug::timer() . " webArchive");
                $this->age = $this->webArchive->getTime();
            }
        }else{
            return $form;
        }
    }

    public function renderDefault(){
        $this->template->isLogged = $this->isLogged();
        if($this->submit){
            $this->template->submit = $this->submit;

            $this->template->seznamPopularity = $this->seznam->getPopulatitySite();
            $this->template->googlePopularity = $this->google->getPopulatitySite();
            $this->template->bingPopularity = $this->bing->getPopulatitySite();

            $this->template->seznamIndexPages = $this->seznam->getIndexPages();
            $this->template->googleIndexPages = $this->google->getIndexPages();
            $this->template->bingIndexPages = $this->bing->getIndexPages();

            $this->template->backlinks = $this->yahoo->getBacklinks();
            $this->template->alexaRank = $this->alexa->getAlexaRank();

            if($this->seznam->getSRank() != -1){
                $this->template->sRank = $this->seznam->getSRank().'/100';
            }else{
                $this->template->sRank = "Data nejsou k dispozici";
            }
            $this->template->pageRank = $this->google->getPageRank();

            $this->template->wikiBackLinks = $this->google->getWiki();
            $this->template->age = $this->age;
        }else{
            $this->template->submit = $this->submit;
        }
    }
}
?>

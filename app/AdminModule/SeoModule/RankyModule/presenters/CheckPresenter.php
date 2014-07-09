<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HomepagePresenter
 *
 * @author WoSSa
 */

namespace AdminModule\SeoModule\RankyModule;

require_once LIBS_DIR . "/Ext/simple_html_dom.php";
require_once(LIBS_DIR.'/jpgraph/src/jpgraph.php');
require_once(LIBS_DIR.'/jpgraph/src/jpgraph_line.php');

use Nette\Application\AppForm, \Nette\Forms\Form;

class CheckPresenter extends \AdminModule\DefaultPresenter {

    /**
     *
     * @var int
     */
    protected $popularitySeznam;
    /**
     *
     * @var int
     */
    protected $popularityGoogle;
    /**
     *
     * @var int
     */
    protected $popularityBing;
    /**
     *
     * @var int
     */
    protected $backlinks;
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
    protected $submit = null;
   

    protected function createComponentMyWebGrid($name) {
        $grid = new RanksGrid($this, $name);

        $model = new \AdminModule\SeoModule\DibiHomepageSeoGridModel();
        $model->setMyWebsFluent($this->userIdentity->id);
        $grid->setModel($model);

        $grid->addColumn("es_web_url", "WWW stánka");

        $pres = $this;
        $grid->addButton("check", "Zkontrolovat", array(
                "link" => function ($row) use($pres) {
                    return $pres->link("check!", $row->es_web_id, $row->es_web_url);
                },
                "visible" => function ($row){
                    $result = \AdminModule\models\database\Ranks::checkRanks($row->es_web_id);
                    if($result){
                        $dateNow = new \DateTime();
                        $dateNow = $dateNow->format('Y-m-d H:i:s');
                        $date = $result->es_ranks_date;
                        if((\strtotime($dateNow) - (\strtotime($date))) / (60*60*24) > 1){
                            return true;
                        }
                        return false;
                    }else{
                        return true;
                    }
                }));
        $grid->addButton("detail", "Detail", array(
            "link" => function ($row) use($pres) {
                return $pres->link("detail", $row->es_web_id);
            },
            "visible" => function ($row){
                $result = \AdminModule\models\database\Ranks::checkRanks($row->es_web_id);
                if($result){
                    return true;
                }else{
                    return false;
                }
            }
        ));

        $grid->addButton("overview", "Přehled", array(
            "link" => function ($row) use($pres) {
                return $pres->link("overview", $row->es_web_id);
            },
            "visible" => function ($row){
                $result = \AdminModule\models\database\Ranks::checkRanks($row->es_web_id);
                if($result){
                    return true;
                }else{
                    return false;
                }
            }
        ));

        return $grid;
    }

    public function actionDetail($id, $url){
        try{
            $this->submit = \AdminModule\models\database\Ranks::fetchRanks($id);
        }catch(\DibiDriverException $exception){
            $this->flashMessage("Chyba serveru, opakujte požadavek později.");
            $this->redirect("this");
        }
    }

    public function handleCheck($id, $url) {
        $this->process($id, $url);

        $this->flashMessage("Ranky pro dému $url byly zkontrolovány a uloženy");
        $this->redirect("this");
    }

    private function  process($id, $url) {
        $seznam = array();
        $google = array();
        $bing = array();

        $backlinks = null;
        $alexa = null;

        $wiki = null;
        $archive = null;

        $this->initObjects($url);
        $this->checkProperties();


        $seznam["popularity"] = $this->seznam->getPopulatitySite();
        $google["popularity"] = $this->google->getPopulatitySite();
        $bing["popularity"] = $this->bing->getPopulatitySite();

        $seznam["indexPages"] = $this->seznam->getIndexPages();
        $google["indexPages"] = $this->google->getIndexPages();
        $bing["indexPages"] = $this->bing->getIndexPages();

        $backlinks = $this->yahoo->getBacklinks();
        $alexa = $this->alexa->getAlexaRank();
        $alexa = \intval($alexa);

        $seznam["sRank"] = $this->seznam->getSRank();
        $google["pageRank"] = $this->google->getPageRank();

        $wiki = $this->google->getWiki();
        $archive = $this->age;

        \AdminModule\models\database\Ranks::insertRanks($id, $seznam, $google, $bing, $backlinks, $alexa, $wiki, $archive);
    }

    private function initObjects($url){
        $this->seznam = new \Components\Seo\Ranks\Seznam($url);
        $this->google = new \Components\Seo\Ranks\Google($url);
        $this->bing = new \Components\Seo\Ranks\Bing($url);
        $this->yahoo = new \Components\Seo\Ranks\Yahoo($url);
        $this->alexa = new \Components\Seo\Ranks\Alexa2($url);
    }

    private function checkProperties(){
        $this->seznam->checkIndexPages();
        $this->seznam->checkPopularitySite();
        $this->seznam->checkSRank();
        $this->google->checkIndexPages();
        $this->google->checkPopularitySite();
        $this->google->checkRank();
        $this->google->checkIndexPagesWiki();
        $this->bing->checkIndexPages();
        $this->bing->checkPopularitySite();
        $this->yahoo->checkYahooSiteExplorer();
        $this->alexa->checkAlexaRank();
        //$this->webArchive = new \Components\Seo\Ranks\WebArchive();
        //$this->webArchive->checkTime($url);
        $this->age = /*$this->webArchive->getTime()*/1;
    }

    public function renderGraph($id, $type){
        switch($type){
            case 1:
                $results = \AdminModule\models\database\Ranks::fetchPageRank($id);
                $resultsArchive = \AdminModule\models\database\Ranks::fetchPageRankArchive($id);
                $this->printGrahp($this->createArrayPageRank($results, $resultsArchive));
                break;
            case 2:
                $results = \AdminModule\models\database\Ranks::fetchSeznamRank($id);
                $resultsArchive = \AdminModule\models\database\Ranks::fetchSeznamRankArchive($id);
                $this->printGrahp($this->createArraySeznamRank($results, $resultsArchive));
                break;
            case 3:
                $results = \AdminModule\models\database\Ranks::fetchAlexa($id);
                $resultsArchive = \AdminModule\models\database\Ranks::fetchAlexaArchive($id);
                $this->printGrahp($this->createArrayAlexa($results, $resultsArchive));
                break;
            case 4:
                $results = \AdminModule\models\database\Ranks::fetchBacklinks($id);
                $resultsArchive = \AdminModule\models\database\Ranks::fetchBacklinksArchive($id);
                $this->printGrahp($this->createArrayBacklinks($results, $resultsArchive));
                break;
            case 5:
                $results = \AdminModule\models\database\Ranks::fetchEnginesPop($id);
                $resultsArchive = \AdminModule\models\database\Ranks::fetchEnginesPopArchive($id);
                $this->printGrahpGroup($this->createArrayEnginesPop($results, $resultsArchive));
                break;
            case 6:
                $results = \AdminModule\models\database\Ranks::fetchEnginesInd($id);
                $resultsArchive = \AdminModule\models\database\Ranks::fetchEnginesIndArchive($id);
                $this->printGrahpGroup($this->createArrayEnginesInd($results, $resultsArchive));
                break;
            case 7:
                $results = \AdminModule\models\database\Ranks::fetchBacklinksWiki($id);
                $resultsArchive = \AdminModule\models\database\Ranks::fetchBacklinksWikiArchive($id);
                $this->printGrahp($this->createArrayWiki($results, $resultsArchive));
                break;
        }
    }

    private function createArrayPageRank($results, $resultsArchive){
        $arrayResults = array();
        $arrayDates = array();
        $arrayResults[] = $results->es_ranks_google_pagerank;
        $arrayDates[] = $results->es_ranks_date;

        foreach ($resultsArchive as $row){
            $arrayResults[] = $row->es_ranks_archive_google_pagerank;
            $arrayDates[] = $row->es_ranks_archive_date;
        }

        $data["results"] = $arrayResults;
        $data["dates"] = $arrayDates;

        return $data;
    }

    private function createArraySeznamRank($results, $resultsArchive){
        $arrayResults = array();
        $arrayDates = array();
        if($results->es_ranks_seznam_rank != -1){
            $arrayResults[] = $results->es_ranks_seznam_rank;
        }else{
            $arrayResults[] = null;
        }
        $arrayDates[] = $results->es_ranks_date;

        foreach ($resultsArchive as $row){
            if($row->es_ranks_archive_seznam_rank != -1){
                $arrayResults[] = $row->es_ranks_archive_seznam_rank;
            }else{
                $arrayResults[] = null;
            }
            $arrayDates[] = $row->es_ranks_archive_date;
        }

        $data["results"] = $arrayResults;
        $data["dates"] = $arrayDates;

        return $data;
    }

    private function createArrayAlexa($results, $resultsArchive){
        $arrayResults = array();
        $arrayDates = array();
        $arrayResults[] = $results->es_ranks_alexa;
        $arrayDates[] = $results->es_ranks_date;

        foreach ($resultsArchive as $row){
            $arrayResults[] = $row->es_ranks_archive_alexa;
            $arrayDates[] = $row->es_ranks_archive_date;
        }

        $data["results"] = $arrayResults;
        $data["dates"] = $arrayDates;

        return $data;
    }

    private function createArrayBacklinks($results, $resultsArchive){
        $arrayResults = array();
        $arrayDates = array();
        $arrayResults[] = $results->es_ranks_backlinks;
        $arrayDates[] = $results->es_ranks_date;

        foreach ($resultsArchive as $row){
            $arrayResults[] = $row->es_ranks_archive_backlinks;
            $arrayDates[] = $row->es_ranks_archive_date;
        }

        $data["results"] = $arrayResults;
        $data["dates"] = $arrayDates;

        return $data;
    }

    private function createArrayEnginesPop($results, $resultsArchive){
        $arraySeznamResults = array();
        $arrayGoogleResults = array();
        $arrayBingResults = array();

        $arrayDates = array();

        $arraySeznamResults[] = $results->es_ranks_pop_seznam / 1000;
        $arrayGoogleResults[] = $results->es_ranks_pop_google / 1000;
        $arrayBingResults[] = $results->es_ranks_pop_bing / 1000;
        $arrayDates[] = $results->es_ranks_date;

        foreach ($resultsArchive as $row){
            $arraySeznamResults[] = $row->es_ranks_archive_pop_seznam / 1000;
            $arrayGoogleResults[] = $row->es_ranks_archive_pop_google / 1000;
            $arrayBingResults[] = $row->es_ranks_archive_pop_bing / 1000;
            $arrayDates[] = $row->es_ranks_archive_date;
        }

        $data["seznam"] = $arraySeznamResults;
        $data["google"] = $arrayGoogleResults;
        $data["bing"] = $arrayBingResults;
        $data["dates"] = $arrayDates;

        return $data;
    }

    private function createArrayEnginesInd($results, $resultsArchive){
        $arraySeznamResults = array();
        $arrayGoogleResults = array();
        $arrayBingResults = array();

        $arrayDates = array();

        $arraySeznamResults[] = $results->es_ranks_ind_seznam / 1000;
        $arrayGoogleResults[] = $results->es_ranks_ind_google / 1000;
        $arrayBingResults[] = $results->es_ranks_ind_bing / 1000;
        $arrayDates[] = $results->es_ranks_date;

        foreach ($resultsArchive as $row){
            $arraySeznamResults[] = $row->es_ranks_archive_ind_seznam / 1000;
            $arrayGoogleResults[] = $row->es_ranks_archive_ind_google / 1000;
            $arrayBingResults[] = $row->es_ranks_archive_ind_bing / 1000;
            $arrayDates[] = $row->es_ranks_archive_date;
        }

        $data["seznam"] = $arraySeznamResults;
        $data["google"] = $arrayGoogleResults;
        $data["bing"] = $arrayBingResults;
        $data["dates"] = $arrayDates;

        return $data;
    }

    private function createArrayWiki($results, $resultsArchive){
        $arrayResults = array();
        $arrayDates = array();
        $arrayResults[] = $results->es_ranks_wiki_backlinks;
        $arrayDates[] = $results->es_ranks_date;

        foreach ($resultsArchive as $row){
            $arrayResults[] = $row->es_ranks_archive_wiki_backlinks;
            $arrayDates[] = $row->es_ranks_archive_date;
        }

        $data["results"] = $arrayResults;
        $data["dates"] = $arrayDates;

        return $data;
    }

    private function printGrahp($data) {

        $graph = new \Graph(862, 350);
        $graph->SetScale("textlin");

        $theme_class = new \UniversalTheme;
        $graph->SetTheme($theme_class);

        $graph->title->Set($name);
        $graph->SetBox(false);

        $graph->yaxis->HideZeroLabel();

        $graph->xaxis->SetTickLabels($data["dates"]);
        $graph->ygrid->SetFill(false);

        if (\is_array($data)) {
            $p1 = new \LinePlot($data["results"]);
        } else {
            $p1 = new \LinePlot(array(null));
        }
        $graph->Add($p1);

        $p1->SetColor("#55bbdd");
        $p1->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
        $p1->mark->SetColor('#55bbdd');
        $p1->mark->SetFillColor('#55bbdd');
        $p1->SetCenter();

        $graph->Stroke();
        exit;
    }

    private function printGrahpGroup($data) {

        //\dump($data);
        $graph = new \Graph(862, 350);
        $graph->SetScale("textlin");

        $theme_class = new \UniversalTheme;
        $graph->SetTheme($theme_class);

        $graph->title->Set($name);
        $graph->SetBox(false);

        $graph->yaxis->HideZeroLabel();

        $graph->xaxis->SetTickLabels($data["dates"]);
        $graph->ygrid->SetFill(false);


        $p1 = new \LinePlot($data["seznam"]);
        $graph->Add($p1);
        $p1->SetColor("#6495ED");
        $p1->SetLegend('Seznam.cz');

        // Create the second line
        $p2 = new \LinePlot($data["google"]);
        $graph->Add($p2);
        $p2->SetColor("#B22222");
        $p2->SetLegend('Google.cz');

        // Create the third line
        $p3 = new \LinePlot($data["bing"]);
        $graph->Add($p3);
        $p3->SetColor("#FF1493");
        $p3->SetLegend('Bing.com');

        $p1->SetColor("#55bbdd");
        $p1->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
        $p1->mark->SetColor('#55bbdd');
        $p1->mark->SetFillColor('#55bbdd');
        $p1->SetCenter();

        $p2->SetColor("#B22222");
        $p2->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
        $p2->mark->SetColor('#B22222');
        $p2->mark->SetFillColor('#B22222');
        $p2->SetCenter();

        $p3->SetColor("#FF1493");
        $p3->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
        $p3->mark->SetColor('#FF1493');
        $p3->mark->SetFillColor('#FF1493');
        $p3->SetCenter();

        $graph->Stroke();
        exit;
    }

    public function renderDefault() {
        if ($this->submit !== null) {
            $this->template->submit = $this->submit;

            $this->template->seznamPopularity = "Data nejsou k dispozici";
            $this->template->googlePopularity = $this->submit->es_ranks_pop_google;
            $this->template->bingPopularity = $this->submit->es_ranks_pop_bing;

            $this->template->seznamIndexPages = $this->submit->es_ranks_ind_seznam;
            $this->template->googleIndexPages = $this->submit->es_ranks_ind_google;
            $this->template->bingIndexPages = $this->submit->es_ranks_ind_bing;

            $this->template->backlinks = $this->submit->es_ranks_backlinks;
            $this->template->alexaRank = $this->submit->es_ranks_alexa;

            if($this->submit->es_ranks_seznam_rank == -1){
                $this->template->sRank = "Data nejsou k dispozici";
            }else{
                $this->template->sRank = $this->submit->es_ranks_seznam_rank;
            }
            $this->template->pageRank = $this->submit->es_ranks_google_pagerank;

            $this->template->wikiBackLinks = $this->submit->es_ranks_wiki_backlinks;

        } else {
            $this->template->submit = $this->submit;
        }
    }

    public function renderDetail(){
        if ($this->submit !== null) {
            $this->template->submit = $this->submit;

            $this->template->seznamPopularity = "Data nejsou k dispozici";
            $this->template->googlePopularity = $this->submit->es_ranks_pop_google;
            $this->template->bingPopularity = $this->submit->es_ranks_pop_bing;

            $this->template->seznamIndexPages = $this->submit->es_ranks_ind_seznam;
            $this->template->googleIndexPages = $this->submit->es_ranks_ind_google;
            $this->template->bingIndexPages = $this->submit->es_ranks_ind_bing;

            $this->template->backlinks = $this->submit->es_ranks_backlinks;
            $this->template->alexaRank = $this->submit->es_ranks_alexa;

            if($this->submit->es_ranks_seznam_rank == -1){
                $this->template->sRank = "Data nejsou k dispozici";
            }else{
                $this->template->sRank = $this->submit->es_ranks_seznam_rank;
            }
            $this->template->pageRank = $this->submit->es_ranks_google_pagerank;

            $this->template->wikiBackLinks = $this->submit->es_ranks_wiki_backlinks;

        } else {
            $this->template->submit = false;
        }
    }

    public function renderOverview($id){
        $this->setLayout("layoutOverview");
        $this->template->id = $id;
    }

}

?>

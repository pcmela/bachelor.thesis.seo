<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OverAllPresenter
 *
 * @author WoSSa
 */
namespace AdminModule\SeoModule\KeywordsModule;

class OverAllPresenter extends \AdminModule\DefaultPresenter{

    private $id;
    private $web_domain;
    private $stackWords = array();
    private $arraySum = array();
    private $engines;
    private $engineSeznam;
    private $engineGoogle;


    public function actionDefault($id){
        if($id == null){
            $this->redirect(":Admin:Seo:Homepage:default");
        }
        $this->id = $id;
        $this->web_domain = \AdminModule\models\database\Web::getDomain($this->id);
        $this->web_domain = $this->web_domain->es_web_url;
    }

    public function renderDefault(){
        $this->setLayout("layoutOverview" );
        $all = array();

        $this->checkPermission($this->userIdentity->id, $this->id);

        $all[] = $this->countPoints($this->web_domain);
        $concurrency = \AdminModule\models\database\Concurrency::get($this->id);
        foreach ($concurrency as $row){
            $this->stackWords = array();
            $this->arraySum = array();
            $all[] = $this->countPoints($row->es_concurrency_domain);

        }

        $cache = \Nette\Environment::getCache();
        $rand = \rand(1, 9999999999);
        $cache->save($rand, $all, array(
           'expire' => \time() + 60 * 1,
            'sliding' => true
        ));

        $this->template->cacheKey = $rand;
        $this->template->id = $this->id;
    }

    private function checkPermission($userId, $webId){
            if(!\AdminModule\models\database\User::getWeb($userId, $webId)){
                $perm = \AdminModule\models\database\User::getPermissionWeb($userId, $webId);
                if($perm){
                    if($perm->permission == 1){
                        $this->template->editPermission = true;
                    }else{
                        $this->template->editPermission = null;
                    }
                }else{
                    $this->template->editPermission = null;
                }
            }else{
                $this->template->editPermission = true;
            }
        }   

    private function countPoints($web_domain){
        $web = new Web();
        $this->process($web_domain);
        $this->sumPosition();
        $web->setSumSeznam($this->makeSumSeznam());
        $web->setSumGoogle($this->makeSumGoogle());
        $web->setSumSeznamGoogle($this->makeSumSeznamGoogle());
        $web->setSumAll($this->makeSumOther());
        $web->setName($web_domain);
        return $web;
    }


    private function process($web){
        $graphModel = new GraphModel();
        $this->engines = \AdminModule\models\SearchEngine::getAll();
        $words = \AdminModule\models\database\Web::getAllWord($this->id);


        //foreach ($words as $word){
            $data = $graphModel->getEngineResultsArchiveOverAllv2($this->id, $web);
            $dataLastUpdate = \AdminModule\models\database\Test::lastTest($this->id, $web);

            // read data and make array with objects

            $stackTests = $this->makeObjectArray($data, $dataLastUpdate);

            foreach ($stackTests as $test){
                $this->stackWords = $this->checkAlreadyWord($this->stackWords, $test);
                $var = $this->stackWords[$test->getWordId()];
                $var = $this->checkAlreadyDate($var, $test);
                $var[$test->getDate()][$test->getEngine()] = (101 - $test->getPosition()) * $test->getWeight();
                $this->stackWords[$test->getWordId()] = $var;
            }            
        //}
    }


    private function makeSumSeznam(){
        $this->getSpecificEngines();
        $seznamArray = array();

        foreach ($this->arraySum as $date => $arrayEngines){
            $seznamArray = $this->checkAlreadyDateFromArray($seznamArray, $date);
            foreach ($arrayEngines as $engineId => $position){
                if($engineId == $this->engineSeznam){
                    $seznamArray[$date] += $position;
                }
            }
        }
        return $seznamArray;
    }

    private function makeSumGoogle(){
        $this->getSpecificEngines();
        $seznamArray = array();

        foreach ($this->arraySum as $date => $arrayEngines){
            $seznamArray = $this->checkAlreadyDateFromArray($seznamArray, $date);
            foreach ($arrayEngines as $engineId => $position){
                if($engineId == $this->engineGoogle){
                    $seznamArray[$date] += $position;
                }
            }
        }
        return $seznamArray;
    }

    private function makeSumOther(){
        $this->getSpecificEngines();
        $seznamArray = array();

        foreach ($this->arraySum as $date => $arrayEngines){
            $seznamArray = $this->checkAlreadyDateFromArray($seznamArray, $date);
            foreach ($arrayEngines as $engineId => $position){
                //if($engineId !== $this->engineGoogle && $engineId !== $this->engineSeznam){
                    $seznamArray[$date] += $position;
                //}
            }
        }
        return $seznamArray;
    }

    private function makeSumSeznamGoogle(){
        $this->getSpecificEngines();
        $seznamArray = array();

        foreach ($this->arraySum as $date => $arrayEngines){
            $seznamArray = $this->checkAlreadyDateFromArray($seznamArray, $date);
            foreach ($arrayEngines as $engineId => $position){
                if($engineId == $this->engineGoogle || $engineId == $this->engineSeznam){
                    $seznamArray[$date] += $position;
                }
            }
        }
        return $seznamArray;
    }

    private function getSpecificEngines(){
        if($this->engineGoogle === null && $this->engineSeznam === null){
            foreach ($this->engines as $engine){
                if($engine->es_search_engine_name === "seznam.cz"){
                    $this->engineSeznam = $engine->es_search_engine_id;
                }else if($engine->es_search_engine_name === "google.cz"){
                    $this->engineGoogle = $engine->es_search_engine_id;
                }
            }
        }
    }

    private function checkAlreadyDateFromArray($var, $date){
        if (!isset($var[$date])) {
            $var[$date] = 0;
        }
        return $var;
    }

    private function checkAlreadyDate($var, $test) {
        // kontrola zda datum je jiz pouzito
        if (!isset($var[$test->getDate()])) {

            // vytvoreni pole s jednotlivymi vyhledavaci
            $arrEngine = array();

            foreach ($this->engines as $engine) {
                $arrEngine[$engine->es_search_engine_id] = 0;
            }

            $var[$test->getDate()] = $arrEngine;
        }
        return $var;
    }

    private function checkAlreadyWord($stackWords, $test) {
        // kontrola jestli jiz dane slovo bylo pouzito
        if (!isset($stackWords[$test->getWordId()])) {
            //inicializace pole
            $stackWords[$test->getWordId()] = array();
        }
        return $stackWords;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    private function makeObjectArray($data, $dataLastUpdate) {
        $stackTests = array();
        
        foreach ($data as $row) {
            $test = new Test();
            $test->setDate($row->es_archive_date);
            $test->setEngine($row->es_archive_search_engine_id);
            $test->setPosition($row->position);
            $test->setWordId($row->word_id);
            $test->setWeight($row->weight / 10);

            $stackTests[] = $test;
        }

       

        foreach ($dataLastUpdate as $row){
            $test = new Test();
            $test->setDate($row->es_test_date);
            $test->setEngine($row->es_test_search_engine_id);
            $test->setPosition($row->es_test_position);
            $test->setWordId($row->word_id);
            $test->setWeight($row->weight / 10);

            $stackTests[] = $test;
        }

        return $stackTests;
    }

    private function sumPosition(){
        foreach ($this->stackWords as $wordId => $word){
        //    dump($wordId);
            foreach ($word as $date => $result_engines){
        //        dump($date);
                if(!isset ($this->arraySum[$date])){
                    $this->arraySum[$date] = array();

                    foreach ($this->engines as $row){
                        $this->arraySum[$date][$row->es_search_engine_id] = 0;
                    }
                }

                foreach ($result_engines as $engineId => $position){
                    $this->arraySum[$date][$engineId] += $position;
                }
            }
        }
    }
}


class Test{
    private $position;
    private $date;
    private $engine;
    private $wordId;
    private $weight;

    public function getWeight() {
        return $this->weight;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    
    public function getWordId() {
        return $this->wordId;
    }

    public function setWordId($wordId) {
        $this->wordId = $wordId;
    }


    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getEngine() {
        return $this->engine;
    }

    public function setEngine($engine) {
        $this->engine = $engine;
    }
}

class Web{
    private $sumSeznam;
    private $sumGoogle;
    private $sumAll;
    private $sumSeznamGoogle;
    private $name;

    public function getSumSeznamGoogle() {
        return $this->sumSeznamGoogle;
    }

    public function setSumSeznamGoogle($sumSeznamGoogle) {
        $this->sumSeznamGoogle = $sumSeznamGoogle;
    }

    public function getSumSeznam() {
        return $this->sumSeznam;
    }

    public function setSumSeznam($sumSeznam) {
        $this->sumSeznam = $sumSeznam;
    }

    public function getSumGoogle() {
        return $this->sumGoogle;
    }

    public function setSumGoogle($sumGoogle) {
        $this->sumGoogle = $sumGoogle;
    }

    public function getSumAll() {
        return $this->sumAll;
    }

    public function setSumAll($sumAll) {
        $this->sumAll = $sumAll;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }


}
?>

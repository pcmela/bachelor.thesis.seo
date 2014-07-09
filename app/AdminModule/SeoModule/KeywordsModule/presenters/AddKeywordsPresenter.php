<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AddKeywordsPresenter
 *
 * @author WoSSa
 */
namespace AdminModule\SeoModule\KeywordsModule;

//require_once LIBS_DIR."/Ext/simple_html_dom.php";


use Nette\Environment, Nette\Application\AppForm, Nette\Forms\Form;

class AddKeywordsPresenter extends \AdminModule\DefaultPresenter {

    private $id;
    private $concurrency;

    private $graphModel;

    private $error;


    private $url = array();

    private $modelFetchWords;

    private $findWordsModel;
    private $similiarWords;
    private $words;
    private $concurrencyModel;

    private $keywords;

    private $search;

    public function startup() {
        parent::startup();
    }

    /**
     *
     * @return ConcurrencyModel
     */
    private function getConcurrencyModel(){
        if($this->concurrencyModel === null){
            $this->concurrencyModel = new ConcurrencyModel();
        }

        return $this->concurrencyModel;
    }


    /**
     *
     * @return GraphModel
     */
    private function getGraphModel(){
        if($this->graphModel === null){
            $this->graphModel = new GraphModel();
        }
        return $this->graphModel;
    }

    /**
     *
     * @return FetchWordModel
     */
    private function getFetchWords(){
        if($this->modelFetchWords === null){
            $this->modelFetchWords = new FetchWordModel();
        }

        return $this->modelFetchWords;
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

    private function checkPermissionView($userId, $webId){
        if(!\AdminModule\models\database\User::getWeb($userId, $webId)){
            $perm = \AdminModule\models\database\User::getPermissionWeb($userId, $webId);
            if($perm){
                if($perm->permission == 1 || $perm->permission == 2){
                    $this->template->editPermission = true;
                    $this->template->role = $perm->permission;
                }else{
                    $this->template->editPermission = null;
                }
            }else{
                $this->template->editPermission = null;
            }
        }else{
            $this->template->role = 1;
            $this->template->editPermission = true;
        }
    }

    public function createComponentSearchKw($name) {
        $form = new \Nette\Application\AppForm($this, $name);
        $form->getElementPrototype()->name('form_keywords');

        $form->addTextArea("kw", "Klicove slovo", 80, 20)
                ->addRule(Form::FILLED, "Zadejte prosím nějaké klíčové slovo.");
        $form->addHidden("url")->setDefaultValue($this->id);

        $form->addSubmit("submit", "Odeslat");

        $form->onSubmit[] = \callback($this, "submitSearchKw");
    }

    public function submitSearchKw(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                \dump($this->id);
                $words = explode("\n", $form->values["kw"]);
                
                foreach($words as $key => $value){                    
                    if(\strlen(\trim($words[$key])) == 0){
                        unset($words[$key]);
                    }else{
                        $words[$key] = trim($words[$key]);
                    }
                }

                \Nette\Debug::log(\count($words));


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://".$_SERVER['HTTP_HOST'].$this->link(":Admin:Seo:Keywords:Background:default"));
                curl_setopt($ch, CURLOPT_POST, 1);



                curl_setopt($ch, CURLOPT_POSTFIELDS, "words=". serialize($words)."&seznamData=null&webId=".  \serialize($this->id));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'curl');

                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                $result = curl_exec($ch);
                curl_close($ch);

                $this->flashMessage("Kontrola vámi zadaných slov ve vyhledávačích se nyní provádí. Do 30ti minut budete mít k dispozici výsledky.");
                $this->redirect("this", $this->id);

            }
        }
    }

    public function handleUpdate($webId, $words) {
        $words = \explode(",", $words);
        $data = array();
        foreach ($words as $key => $word){
            if($word === ""){
                unset($words[$key]);
            }
        }
        foreach ($words as $word){
            $data[] = $word;
        }
        $words = $data;
        //\dump($words);
        //\dump($webId);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$_SERVER['HTTP_HOST'].$this->link(":Admin:Seo:Keywords:Background:default"));
        curl_setopt($ch, CURLOPT_POST, 1);



        curl_setopt($ch, CURLOPT_POSTFIELDS, "words=" . serialize($words) . "&seznamData=null&webId=" . \serialize($webId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');

        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->flashMessage("Aktualizace slov může trvat až několik minut, prosíme o strpení.");
        $this->redirect("this");
    }

    protected function createComponentGrid($name) {
        $grid = new MyKeywordGrid($this, $name);
        //$grid->

        $model = new DibiModelGrid($this->id);

        $search = $this->getParam("search", false);
        if ($search) {
            $model->filterSearch($search);
        }

        $grid->setModel($model);

        $grid->setItemsPerPage(15);


        $grid->addColumn("word", "Slovo")->setSortable(true);
        $grid->addColumn("position_seznam", "Seznam")->setSortable(true);
        $grid->addColumn("old_position_seznam", "")->setSortable(true);

        $grid->addColumn("position_google", "Google")->setSortable(true);
        $grid->addColumn("old_position_google", "")->setSortable(true);

        $grid->addColumn("position_bing", "Bing")->setSortable(true);
        $grid->addColumn("old_position_bing", "")->setSortable(true);

        $grid->addColumn("position_centrum", "Centrum")->setSortable(true);
        $grid->addColumn("old_position_centrum", "")->setSortable(true);

        $grid->addColumn("position_jyxo", "Jyxo")->setSortable(true);
        $grid->addColumn("old_position_jyxo", "")->setSortable(true);

        $pres = $this;
        $web = $this->id;
        $grid->addButton("detail", "detail")->setLink(function ($row) use($pres, $web) {
                    return $pres->link("detail", array($web, $row->word, $row->position_seznam, $row->position_google,
                        $row->position_bing, $row->position_centrum, $row->position_jyxo, $pres->getApplication()->storeRequest()));
                });

        $grid->addButton("delete", "X", array(
            "link" => function ($row) use($pres, $web) {
                    return $pres->link("deleteWord!", $row->word);
            },
            "confirmationQuestion" => function ($row) {
                return "Opravdu chcete vymazat klíčové slovo $row->word?";
            }
        ));


        return $grid;
    }



    protected function createComponentUpdateWords($name){
        $form = new \Nette\Application\AppForm($this, $name);

        $words = $this->getFetchWords()->fetchUpdatesWords($this->id);
        $wordsInput = null;
        foreach ($words as $word){
            //\dump($word);
            
            $lastDate = $word->es_word_last_test;
            $actualDate = new \DateTime();
            $actualDate = $actualDate->format('Y-m-d H:i:s');

            if($this->differenceTime($actualDate, $lastDate) > 1 ){
//                if($wordsInput !== null){
//                    $wordsInput .= ",";
//                }
                if($wordsInput === null){
                    $wordsInput = $word->es_word_word;
                }else{
                    $wordsInput .= ",".$word->es_word_word;
                }
            }
        }
        if($wordsInput !== null){
            $form->getElementPrototype()->name('form_update');

            $form->addHidden('words')
                        ->setDefaultValue($wordsInput);

            $form->addHidden("url")->setDefaultValue($this->id);

            $form->addSubmit('submit_update', 'Aktualizovat');
            $form->onSubmit[] = \callback($this, "submitUpdate");
        }

        
    }


    public function submitUpdate(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $words = \explode(",", $form->values["words"]);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://".$_SERVER['HTTP_HOST'].$this->link(":Admin:Seo:Keywords:Background:default"));
                curl_setopt($ch, CURLOPT_POST, 1);



                curl_setopt($ch, CURLOPT_POSTFIELDS, "words=". serialize($words)."&seznamData=null&webId=".  \serialize($form->values["url"]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'curl');

                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                $result = curl_exec($ch);
                curl_close($ch);

                $this->flashMessage("Aktualizace slov může trvat až několik minut, prosíme o strpení.");
                $this->redirect("this");
            }
        }
    }

    private function differenceTime($now, $last){
        return (\strtotime($now) - \strtotime($last))/(60*60*24);
    }

    protected function createComponentFilters($name) {
        $form = new \Nette\Application\AppForm($this, $name);
        $form->addText("search", "Vyhledat slovo")
                ->setDefaultValue($this->getParam("search", ""));
        $form->addSubmit("s", "Filter");
        $form->onSubmit[] = array($this, "filters_submit");
    }

    public function filters_submit($form) {
        $this->redirect("this", $form->getValues());
    }

    public function actionDefault($id) {
        $this->search = $this->getParam("search", false);
        $intId = \intval($id);
        if((int)$id === 0 || \strlen((string)$intId) < \strlen($id) ){
            preg_match('/([a-zA-Z0-9-]*\.)+[a-zA-Z]{2,4}/', $id, $result);
            //\dump($result[0]);
            if($result[0] !== null){
                $this->template->error = true;
            }else{
                $this->template->error = false;
            }
        }else{
            //\dump("true");
            $this->template->error = true;
        }
        $this->id = $id;
        $this->template->id = $id;
    }

    public function actionFind($id) {
        if($id == null){
            $this->redirect(":Admin:Seo:Homepage:default");
        }
        $this->id = $id;
        $this->template->id = $id;
    }

    public function actionDetail($idWeb, $word, $position_seznam, $position_google, $position_bing, $position_centrum, $position_jyxo, $restore) {
        $this->id = $idWeb;
        $this->template->id = $idWeb;
        $this->template->restore = $restore;
        if ($idWeb !== null && $word !== null) {
            $this->getDataToGraph($idWeb, $word, $position_seznam, $position_google, $position_bing, $position_centrum, $position_jyxo);
        }
    }
    
    private function getDataToGraph($idWeb, $word, $position_seznam, $position_google, $position_bing, $position_centrum, $position_jyxo){
        $engines = $this->getGraphModel()->getEngines();
        $webName = $this->getGraphModel()->getWebUrl($idWeb);
        //$lastUpdate = $this->getGraphModel()->getLastUpdateWord($word);

        $results = array();
        foreach ($engines as $engine){
            $eng = new Engine();
            $eng->setEngineId($engine->es_search_engine_id);
            $eng->setName($engine->es_search_engine_name);
            $eng->setWord($word);
            $eng->setWebName($webName);
            //$eng->setResults($this->getGraphModel()->getEngineResultsArchive($word, $engine->es_search_engine_id, $webName));
            switch($engine->es_search_engine_name){
                case "seznam.cz":
                    $eng->setLastPosition($position_seznam);
                    break;
                case "google.cz":
                    $eng->setLastPosition($position_google);
                    break;
                case "bing.com":
                    $eng->setLastPosition($position_bing);
                    break;
                case "centrum.cz":
                    $eng->setLastPosition($position_centrum);
                    break;
                case "jyxo.cz":
                    $eng->setLastPosition($position_jyxo);
                    break;
            }
            //$eng->setLastDate($lastUpdate);

            $results[] = $eng;
            //\dump($eng->getResults());
        }

        $this->url = $results;
    }


    public function handleRestore($key){
        $this->getApplication()->restoreRequest($key);
    }

    private function getConcurrency(){
        $this->template->currentConcurrency = $this->getConcurrencyModel()->currentConcurrency($this->id);
    }

    public function renderDefault($id) {
        
        $this->template->cacheGrid = 'grid'.$id;
        if(isset ($cache["web".$this->id])){
            $this->template->cachedUpdate = $cache["web".$this->id];
        }else{
            $this->template->cachedUpdate = true;
        }

        $this->getConcurrency();

        $this->setLayout("layoutOverview" );


        if($this->search){
            if(\strlen(\trim($this->search)) == 0){
                $this->template->cache = true;
            }else{
                $this->template->cache = false;
            }
        }else{
            $this->template->cache = true;
        }

        $this->checkPermissionView($this->userIdentity->id, $this->id);
        $this->template->filters = $this["filters"];
    }

    public function renderDetail() {
        
        $this->checkPermission($this->userIdentity->id, $this->id);
        $this->setLayout("layoutOverview" );
        if($this->url !== null){
            $this->template->data = true;
            //$this->template->graphs = $this->graphs;
            $this->template->url = $this->url;
        }else{
            $this->template->data = false;
            $this->template->message = "Omlouváme se, ale bohužel nebyla nalezena žádná data.";
        }
    }


    

    /**
     *
     * @return FindKeywordsModel
     */
    private function getFindWordsModel(){
        if(!$this->findWordsModel){
            $this->findWordsModel = new FindKeywordsModel();
            return $this->findWordsModel;
        }

        return $this->findWordsModel;
    }


    protected function createComponentKeywordsForm($name){
        $form = new AppForm($this, $name);

        $form->addText("word", "Slovo")
                ->addRule(Form::FILLED, "Napište slovo, ke kterému chcete najít podobná slova.");

        $form->addSubmit("submit", "Najít");

        $form->onSubmit[] = \callback($this, "submitKeywordsForm");

        return $form;
    }

    public function submitKeywordsForm(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $model = new FetchWordModel();
                $this->words = $model->getAllWordsFromUser($this->id);

                $this->similiarWords = $this->getFindWordsModel()->findSimiliarWords($form->values["word"]);
                foreach ($this->words as $word){
                    if(\in_array($word->es_word_word, $this->similiarWords)){
                        for($i = 0; $i < \count($this->similiarWords); $i++){
                            if($this->similiarWords[$i] === $word->es_word_word){
                                unset($this->similiarWords[$i]);
                            }
                        }
                    }
                }
                $namespace = Environment::getSession('similiarWords');
                $namespace->setExpiration(0);
                $namespace->similiarWords = $this->similiarWords;
//                $this->count = count($this->similiarWords);
            }
        }
    }


    protected function createComponentAddWordForm($name){
        $form = new AppForm($this, $name);

        $form->getElementPrototype()->name('form_keywords');

        $form->addHidden("url")->setDefaultValue($this->id);

        $form->addCheckbox("checkAll");

        $namespace = Environment::getSession('similiarWords');
        if(isset ($namespace->similiarWords)){
            $similiarWords = $namespace->similiarWords;
        }else{
            $similiarWords = array();
        }
        foreach($similiarWords as $key => $word){
            $form->addHidden("hidden$key")->setDefaultValue($word);
            $form->addCheckbox("name$key", $word);
            $form["name$key"]->getControlPrototype()->class = "check_word";
        }
        $form->addCheckbox("checkAlls");


        $form->addSubmit("submitWords", "Potvrdit");

        $namespace = Environment::getSession('similiarWords');
        unset ($namespace->similiarWords);

        $form->onSubmit[] = \callback($this, "addwords_submit");

        return $form;
    }

    public function addwords_submit(AppForm $form){
        \dump($form->getValues());
        $namespace = Environment::getSession('similiarWords');
        \dump($namespace->similiarWords);
    }


    public function renderFind($id){
        $this->checkPermission($this->userIdentity->id, $this->id);
        if($id === null){
            $this->template->id = null;
        }else{
            $this->template->id = $id;
        }
        $this->template->similiarWords = $this->similiarWords;
    }


    public function actionConcurrency($id, $concurrency){
        $this->id = $id;
        $this->concurrency = $concurrency;
    }

    public function renderConcurrency(){
        $this->checkPermission($this->userIdentity->id, $this->id);
        $this->template->id = $this->id;
        $this->setLayout("layoutOverview" );
    }


    protected function createComponentGridConcurrency($name) {
        $grid = new \Components\Seo\SearchEngine\MyGridito($this, $name);
        //$grid->

        $model = new DibiModelGrid($this->id, $this->concurrency);

        $search = $this->getParam("search", false);
        if ($search) {
            $model->filterSearch($search);
        }

        $grid->setModel($model);

        $grid->setItemsPerPage(15);


        $grid->addColumn("word", "Slovo")->setSortable(true);
        $grid->addColumn("position_seznam", "Seznam")->setSortable(true);

        $grid->addColumn("position_google", "Google")->setSortable(true);

        $grid->addColumn("position_bing", "Bing")->setSortable(true);

        $grid->addColumn("position_centrum", "Centrum")->setSortable(true);

        $grid->addColumn("position_jyxo", "Jyxo")->setSortable(true);


        return $grid;
    }


    public function handleDeleteWord($idWord){
        //\dump($idWord);
        try{
            $result = $this->getFetchWords()->deleteWord($idWord, $this->id);
            $this->flashMessage("Slovo se podařilo smazat");
        }catch(\DibiDriverException $exception){
            $this->flashMessage("Slovo se nepodařilo smazat");
        }
        $cacheTemp = \Nette\Environment::getCache('Nette.Template.Cache');
         $cacheTemp->clean(array(\Nette\Caching\Cache::TAGS => array("grid$this->id")));
        $this->redirect("this", $this->id);
    }

    public function actionKeywords($id){
        if($id == null){
            $this->redirect(":Admin:Seo:Homepage:default");
        }
        $this->id = $id;
        $this->template->id = $id;
        $this->keywords = \AdminModule\models\database\Web::getAllWord($this->id);
        $this->template->kw = $this->keywords;

        $this->checkPermission($this->userIdentity->id, $this->id);
    }

    public function createComponentKwPoints($name){
        $form = new AppForm($this, $name);

        $array = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10);

        foreach ($this->keywords as $keyword){
            $form->addSelect($keyword->word_id, $keyword->word_word, $array)
                ->setDefaultValue($keyword->weight);
        }
        $form->onSubmit[] = \callback($this, 'submitKwPoints');

        $form->addSubmit("submit", "Odeslat");
    }

    public function submitKwPoints(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                foreach ($form->values as $key => $value){
                   try{
                        \AdminModule\models\database\Web::updateWeightWebWord($this->id, $key, $value);
                   }catch(\DibiDriverException $exception){

                   }
                }
                $this->flashMessage("Váha slov byla aktualizována.");
                $this->redirect("this", $this->id);
            }
        }
    }

}

?>

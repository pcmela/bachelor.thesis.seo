<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConcurrencyPresenter
 *
 * @author wossa
 */
namespace AdminModule\SeoModule\KeywordsModule;

use AdminModule\DefaultPresenter, Nette\Application\AppForm, Nette\Forms\Form;

class ConcurrencyPresenter extends DefaultPresenter{
	
	private $concurrencyModel;
        public $id;

        private $countSeznam;
        private $countGoogle;
        private $dataResults;
	
	/**
	 * 
	 * @return ConcurrencyModel
	 */

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

	private function getConcurrencyModel(){
		if($this->concurrencyModel === null){
			$this->concurrencyModel = new ConcurrencyModel();
		}
		
		return $this->concurrencyModel;
	}
	

	
	public function actionDefault($id){
            if($id == null){

                $this->redirect(":Admin:Seo:Homepage:default");
            }
                $this->id = $id;
                $this->template->id = $id;
		$this->checkConcurrency($id);
                $this->currentConcurrency();
	}

        public function actionDetail($id){
             if($id == null){

                $this->redirect(":Admin:Seo:Homepage:default");
            }
            $this->checkPermission($this->userIdentity->id, $this->id);
            $this->id = $id;
            $this->template->id = $this->id;
        }
	
	private function checkConcurrency($id){
		$web_word = $this->getConcurrencyModel()->getWebWords($id);
		$results = array();
		
		$sql_seznam = "";
		$sql_google = "";
		$last_tab = "";
		$counter = 1;
		foreach ($web_word as $row){
			//$result = $this->getConcurrencyModel()->getTestResults($word->es_web_word_word_id);
			if($counter === 1){
				$sql_seznam = "SELECT tab_".$row->es_web_word_word_id.".domain as domain FROM ";
				$sql_seznam .= " (select tab_seznam.es_test_domain as domain 
					from (select DISTINCT es_test_domain, es_test_search_engine_id from elpod_seo_test where es_test_word_id = $row->es_web_word_word_id  
					AND es_test_search_engine_id = 1 AND es_test_position < 101) AS tab_seznam  ) AS tab_".$row->es_web_word_word_id;
				
				$sql_google = "SELECT tab_".$row->es_web_word_word_id.".domain as domain FROM ";
				$sql_google .= " (select tab_seznam.es_test_domain as domain 
					from (select DISTINCT es_test_domain, es_test_search_engine_id from elpod_seo_test where es_test_word_id = $row->es_web_word_word_id  
					AND es_test_search_engine_id = 2 AND es_test_position < 101) AS tab_seznam  ) AS tab_".$row->es_web_word_word_id;
			}else{		
				$sql_seznam .= " JOIN (select tab_seznam.es_test_domain as domain 
						from (select DISTINCT es_test_domain, es_test_search_engine_id from elpod_seo_test where es_test_word_id = ".$row->es_web_word_word_id."  
						AND es_test_search_engine_id = 1 AND es_test_position < 101) AS tab_seznam  ) AS tab_".$row->es_web_word_word_id;
				$sql_seznam .= " ON ".$last_tab.".domain = tab_".$row->es_web_word_word_id.".domain";
				
				
				$sql_google .= " JOIN (select tab_seznam.es_test_domain as domain 
						from (select DISTINCT es_test_domain, es_test_search_engine_id from elpod_seo_test where es_test_word_id = ".$row->es_web_word_word_id."  
						AND es_test_search_engine_id = 2 AND es_test_position < 101) AS tab_seznam  ) AS tab_".$row->es_web_word_word_id;
				$sql_google .= " ON ".$last_tab.".domain = tab_".$row->es_web_word_word_id.".domain";
			}
			$last_tab = "tab_".$row->es_web_word_word_id;
			$counter++;
		}
		if($sql_seznam !== "" && $sql_google !== ""){
                    $results_seznam = $this->getConcurrencyModel()->fetchSql($sql_seznam);
                    $results_google = $this->getConcurrencyModel()->fetchSql($sql_google);

                    $this->countGoogle = \count($results_google);
                    $this->countSeznam = \count($results_seznam);

                    $this->template->results_seznam = $results_seznam;
                    $this->template->results_google = $results_google;

                    $this->createDataArray($results_seznam);
                    $this->pushDataArray($results_google);
                    $this->template->countData = \count($this->dataResults);
                }

	}

        private function createDataArray($data){
            foreach ($data as $row){
                $this->dataResults[] = $row->domain;
            }
        }


        private function pushDataArray($data){
            foreach ($data as $row){
                if(!\in_array($row->domain, $this->dataResults)){
                    \array_push($this->dataResults, $row->domain);
                }
            }
        }

        private function currentConcurrency(){
            $results = $this->getConcurrencyModel()->currentConcurrency($this->id);
            $webName = \AdminModule\models\database\Web::getUrl($this->id);
            $currentWebs = array();

            $currentWebs[] = $webName->es_web_url;

            foreach ($results as $row){
                $currentWebs[] = $row->es_concurrency_domain;
            }

            $this->template->currentWebs = $currentWebs;

        }

        protected function createComponentDataForm($name){
            $form = new \Nette\Application\AppForm($this, $name);
            $form->addHidden("web_seznam")->setDefaultValue($this->id);

            foreach($this->dataResults as $key => $value){
                $form->addCheckbox("name$key", $value);
                $form["name$key"]->getControlPrototype()->class = "check_word_seznam";
            }

            $form->addSubmit("submit", "Potvrdit");

            $form->onSubmit[] = \callback($this, "submitDataForm");
            return $form;
        }

        public function submitDataForm(\Nette\Application\AppForm $form){
            $this->flashMessage("Povolte prosím javascript ve Vašem prohlížeči.");
            $this->redirect("this", $this->id);
        }

//        protected function createComponentGoogleForm($name){
//            $form = new \Nette\Application\AppForm($this, $name);
//            $form->addHidden("web_google")->setDefaultValue($this->id);
//
//            foreach($this->template->results_google as $key => $value){
//                $form->addCheckbox("name$key", $value->domain);
//                $form["name$key"]->getControlPrototype()->class = "check_word_google";
//            }
//
//            $form->addSubmit("submit", "Potvrdit");
//
//            $form->onSubmit[] = \callback($this, "submitGoogleForm");
//
//            return $form;
//        }
//
//        public function submitGoogleForm(\Nette\Application\AppForm $form){
//            //\dump($form->values);
//        }

        public function createComponentGrid($name){
            $grid = new \Gridito\Grid($this, $name);

            $model = new DibiModelConcurrencyGrid($this->id);

            $grid->setModel($model);

            $grid->addColumn("es_concurrency_domain", "Konkurence");

            $pres= $this;
            $grid->addButton("delete", "Smazat")->setLink(function ($row) use($pres) {
                    return $pres->link("delete!", array("domain" => $row->es_concurrency_domain, "id" => $pres->id));
                });
        }

        public function handleDelete($domain){
            $this->getConcurrencyModel()->deleteConcurency($domain, $this->id);
            $this->redirect("this", $this->id);
        }


        public function handleSig($param, $engine, $web){
            //$this->redirect("this");
            $array = array();
            $param = \explode(",", $param);
            foreach ($param as $key => $value){
                
                if(\trim($value) !== ""){
                    $this->getConcurrencyModel()->insertConcurrency($web, $param[$key]);
                    $array[] = $param[$key];
                }

            }
            $this->flashMessage("Konkurenční weby byly přidány.");
            $this->redirect('this', $this->id);
        }

        protected function createComponentAddWeb($name){
            $form = new AppForm($this, $name);

            $form->addText("name", "Domena konkurencniho webu")
                    ->addRule(Form::URL, "Doména není ve správném formátu");
            $form->addSubmit("submitWeb", "Odeslat");
            $form->onSubmit[] = \callback($this, "submitAddWeb");

            return $form;
        }

        public function submitAddWeb(AppForm $form){
            if($form->isSubmitted()){
                if($form->isValid()){
                    try{
                        $this->getConcurrencyModel()->addWebConcurrency($this->id, $form->values["name"]);
                    }catch(\DibiDriverException $exception){
                        $form->addError("Zadané doména konkurenčního webu již je zadána.");
                    }
                }
            }
        }

        public function renderDefault(){
            $this->checkPermission($this->userIdentity->id, $this->id);
            $this->template->dataResults = $this->dataResults;
        }

        public function renderDetail(){
            $this->checkPermission($this->userIdentity->id, $this->id);
        }
}
?>

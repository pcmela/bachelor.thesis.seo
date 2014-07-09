<?php

/**
 * Description of CheckPresenter
 *
 * @author wossa
 */

namespace FrontModule\SeoModule\SearchEngineModule;

use Nette\Application\AppForm, Nette\Forms\Form;

class CheckPresenter extends \BasePresenter {
    
    private $grid = false;
    private $gridData;
    private $searchEngines;

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
        $this->searchEngines = array("google" => false, "bing" => false, "seznam" => false);
    }

    public function actionDefault() {

    }

    public function renderDefault() {
        $this->template->isLogged = $this->isLogged();
        $model = new \BaseModel();
        if($this->gridData !== null){
            $this->template->grid = true;
        }else{
            $this->template->grid = false;
        }
    }

    /**
     *
     * @param String $name
     * @return AppForm
     */
    protected function createComponentCheckKeywordsForm($name){
        $form = new AppForm($this, $name);

        $form->addText("keywords", "Klíčová slova: ")
                ->addRule(Form::FILLED, "Prosím zadejte nějaké klíčové slovo.");

        $form->addText("domain", "URL adresa Vašeho webu: ")
                ->addRule(Form::FILLED, "Prosím zadejte URL adresu Vašeho webu.")
                ->addRule(Form::URL, "Neplatný tvar URL adresy.");

        $form->addCheckbox("seznam", "Seznam.cz");
        $form->addCheckbox("google", "Google.cz");
        $form->addCheckbox("bing", "Bing.com");

        $form->addSubmit("submit", "Odeslat");

        $form->onSubmit[] = \callback($this, "checkKeywordsFormSubmitted");

        return $form;
    }

    public function checkKeywordsFormSubmitted(AppForm $form){
        $gridData = null;
        $engineResult = array();
        $dataRow = array();
        $engineSerp = array();

        if($form->isSubmitted()){
            if($form->isValid()){
                $data = $form->getValues();

                if($data["google"]){
                    $this->searchEngines["google"] = true;
                    $google = new \Components\Seo\SearchEngine\FindKeywordsGoogle($data["keywords"], $data["domain"]);
                    $engineSerp[] = $google->getPositionKeywords(null, false);
                }

                if($data["bing"]){
                    $this->searchEngines["bing"] = true;
                    $bing = new \Components\Seo\SearchEngine\FindKeywordsBing($data["keywords"], $data["domain"]);
                    $engineSerp[] = $bing->getPositionKeywords(null, false);
                }

                if($data["seznam"]){
                    $this->searchEngines["seznam"] = true;
                    $seznam = new \Components\Seo\SearchEngine\FindKeywordsSeznam($data["keywords"], $data["domain"]);
                    $engineSerp[] = $seznam->getPositionKeywords(null, false);
                }

                //$engineSerp[] = $google->getPositionKeywords(null, false);
                //$engineSerp[] = $bing->getPositionKeywords(null, false);


                foreach ($engineSerp as $oneSerp){
                    foreach ($oneSerp as $serp){
                        $engineResult[] = $this->find($data["domain"], $serp);
                    }
                }


                $engineResult = $this->formatResult($engineResult);


                foreach ($engineResult as $result){
                    if($result !== null){
                        $dataRow[] = new \DibiRow($result);
                    }
                }

                $this->gridData = $dataRow;
            }
        }else{
            return $form;
        }
    }


    private function formatResult(array $data){
        $result = array();

        $i = 0;
        foreach ($data as $part) {
            $partResult["word"] = $part["word"];
            $partResult[$part["engine"] . "_position"] = $part["position"];

            $duplicity = false;

            foreach ($result as $row){
                if($row["word"] === $part["word"]){
                    $duplicity = true;
                }
            }
            if(!$duplicity){
                foreach ($data as $innerPart){

                    if($part["word"] === $innerPart["word"] && $part["engine"] !== $innerPart["engine"]){
                        $partResult[$innerPart["engine"] . "_position"] = $innerPart["position"];
                    }
                }
                $result[] = $partResult;
            }
            $partResult = null;
        }

        return $result;
    }

    private function find($domain, $result){
        $return = null;
        $engine = null;
        $word = null;
        foreach ($result as $row){
            if($engine === null){
                $engine = $row["engine"];
                $word = $row["word"];
            }
            if($row["domain"] === $domain){                
                $return = $row;
                break;
            }
        }
        
        if($return === null){
            $return = array("position" => null, "word" => $word, "engine" => $engine);
        }

        return $return;
    }

    protected function createComponentGrid($name){
        $grid = new \Gridito\Grid($this, $name);

        $model = new \Components\Seo\SearchEngine\DibiModel($this->gridData);

        $grid->setModel($model);

        $grid->addColumn("word", "Slovo");
        if($this->searchEngines["google"]){
            $grid->addColumn("google_position", "Google");
        }

        if($this->searchEngines["bing"]){
            $grid->addColumn("bing_position", "Bing");
        }

        if($this->searchEngines["seznam"]){
            $grid->addColumn("seznam_position", "Seznam");
        }

        return $grid;
    }

}

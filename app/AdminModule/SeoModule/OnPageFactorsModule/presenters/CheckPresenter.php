<?php

/**
 * Description of CheckPresenter
 *
 * @author wossa
 */

namespace AdminModule\SeoModule\OnPageFactorsModule;

use Nette\Forms\Form, Nette\Application\AppForm;

class CheckPresenter extends \BasePresenter {

    private $check;

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
    }

    public function actionDefault() {

    }

    public function handleSignal(){
        \dump('funguje to...');
        $this->redirect('signal');
    }
    
    protected function createComponentCheckForm($name){
        $form = new AppForm($this, $name);
        
        $form->addText("webUrl", "Adresa WWW:")
                ->addRule(Form::FILLED, "Adresa WWW musí být vyplněna.")
                ->addRule(Form::URL, "Neplatný formát WWW adresy.");
        
        $form->addSubmit("submit", "Zkontrolovat");
        
        $form->onSubmit[] = callback($this, "submitCheckForm");
    }
    
    public function submitCheckForm(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $values = $form->getValues();

                $this->check = new FactorsModel($values["webUrl"]);
                $this->check->checkFactors();
            }
        }else{
            return $form;
        }
    }

    public function renderDefault() {
        if($this->check !== null){
            $this->template->data = true;

            $head = 0;

            $this->template->validateHtml = $this->check->getValidHtml();

            $this->template->title = $this->check->getTitle();
            if($this->template->title["status"] === "ok"){
                $head += 19;
            }
            $this->template->description = $this->check->getDescription();
            if($this->template->description["status"] === "ok"){
                $head += 5;
            }
            $this->template->keywords = $this->check->getKeywords();
            if($this->template->keywords["status"] === "ok"){
                $head += 5;
            }
            $this->template->author = $this->check->getAuthor();
            if($this->template->author["status"] === "ok"){
                $head += 1;
            }
            $this->template->robotsInfo = $this->check->getRobotsInfo();
            if($this->template->robotsInfo["status"] === "ok"){
                $head += 5;
            }
            $this->template->doctype["status"] = $this->template->validateHtml["doctype"];
            if($this->template->doctype != "" && $this->template->doctype != null){
                $this->template->doctype["data"] = "Stránka obsahuje definici dokumentu.";
                $head += 1;
            }else{
                $this->template->doctype["data"] = "Stránka neobsahuje definici dokumentu.";
            }
            $this->template->charset = $this->check->getCharset();
            if($this->template->charset["status"] === "ok"){
                $head += 19;
            }
            $this->template->head = $head;


            $source = 0;

            
            if($this->template->validateHtml["errors"] == 0 && $this->template->validateHtml["warnings"] == 0){
                $source += 7;
            }
            $this->template->headlinesStruct = $this->check->getHeadlines();
            $this->template->htmlSize = $this->check->getHtmlSize();
            $this->template->sizeOfJavascript = $this->check->getUnobtrusiveJavascript();
            if($this->template->sizeOfJavascript["size"] == 0){
                $source += 4;
            }else if($this->template->sizeOfJavascript["size"] < 1){
                $source += 2;
            }
            $this->template->sizeOfCss = $this->check->getExtCss();
            if($this->template->sizeOfCss["size"] == 0){
                $source += 4;
            }else if($this->template->sizeOfCss["size"] < 1){
                $source += 2;
            }

            $this->template->source = $source;


            $headError = false;
            $last = 0;
            foreach ($this->check->getHeadlines() as $element){
                if ($element["status"] === false){
                    $headError = true;
                    break;
                }
            }
            if($headError){
                $this->template->bodyHeadlines = 0;
            }else{
                $this->template->bodyHeadlines = 10;
            }

            $othersBody = 0;
            $sitemap = $this->check->getSitemap();
            if($sitemap == "HTTP/1.1 200 OK"){
                $othersBody += 5;
            }

            $this->template->nestedTables = $this->check->getNestedTables();
            if($this->template->nestedTables == true){
                $othersBody += 1;
            }
            $this->template->altContent = $this->check->getAltContent();
            if($this->template->altContent == true){
                $othersBody += 8;
            }
            
            $this->template->textSize = $this->check->getTextSize();
            if($this->template->textSize >= 1){
                $othersBody += 6;
            }

            $this->template->sitemap = $sitemap;

            $this->template->links = $this->check->getLinks();

            $this->template->othersBody = $othersBody;

            $this->template->sumScore = $head + $source + $this->template->bodyHeadlines + $othersBody;
        }else{
            $this->template->data = false;
        }
    }

}
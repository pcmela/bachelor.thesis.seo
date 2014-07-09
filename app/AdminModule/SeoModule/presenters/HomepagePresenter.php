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
namespace AdminModule\SeoModule;

use \Nette\Forms\Form, Nette\Application\AppForm, Nette\Environment;

class HomepagePresenter extends \AdminModule\DefaultPresenter{

    private $dataModel = null;
    

    public function  __construct() {
        parent::__construct();
    }

    /**
     *
     * @return DataModel
     */
    public function getData() {
        if ($this->dataModel === NULL)
            $this->dataModel = new DataModel ();
        return $this->dataModel;
    }

    public function handleSig(){
        $this->redirect('this');
    }

    

    public function createComponentAddWebForm($name){
        $form = new AppForm($this, $name);

        $form->addHidden("es_web_id_user")
                ->setDefaultValue($this->userIdentity->id);

        $form->addText("es_web_url", "URL: ")
                ->addRule(Form::FILLED, "Vyplňte URL adresu stránky")
                ->addRule(Form::URL, "Neplatny tvar URL");

        $form->addHidden("es_web_date")
                ->setDefaultValue(\date("Y:m:d"));


//        $form->addHidden("vymena_odkazu")
//                ->setDefaultValue(null);

        $form->addSubmit("add", "Přidat");

        $form->onSubmit[] = callback($this, "addWebFormSubmitted");

        return $form;
    }

    public function addWebFormSubmitted(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $values = $form->getValues();
                $values["es_web_url"] = \str_replace("http://", "", $values["es_web_url"]);
                $values["es_web_url"] = \str_replace("www.", "", $values["es_web_url"]);
                if(\substr($values["es_web_url"], -1) === "/"){
                    $values["es_web_url"] = \substr($values["es_web_url"], 0, -1);
                }
                $values['es_web_id_user'] = \intval($values['es_web_id_user']);

                $this->data->addWeb($values);
                $this->redirect("Homepage:default");
            }
        }else{
            return $form;
        }
    }

    public function createComponentAddPermision($name){
        $form = new AppForm($this, $name);

//        $form->addText("web", "Web: ")
//                ->addRule(Form::FILLED, "Vyplnte adresu webu.");
        $form->addSelect("web", "Web: ", $this->getUserWebs());

        $form->addText("user", "E-mail uzivatele: ")
                ->addRule(Form::FILLED, "Zadejte e-mail uzivatele.");

        $form->addSelect("role", "Opravneni: ", $this->getWebPermission());

        $form->addSubmit("send", "Potvrdit");

        $form->onSubmit[] = callback($this, "addPermisionUserSubmitted");
        return $form;
    }

    /**
     *
     * @return array
     */
    private function getWebPermission(){
        $permissionResult = $this->getData()->getWebPermission();
        $permissions = array();

        foreach ($permissionResult as $permission){
            $permissions[$permission->es_role_id] = $permission->es_role_name;
        }

        return $permissions;
    }

    private function getUserWebs(){
        $websResult = \AdminModule\models\database\User::getWebs($this->userIdentity->id);
        $webs = array();

        foreach ($websResult as $web){
            $webs[$web->es_web_id] = $web->es_web_url;
        }

        return $webs;
    }

    protected function createComponentGrid($name){
        $grid = new \Gridito\Grid($this, $name);
        $grid->setModel(new DibiModelPermission($this->userIdentity->id));

        $grid->addColumn("user_mail", "E-Mail")->setSortable(true);
        $grid->addColumn("web_url", "Web")->setSortable(true);
        $grid->addColumn("role_name", "Pravo")->setSortable(true);

        $pres = $this;
        $grid->addButton("delete", "Smazat")->setLink(function ($row) use($pres) {
            return $pres->link("deletePermission!", $row->user, $row->web_id);
        });

        return $grid;
    }

    public function addPermisionUserSubmitted(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $values = $form->getValues();
                try{
                    if(!$this->getData()->addPermisionUser($values)){
                        $this->flashMessage("Uživatel se zadaným e-mailem bohužel neexistuje.");
                    }else{
                        $this->flashMessage("Opravnění bylo úspěšně přiděleno.");
                        $this->redirect("Homepage:default");
                    }
                }catch(\DibiDriverException $exception){
                    $this->flashMessage("Uživatel již má předěleno oprávnění k tomuto webu.");
                }
            }
        }else{
            return $form;
        }
    }

    protected function createComponentMyWebGrid($name){
        $grid = new MyWebsGrid($this, $name);

        $model = new DibiHomepageSeoGridModel();
        $model->setMyWebsFluent($this->userIdentity->id);
        $grid->setModel($model);

        $grid->addColumn("es_web_url", "WWW stánka");

        $pres = $this;
        $grid->addButton("detail", "Detail")->setLink(function ($row) use($pres) {
            return $pres->link(":Admin:Seo:Keywords:addKeywords:default", $row->es_web_id);
        });

        $grid->addButton("delete", "Smazat", array(
            "link" => function ($row) use($pres) {
                return $pres->link("deleteWeb!", $row->es_web_id);
            },
            "confirmationQuestion" => function ($row) {
                return "Opravdu si přejete vymazat webovou stránku $row->es_web_url?";
            }
        ));

        return $grid;
    }

    protected function createComponentCoOwnerWebGrid($name){
        $grid = new \Gridito\Grid($this, $name);

        $model = new DibiHomepageSeoGridModel();
        $model->setCoOwnerWebsFluent($this->userIdentity->id);
        $grid->setModel($model);

        $grid->addColumn("url", "WWW stánka");

        $pres = $this;
        $grid->addButton("detail", "Detail")->setLink(function ($row) use($pres) {
            return $pres->link(":Admin:Seo:Keywords:addKeywords:default", $row->id);
        });

        return $grid;
    }

    protected function createComponentViewerWebGrid($name){
        $grid = new \Gridito\Grid($this, $name);

        $model = new DibiHomepageSeoGridModel();
        $model->setViewerWebsGrid($this->userIdentity->id);
        $grid->setModel($model);

        $grid->addColumn("url", "WWW stánka");

        $pres = $this;
        $grid->addButton("detail", "Detail")->setLink(function ($row) use($pres) {
            return $pres->link(":Admin:Seo:Keywords:addKeywords:default", $row->id);
        });

        return $grid;
    }

    public function handleDelete($id_w, $id_u){
        $this->data->deletePermission($id_w, $id_u);

        $backlink = $this->getApplication()->storeRequest();
        $this->redirect("this", array('backlink' => $backlink));
    }

    

    public function renderDefault(){
        $this->template->webs = $this->data->fetchAllWebs($this->userIdentity->id);
        $this->template->co_ownerWebs = $this->data->fetchAllCoOwnerWebs($this->userIdentity->id);
        $this->template->viewWebs = $this->data->fetchAllWiewWebs($this->userIdentity->id);
    }

    public function renderCheckPermission(){
        //$this->template->data = $this->data->getListPermissions($this->userIdentity->id);
    }

    public function handleDeletePermission($user, $webId){
        try{
            $this->getData()->deleteWebPermission($user, $webId);
            $this->flashMessage("Oprávnění bylo úspěšně smazáno.");
            \dump($user, $webId);
        }catch(\DibiDriverException $exception){
            $this->flashMessage("Oprávnění nebylo úspěšně smazáno.");
        }
        $this->redirect("this");
    }

    public function handleDeleteWeb($id){
        $result = $this->getData()->deleteWeb($id);

        if($result === false){
            $this->flashMessage("Na webovou stránku má ještě někdo další oprávnění. Pokud můžete, smažte oprávnění a poté smažte stránku.");
        }else{
            $this->flashMessage("Webová stránka byla úspěšně smazána.");
            $this->redirect("this");
        }
    }
}
?>

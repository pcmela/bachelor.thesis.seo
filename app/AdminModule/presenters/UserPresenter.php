<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPresenter
 *
 * @author WoSSa
 */
namespace AdminModule;
use Nette\Application\AppForm, Nette\Forms\Form;

class UserPresenter extends DefaultPresenter{
    
    private $id;


    protected function createComponentUserForm($name){
        $form = new AppForm($this, $name);

        $form->addPassword("currentPass", "Stávající heslo")
                ->addRule(Form::FILLED, "Zadejte prosím původní heslo");

        $form->addPassword("newPass", "Nové heslo")
                ->addRule(Form::FILLED, "Zadejte nové heslo");

        $form->addPassword("newPassRepeat", "Zadejte nové heslo ještě jednou")
                ->addRule(Form::FILLED, "Zadejte nové heslo ještě jednou")
                ->addRule(Form::EQUAL, "Zadaná hesla se neshodují.", $form["newPass"]);

        $form->addSubmit("submitNewPass", "Potvrdit");

        $form->onSubmit[] = \callback($this, "submitNewPass");
    }

    public function submitNewPass(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $currentPass = models\database\User::getPass($this->userIdentity->id);
                \dump($currentPass);
                \dump(\sha1($form->values["currentPass"]));
                if($currentPass->ea_user_password === \sha1($form->values["currentPass"])){
                    try{
                        models\database\User::changePassword($this->userIdentity->id, $form->values["newPass"]);
                        $this->flashMessage("Vaše heslo bylo změněno");
                        $this->redirect("this");
                    }catch(\DibiDriverException $exception){
                        $this->flashMessage("Vaše heslo se nepodařilo změnit, zkuste to ještě jednou.");
                        $this->redirect("this");
                    }

                }else{
                    $this->flashMessage("Bylo zadáno neplatné aktuální heslo.");
                    $this->redirect("this");
                }
                
            }
        }
    }
}
?>

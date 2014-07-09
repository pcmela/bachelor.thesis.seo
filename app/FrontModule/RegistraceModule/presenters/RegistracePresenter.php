<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegistracePresenter
 *
 * @author WoSSa
 */
namespace FrontModule\RegistraceModule;

use Nette\Forms\Form, Nette\Application\AppForm;

class RegistracePresenter extends \BasePresenter{
    
    private $registraceModel = null;

    public function getRegistrace() {
        if ($this->registraceModel === NULL)
            $this->registraceModel = new RegistraceModel ();
        return $this->registraceModel;
    }

    public function createComponentRegistraceForm($name){
        $form = new AppForm($this, $name);

        $form->addText("ea_user_name", "Uživatelské jméno: ")
                ->addRule(Form::FILLED, "Prosím zadejte uživatelské jméno.");

        $form->addText("ea_user_mail", "E-mail: ")
                ->addRule(Form::FILLED, "Zadejte prosím Vaší e-mailovou adresu.")
                ->addRule(Form::EMAIL, "Neplatná e-mailová adresa.");

        $form->addPassword("ea_user_password", "Heslo: ")
                ->addRule(Form::FILLED, "Zadejte prosím heslo.");

        $form->addPassword("heslo2", "Opakujte heslo: ")
                ->addRule(Form::FILLED, "Zadejte heslo ještě jednou pro kontrolu.")
                ->addRule(Form::EQUAL, "Zadaná hesla se neshodují.", $form['ea_user_password']);

        $form->addHidden("ea_user_role")
                ->setDefaultValue(2);

        $form->addSubmit("register", "Zaregistrovat se");

        $form->onSubmit[] = callback($this, "registerFormSubmitted");
    }

    public function registerFormSubmitted(AppForm $form){
        if($form->isSubmitted()){
            if($form->isValid()){
                $values = $form->getValues();
                unset($values["heslo2"]);
                $values["ea_user_password"] = \sha1($values["ea_user_password"]);
                try{
                    if(!$this->registrace->checkMail($values["ea_user_mail"])){
                        $this->registrace->insertUser($values);


                        $this->flashMessage("Registrace probehla uspesne, nyní se můžete přihlásit.");
                        $this->redirect("this");
                    }else{
                        $form->addError("Zadany mail je obsazen, zvolte prosim jine.");
                        return $form;
                    }
                }catch(\MyException\Database\DupliciteDataException $exception){
                    $form->addError("Na Vaší e-mailovou adresu je již někdo registrován.");
                    return $form;
                }
            }
        }else{
            return $form;
        }
    }

    public function renderDefault(){
        $this->template->isLogged = $this->isLogged();
    }
}
?>

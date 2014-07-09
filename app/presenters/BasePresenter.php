<?php

use Nette\Environment;
use Nette\Application\Presenter;
use Nette\Application\AppForm;
use Nette\Forms\Form, Nette\Security\AuthenticationException, Nette\Security\SimpleAuthenticator, Nette\Debug;

abstract class BasePresenter extends Nette\Application\Presenter {

    public $oldLayoutMode = FALSE;
    public $oldModuleMode = FALSE;

    protected $user;
    protected $userIdentity;


    public function  __construct() {
        $this->user = Environment::getUser();
        $this->userIdentity = $this->user->getIdentity();
    }

    protected function beforeRender() {
        $this->template->view = $this->view;
        $this->template->user = Environment::getUser()->isLoggedIn();
        Debug::fireLog($this->template->user);

        $a = strrpos($this->name, ':');
        if ($a === FALSE) {
            $this->template->moduleName = '';
            $this->template->presenterName = $this->name;
        } else {
            $this->template->moduleName = substr($this->name, 0, $a + 1);
            $this->template->presenterName = substr($this->name, $a + 1);
        }
    }

    public function createComponentLogin($name) {

        $form = new AppForm($this, $name);

        $form->addText("email", "Váš e-mail:")
                ->addRule(Form::FILLED, "Prosím vyplňte e-mailovou adresu.");

        $form->addPassword("password", "Heslo:")
                ->addRule(Form::FILLED, "Prosím zadejte heslo.");

        $form->addSubmit('login', " Přihlásit ");

        $form["email"]->getControlPrototype()->class = "txtBox";
        $form["password"]->getControlPrototype()->class = "txtBox";
        $form["login"]->getControlPrototype()->class = "go";

        $form->onSubmit[] = callback($this, "loginFormSubmitted");

        return $form;
    }

    public function loginFormSubmitted(AppForm $form) {

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                try {
                    $values = $form->values;
                    $user = $this->getUser();
                    $user->setAuthenticationHandler(new \AdminModule\AuthenticatorModel());

                    $user->login($values['email'], $values['password']);
                    $this->redirect(":Admin:Homepage:default");
                } catch (AuthenticationException $e) {
                    $form->addError($e->getMessage());
                    //$this->redirect("this");
                }
            }
        } else {
            return $form;
        }
    }

    protected function isLogged() {

        if (!$this->user->isLoggedIn()) {
            return false;
        }else{
            return true;
        }
    }

    public function handleLogout(){
        $this->user->logout();
        $backlink = $this->getApplication()->storeRequest();
        $this->redirect(":Front:Homepage:default",array('backlink' => $backlink));
    }

}

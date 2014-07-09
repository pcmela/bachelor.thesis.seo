<?php

namespace AdminModule;

use Nette\Environment;

class DefaultPresenter extends \BasePresenter
{

//    protected  $user = null;
//    protected  $userIdentity = null;

    public function  __construct() {
        parent::__construct();
    }

    public function  startup() {
        parent::startup();

        if(!$this->user->isLoggedIn()){
            if($this->user->getLogoutReason() === \Nette\Web\User::INACTIVITY){
                $this->flashMessage("Byl jste odhlasen z duvodu neaktivity, prosim prihlaste se znovu.");
            }else{
                $this->flashMessage("Nejste prihlasen, prosim, prihlaste se.");
            }
            $backlink = $this->getApplication()->storeRequest();
            $this->redirect(":Front:Homepage:default");
        }
    }

    
}

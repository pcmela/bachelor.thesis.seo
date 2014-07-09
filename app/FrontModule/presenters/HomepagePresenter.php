<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */



/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
namespace FrontModule;

use Nette\Application\Presenter;
use Nette\Application\AppForm;
use Nette\Forms\Form, Nette\Security\AuthenticationException, Nette\Security\SimpleAuthenticator, Nette\Debug;

class HomepagePresenter extends \BasePresenter
{

    public function __construct(){
        parent::__construct();

    }

        public function renderDefault(){
            $this->template->isLogged = $this->isLogged();
            $this->setLayout('layoutHome');
        }

        public function renderSeo(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderCheckPosition(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderConcurrency(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderConcurrecyView(){
            $this->template->isLogged = $this->isLogged();
        }
        public function renderFindKeywords(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderOnPageFactors(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderOnas(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderOverAll(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderPodpora(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderRanks(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderSluyby(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderStatistiky(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderServices(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderProducts(){
            $this->template->isLogged = $this->isLogged();
        }

        public function renderServicesView(){
            $this->template->isLogged = $this->isLogged();
        }

}

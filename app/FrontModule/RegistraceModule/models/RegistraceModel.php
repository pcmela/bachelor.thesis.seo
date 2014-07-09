<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegistraceModel
 *
 * @author WoSSa
 */
namespace FrontModule\RegistraceModule;

use MyException\Database\DupliciteDataException;

class RegistraceModel extends \BaseModel{

    public function  __construct() {
        parent::__construct();
        $this->name = 'elpod_all_user';
    }

    public function insertUser(array $data){
        try{
            $this->connection->insert($this->name, $data)->execute();
        }catch(\InvalidArgumentException $exception){
            //\Nette\Debug::dump($exception);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }catch(\Exception $exception){

        }
    }

    public function checkMail($mail){
        try{
            $fetch = $this->connection->fetch("SELECT ea_user_mail FROM elpod_all_user WHERE ea_user_mail = %s", $mail);
            if(!$fetch){
                return false;
            }else{
                return true;
            }
        }catch(DibiDriverException $exception){
            throw $exception;
        }
        return true;
    }
}
?>

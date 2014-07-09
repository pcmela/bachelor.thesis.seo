<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Uzivatel
 *
 * @author WoSSa
 */
namespace AdminModule\models\database;

class User extends \BaseModel{


    public static function getWeb($idUser, $idWeb){
        try{
            return self::$defaultConnection->fetch("select web.es_web_id from elpod_all_user as user
                join elpod_seo_web as web on user.ea_user_id = ",$idUser,"
                and user.ea_user_id = web.es_web_id_user and web.es_web_id = ",$idWeb);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function getPermissionWeb($idUser, $idWeb){
        try{

            return self::$defaultConnection->fetch("select user.ea_user_id, permission.es_permission_role_id as permission from elpod_all_user as user
                join elpod_seo_permission as permission on user.ea_user_id = permission.es_permission_user_id
                and permission.es_permission_web_id =",$idWeb," and user.ea_user_id =",$idUser);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function getWebs($idUser){
        try{
            return self::$defaultConnection->fetchAll("select es_web_id, es_web_url from elpod_seo_web where es_web_id_user = %i", $idUser);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function changePassword($idUser, $newPass){
        try{
            $password = \sha1($newPass);
            return self::$defaultConnection->query("UPDATE elpod_all_user SET ea_user_password = %s", $password, " WHERE ea_user_id = %i", $idUser);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function getPass($idUser){
        try{
            return self::$defaultConnection->fetch("SELECT ea_user_password FROM elpod_all_user WHERE ea_user_id = %i", $idUser);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    
}

?>

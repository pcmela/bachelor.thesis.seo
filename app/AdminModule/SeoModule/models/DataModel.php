<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataModel
 *
 * @author WoSSa
 */
namespace AdminModule\SeoModule;

class DataModel extends \BaseModel{


    public function fetchAllWebs($id_user){
        try{
            if(!\is_int($id_user)){
                $id_user = \intval($id_user);
            }
            return $this->connection->fetch("SELECT count(*) as count FROM elpod_seo_web WHERE es_web_id_user = %i", $id_user);
        }catch(DibiException $exception){
            throw $exception;
        }
    }

    public function fetchAllCoOwnerWebs($id_user){
        try{
            return $this->connection->fetch("select count(*) as count from elpod_all_user as user
                join elpod_seo_permission as permission on user.ea_user_id = permission.es_permission_user_id and permission.es_permission_role_id = 1
                and user.ea_user_id = %i",$id_user,"
                join elpod_seo_web as web on permission.es_permission_web_id = web.es_web_id");
        }catch(DibiException $exception){
            throw $exception;
        }
    }

    public function fetchAllWiewWebs($id_user){
        try{
            return $this->connection->fetchAll("select count(*) as count from elpod_all_user as user
                join elpod_seo_permission as permission on user.ea_user_id = permission.es_permission_user_id and permission.es_permission_role_id = 2
                and user.ea_user_id = %i",$id_user,"
                join elpod_seo_web as web on permission.es_permission_web_id = web.es_web_id; ");
        }catch(DibiException $exception){
            throw $exception;
        }
    }

    public function addWeb($data){
        try{
            $this->connection->insert("elpod_seo_web", $data)->execute();
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    public function addPermisionUser($data){
        try{
            $id_web = $data["web"];

            $id_uzivatel = $this->connection->fetch("SELECT ea_user_id FROM elpod_all_user WHERE ea_user_mail = %s", $data["user"]);
            if($id_uzivatel !== null){
                $id_uzivatel = $id_uzivatel->ea_user_id;
            }else{
                $id_uzivatel = null;
            }

            if($id_uzivatel !== null){
                \dump($id_uzivatel);
                $this->connection->query("INSERT INTO elpod_seo_permission VALUES( %i", $id_web, ", %i", $id_uzivatel,
                        ", %i", \intval($data["role"]) ,")");
                return true;
            }else{
                return false;
            }
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public function getListPermissions($idUser){
        try{
            return $this->connection->query("SELECT w.web_url, w.id_web, o.typ_prava, u.e_mail,u.id_uzivatel FROM web AS w INNER JOIN
                opravneni AS o ON w.id_web = o.id_web INNER JOIN uzivatel AS u ON o.id_uzivatel = u.id_uzivatel WHERE w.id_uzivatel = %i", \intval($idUser));
        }catch(DibiDriverException $exception){
            throw $exception;
        }

    }

    public function deletePermission($id_web,$id_uzivatel){
        try{
            $this->connection->query("DELETE FROM opravneni WHERE id_web = %i", \intval($id_web), " AND id_uzivatel = %i", \intval($id_uzivatel));
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    public function getWebPermission(){
        try{
            return $this->connection->fetchAll("SELECT es_role_id, es_role_name FROM elpod_seo_role");
        }catch(DibiException $exception){
            throw $exception;
        }
    }

    public function deleteWebPermission($user, $webId){
        $this->connection->query("DELETE FROM elpod_seo_permission where es_permission_web_id = %i", $webId, " and es_permission_user_id = %i", $user);
    }

    public function deleteWeb($id){
        try{
            $words = $this->connection->fetchAll("SELECT es_web_word_word_id FROM elpod_seo_web_word WHERE es_web_word_web_id = %i", $id);
            $this->connection->query("DELETE FROM elpod_seo_permission WHERE es_permission_web_id = %i", $id);
            $this->connection->query("DELETE FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
            $this->connection->query("DELETE FROM elpod_seo_archive_ranks WHERE es_ranks_archive_web_id = %i", $id);

            foreach ($words as $word){
                \AdminModule\models\database\Slovo::deleteWord($word, $id);
            }

            $this->connection->query("DELETE FROM elpod_seo_concurrency WHERE es_concurrency_web_id = %i", $id);
            $this->connection->query("DELETE FROM elpod_seo_web WHERE es_web_id = %i", $id);
            return true;
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }
}
?>

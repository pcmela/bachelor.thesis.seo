<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DibiGridModel
 *
 * @author WoSSa
 */
namespace AdminModule\SeoModule;

class DibiHomepageSeoGridModel extends \BaseModel{

    public function __construct(){
        parent::__construct();
    }

    public function setMyWebsFluent($id){
        $this->fluent = $this->connection->select("es_web_id, es_web_url")->from("elpod_seo_web")->where("es_web_id_user = %i",$id);
    }

    public function setCoOwnerWebsFluent($id){
        $this->fluent = $this->connection->select("*")->from("(select web.es_web_id as id, web.es_web_url as url from elpod_all_user as user
                join elpod_seo_permission as permission on user.ea_user_id = permission.es_permission_user_id and permission.es_permission_role_id = 1
                and user.ea_user_id = %i",$id,"
                join elpod_seo_web as web on permission.es_permission_web_id = web.es_web_id) as coOwner");
    }

    public function setViewerWebsGrid($id){
        $this->fluent = $this->connection->select("*")->from("(select web.es_web_id as id, web.es_web_url as url from elpod_all_user as user
                join elpod_seo_permission as permission on user.ea_user_id = permission.es_permission_user_id and permission.es_permission_role_id = 2
                and user.ea_user_id = %i",$id,"
                join elpod_seo_web as web on permission.es_permission_web_id = web.es_web_id) as viewer");
    }
}
?>

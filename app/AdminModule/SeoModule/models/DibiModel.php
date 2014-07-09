<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DibiModel
 *
 * @author WoSSa
 */
namespace AdminModule\SeoModule;

class DibiModelPermission extends \BaseModel{

    public function __construct($id){
        parent::__construct();
        $this->fluent = $this->connection->select("*")->from("elpod_view_seo_overview_permission")->where("owner = %i", \intval($id));
    }
}
?>

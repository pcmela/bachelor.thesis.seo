<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseModel
 *
 * @author WoSSa
 */
use Nette\Environment;

class BaseModel extends \Gridito\DibiFluentModel{

    /** @var DibiConnection */
    public static $defaultConnection;

    public static function connect(){
        self::$defaultConnection = dibi::connect(array(
            'driver'   => 'mysqli',
            'host'     => '127.0.0.1',
            'database' => 'elpod',
            'username' => 'root',
            'password' => 'heslo',
        ));
    }

    public static function disconnect(){
        self::$defaultConnection->disconnect();
    }

    /** @var DibiConnection */
    protected $connection;

    /** Database table name */
    protected $name;

    public function  __construct(DibiConnection $connection = NULL) {
        parent::__construct(null);
        $this->connection = ($connection !== NULL ? $connection : self::$defaultConnection);
    }

    public function test(){
        return $this->connection->select("*")->from("elpod_all_user")->execute()->setRowClass("DibiRow")->fetchAll();
    }


}
?>

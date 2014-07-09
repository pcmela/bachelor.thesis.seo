<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FetchDataModel
 *
 * @author WoSSa
 */
namespace Components\Seo\SearchEngine;

class FetchData extends \BaseModel{

    public function __construct(){
        parent::__construct();
    }

//    public function fetchKeywords($url){
//        try{
//            return $this->connection->fetchAll("SELECT slovo.slovo FROM web INNER JOIN web_slovo ON web.id_web = web_slovo.id_web
//                INNER JOIN slovo ON web_slovo.id_slovo = slovo.slovo_id WHERE web.id_web = %i", \intval($url));
//        }catch(Exception $exception){
//            throw $exception;
//        }
//    }
    
    public function fetchKeywordsSeznam($url){
        try{
            return $this->connection->fetchAll("SELECT * FROM prehled_seznam WHERE id_web = %i", \intval($url));
        }catch(Exception $exception){
            throw $exception;
        }
    }
    
    public function fetchKeywordsGoogle($url){
        try{
            return $this->connection->fetchAll("SELECT * FROM prehled_google WHERE id_web = %i", \intval($url));
        }catch(Exception $exception){
            throw $exception;
        }
    }
    
    public function fetchKeywordsBing($url){
        try{
            return $this->connection->fetchAll("SELECT * FROM prehled_bing WHERE id_web = %i", \intval($url));
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function fetchPosition($id, $slovo){
        try{
            return $this->connection->fetch("SELECT pozice, stara_pozice FROM prehled WHERE id_web = %i", $id , " AND slovo = %s", $slovo);
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function copyToArchive($idKeyword,$idEngine, $date, $id){
        try{
            $data = $this->connection->fetchAll("SELECT * FROM elpod_seo_test WHERE es_test_word_id = %i", \intval($idKeyword),
                    " AND es_test_search_engine_id = %i", \intval($idEngine));
    
            $oldPozice = null;
            if(\count($data) > 0){
                $domain = $this->getWebDomain($id);
                $this->insertDataCopy($data);
                foreach ($data as $row){
                    
                    if($oldPozice === null){
                        if($row->es_test_domain === $domain){
                            $oldPozice = $row->es_test_position;
                        }
                    }
                }
                $this->deleteOldData($idKeyword, $idEngine);
            }
            $this->updateKeywordDate($idKeyword, $date);

            return array("pozice" => $oldPozice, "url" => $domain);

        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function insertDataCopy($data_copy){
        try{

            $sqlPrefix = "INSERT INTO elpod_seo_archive VALUES ";
            $count = 0;
            foreach ($data_copy as $row){
                if($count > 0){
                    $sqlPrefix .= ",";
                }
                if($row->es_test_url === null){
                    $row->es_test_url = "NULL";
                }
                $sqlPrefix .= "(". $row->es_test_word_id .", " . $row->es_test_search_engine_id . ", "
                        . $row->es_test_position. ", " .$row->es_test_url . ", '" . $row->es_test_domain .
                        "' ,'" . $row->es_test_date . "' )";
                $count++;
            }
            $this->connection->query($sqlPrefix);

        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function deleteOldData($idKeyword, $idEngine){
        try{
            $this->connection->query("DELETE FROM elpod_seo_test WHERE es_test_word_id = %i", \intval($idKeyword), " AND
                es_test_search_engine_id = %i", \intval($idEngine));
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function getTestEntity($keyword){
        try{
            return $this->connection->fetch("SELECT es_word_id, es_word_last_test FROM elpod_seo_word WHERE es_word_word=%s", $keyword);
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function getWordWeb($idWeb, $idWord){
        try{
            return $this->connection->fetch("select * from elpod_seo_web_word where es_web_word_web_id = %i", $idWeb, " and es_web_word_word_id = %i", $idWord);
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function insertWordWeb($idWeb, $idWord){
        try{
            return $this->connection->query("INSERT INTO elpod_seo_web_word (es_web_word_web_id, es_web_word_word_id) VALUES(%i", $idWeb, ", %i", $idWord, ")");
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function updateKeywordDate($idKeyword, $date){
        try{

            $this->connection->query("UPDATE elpod_seo_word SET es_word_last_test = %t", $date , " WHERE es_word_id = %i", \intval($idKeyword));
        }catch(Exception $exception){
            throw $exception;
        }
    }

    public function getWebDomain($id){
        try{
            return $this->connection->fetch("SELECT es_web_url FROM elpod_seo_web WHERE es_web_id = %i", \intval($id))->es_web_url;
        }catch(Exception $exception){

        }
    }
    
    public function getCountTests($id_keyword, $id_engine){
        try {
            \Nette\Debug::fireLog($id_keyword . "  " . $id_engine);
            return $this->connection->query("SELECT count(*) AS count FROM elpod_seo_test WHERE es_test_word_id = %i", \intval($id_keyword),
                    " AND es_test_search_engine_id = %i", $id_engine)->fetch()->count;
        } catch (\DibiException $exc) {
            throw $exc;
        }
    }
    
    
    
}
?>

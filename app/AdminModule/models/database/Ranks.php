<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ranks
 *
 * @author WoSSa
 */
namespace AdminModule\models\database;

class Ranks extends \BaseModel{
    
    public static function checkRanks($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_web_id, es_ranks_date FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function insertRanks($id, $seznam, $google, $bing, $backlinks, $alexa, $wiki, $archive){
        try{
            \dibi::begin();
            self::$defaultConnection->query("LOCK TABLES elpod_seo_ranks WRITE, elpod_seo_archive_ranks WRITE");
            $update = self::$defaultConnection->fetch("SELECT es_ranks_web_id FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
            
            if($update){
                $results = self::$defaultConnection->fetch("SELECT * FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);                

                self::$defaultConnection->query("INSERT INTO elpod_seo_archive_ranks (es_ranks_archive_web_id, es_ranks_archive_google_pagerank,
                    es_ranks_archive_seznam_rank, es_ranks_archive_pop_seznam, es_ranks_archive_pop_google, es_ranks_archive_pop_bing,
                    es_ranks_archive_ind_seznam, es_ranks_archive_ind_google, es_ranks_archive_ind_bing, es_ranks_archive_wiki_backlinks,
                    es_ranks_archive_backlinks, es_ranks_archive_alexa, es_ranks_archive_date) VALUES (%i",$results->es_ranks_web_id,",%i",$results->es_ranks_google_pagerank,",%i",
                    $results->es_ranks_seznam_rank,",%i",$results->es_ranks_pop_seznam,",%i",$results->es_ranks_pop_google,",%i",$results->es_ranks_pop_bing,"
                    ,%i",$results->es_ranks_ind_seznam,",%i",$results->es_ranks_ind_google,",%i",$results->es_ranks_ind_bing,",%i",$results->es_ranks_wiki_backlinks,"
                    ,%i",$results->es_ranks_backlinks,",%i",$results->es_ranks_alexa,",%t",$results->es_ranks_date,")");

                self::$defaultConnection->query("DELETE FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
            }

            self::$defaultConnection->query("INSERT INTO elpod_seo_ranks (es_ranks_web_id, es_ranks_google_pagerank, es_ranks_seznam_rank,
                es_ranks_pop_seznam, es_ranks_pop_google, es_ranks_pop_bing, es_ranks_ind_seznam, es_ranks_ind_google, es_ranks_ind_bing,
                es_ranks_wiki_backlinks, es_ranks_backlinks,es_ranks_alexa)
                VALUES(%i", $id ,",%i", $google["pageRank"] ,",%i", $seznam["sRank"] ,",
                %i", $seznam["popularity"] ,",%i", $google["popularity"] ,",%i", $bing["popularity"] ,",%i", $seznam["indexPages"] ,",
                    %i", $google["indexPages"] ,",%i", $bing["indexPages"] ,", %i", $wiki ,", %i", $backlinks ,",%i", $alexa ,")");
            self::$defaultConnection->query("UNLOCK TABLES");
            \dibi::commit();
        }catch(\DibiDriverException $exception){
            \dibi::commit();
            throw $exception;
        }
    }

    public static function fetchRanks($id){
        try{
            return self::$defaultConnection->fetch("SELECT * FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchPageRank($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_google_pagerank, es_ranks_date FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchPageRankArchive($id){
        try{
            return self::$defaultConnection->fetchAll("SELECT es_ranks_archive_google_pagerank, es_ranks_archive_date FROM elpod_seo_archive_ranks
                WHERE es_ranks_archive_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchSeznamRank($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_seznam_rank, es_ranks_date FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchSeznamRankArchive($id){
        try{
            return self::$defaultConnection->fetchAll("SELECT es_ranks_archive_seznam_rank, es_ranks_archive_date FROM elpod_seo_archive_ranks
                WHERE es_ranks_archive_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchAlexa($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_alexa, es_ranks_date FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchAlexaArchive($id){
        try{
            return self::$defaultConnection->fetchAll("SELECT es_ranks_archive_alexa, es_ranks_archive_date FROM elpod_seo_archive_ranks
                WHERE es_ranks_archive_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchBacklinks($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_backlinks, es_ranks_date FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchBacklinksArchive($id){
        try{
            return self::$defaultConnection->fetchAll("SELECT es_ranks_archive_backlinks, es_ranks_archive_date FROM elpod_seo_archive_ranks
                WHERE es_ranks_archive_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchEnginesPop($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_pop_seznam, es_ranks_pop_google, es_ranks_pop_bing, es_ranks_date
                FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchEnginesPopArchive($id){
        try{
            return self::$defaultConnection->fetchAll("SELECT es_ranks_archive_pop_seznam, es_ranks_archive_pop_google, es_ranks_archive_pop_bing,
                es_ranks_archive_date FROM elpod_seo_archive_ranks
                WHERE es_ranks_archive_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchEnginesInd($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_ind_seznam, es_ranks_ind_google, es_ranks_ind_bing, es_ranks_date
                FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchEnginesIndArchive($id){
        try{
            return self::$defaultConnection->fetchAll("SELECT es_ranks_archive_ind_seznam, es_ranks_archive_ind_google, es_ranks_archive_ind_bing,
                es_ranks_archive_date FROM elpod_seo_archive_ranks
                WHERE es_ranks_archive_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchBacklinksWiki($id){
        try{
            return self::$defaultConnection->fetch("SELECT es_ranks_wiki_backlinks, es_ranks_date FROM elpod_seo_ranks WHERE es_ranks_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

    public static function fetchBacklinksWikiArchive($id){
        try{
            return self::$defaultConnection->fetchAll("SELECT es_ranks_archive_wiki_backlinks, es_ranks_archive_date FROM elpod_seo_archive_ranks
                WHERE es_ranks_archive_web_id = %i", $id);
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

}
?>

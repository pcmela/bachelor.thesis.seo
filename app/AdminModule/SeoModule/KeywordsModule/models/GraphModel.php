<?php

namespace AdminModule\SeoModule\KeywordsModule;

class GraphModel extends \BaseModel{

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * @return array
     */
    public function getEngines(){
        try{
            return $this->connection->fetchAll("SELECT es_search_engine_id, es_search_engine_name FROM elpod_seo_search_engine");
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    /**
     *
     * @param int $webId
     * @return DibiResult
     */
    public function getWebUrl($webId){
        try{
            return $this->connection->fetch("SELECT es_web_url FROM elpod_seo_web WHERE es_web_id = %i", \intval($webId))->es_web_url;
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    /**
     *
     * @param string $word
     * @param int $engine_id
     * @param string $web_domain
     * @return array
     */
    public function getEngineResultsArchive($word, $engine_id, $web_domain){
        try{

            return $this->connection->fetchAll("select archive1.es_archive_date as date, min(archive2.es_archive_position) as position from elpod_seo_word as word

                join elpod_seo_archive as archive1
                on archive1.es_archive_word_id = word.es_word_id and archive1.es_archive_search_engine_id = %i",$engine_id," and word.es_word_word = %s",$word,"

                left join elpod_seo_archive as archive2 ON word.es_word_id = archive2.es_archive_word_id and archive2.es_archive_search_engine_id = ",$engine_id,"
                and archive1.es_archive_date = archive2.es_archive_date and archive2.es_archive_domain = %s ",$web_domain," and word.es_word_word = %s",$word,"
                    group by archive1.es_archive_date");
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    /**
     *
     * @param string $word
     * @return DibiResult
     */
    public function getLastUpdateWord($word){
        try{
            
            return $this->connection->fetch("SELECT es_word_last_test FROM elpod_seo_word WHERE es_word_word = %s", $word)->es_word_last_test;
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    /**
     *
     * @param int $wordId
     * @param string $web_domain
     * @return array
     */
    public function getEngineResultsArchiveOverAll($wordId, $web_domain){
        try{
            
            $this->connection->test("select es_archive_word_id, es_archive_search_engine_id, min(es_archive_position) as position, es_archive_date
                from elpod_seo_archive where es_archive_word_id = %i",$wordId," and es_archive_domain = %s",$web_domain,"
                group by es_archive_search_engine_id, es_archive_date");

            return $this->connection->fetchAll("select es_archive_word_id, es_archive_search_engine_id, min(es_archive_position) as position, es_archive_date
                from elpod_seo_archive where es_archive_word_id = %i",$wordId," and es_archive_domain = %s",$web_domain,"
                group by es_archive_search_engine_id, es_archive_date");
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    public function getEngineResultsArchiveOverAllv2($webId, $web_domain){
        try{
            return $this->connection->fetchAll("select word.es_word_id as word_id, archive.es_archive_position as position, archive.es_archive_search_engine_id, archive.es_archive_date, web_word.es_web_word_weight as weight from elpod_seo_web as web
                join elpod_seo_web_word as web_word on web.es_web_id = web_word.es_web_word_web_id and web.es_web_id = %i",$webId,"
                join elpod_seo_word as word on web_word.es_web_word_word_id = word.es_word_id
                join elpod_seo_archive as archive on archive.es_archive_word_id = word.es_word_id and archive.es_archive_domain = %s",$web_domain,"
                group by archive.es_archive_date, archive.es_archive_search_engine_id, word.es_word_id");
        }catch(DibiDriverException $exception){
            throw $exception;
        }
    }

    

}
?>

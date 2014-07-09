<?php

namespace AdminModule\SeoModule\KeywordsModule;

/**
 * ConcurrencyModel - model pro práci s konkurencí webu
 */
class ConcurrencyModel extends \BaseModel {

    /**
     *
     * @param int $id
     * @return array
     */
    public function getWebWords($id) {
        try {
            return $this->connection->fetchAll("select word.es_web_word_word_id from elpod_seo_web AS web JOIN elpod_seo_web_word AS word ON web.es_web_id = word.es_web_word_web_id AND web.es_web_id = %i", $id);
        } catch (DibiException $exception) {
            throw $exception;
        }
    }

    /**
     *
     * @param int $id
     * @return array
     *
     */
    public function getTestResults($id) {
        try {
            return $this->connection->fetchAll("select DISTINCT es_test_domain, es_test_search_engine_id from elpod_seo_test where es_test_word_id = %i ", $id, " AND es_test_position < 21");
        } catch (DibiException $exception) {
            throw $exception;
        }
    }

    /**
     *
     * @param string $sql
     * @return array
     */
    public function fetchSql($sql) {
        try {
            //$this->connection->test($sql);
            return $this->connection->fetchAll($sql);
        } catch (DibiException $exception) {
            throw $exception;
        }
    }

    /**
     *
     * @param int $web
     * @param int $concurrency
     */
    public function insertConcurrency($web, $concurrency) {
        try {
            $this->connection->query("INSERT INTO elpod_seo_concurrency (es_concurrency_web_id, es_concurrency_domain) VALUES
                (%i", $web, ", %s ", $concurrency, ")");
        } catch (DibiException $exception) {
            throw $exception;
        }
    }

    /**
     *
     * @param int $web
     * @return array
     */
    public function currentConcurrency($web){
        try{
            return $this->connection->fetchAll("SELECT es_concurrency_domain FROM elpod_seo_concurrency WHERE
                es_concurrency_web_id = %i", $web);
        }catch(DibiException $exception){
            throw $exception;
        }
    }

    /**
     *
     * @param string $domain
     * @param int $id
     */
    public function deleteConcurency($domain, $id){
        try{
            $this->connection->query("DELETE FROM elpod_seo_concurrency WHERE
                es_concurrency_web_id = %i", $id, " AND es_concurrency_domain = %s", $domain);
        }catch(DibiException $exception){
            throw $exception;
        }
    }

    /**
     *
     * @param int $id
     * @param string $domain
     */
    public function addWebConcurrency($id, $domain){
        try{
            $exception = new \DibiDriverException();
            $this->connection->query("INSERT INTO elpod_seo_concurrency (es_concurrency_web_id, es_concurrency_domain)
                VALUES (%i",$id,", %s",$domain,")");
        }catch(\DibiDriverException $exception){
            throw $exception;
        }
    }

}
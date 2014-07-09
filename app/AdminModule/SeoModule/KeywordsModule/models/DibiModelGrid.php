<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DibiModelGrid
 *
 * @author WoSSa
 */

namespace AdminModule\SeoModule\KeywordsModule;

class DibiModelGrid extends \BaseModel {


    private $seznamSql;
    private $googleSql;
    private $bingSql;
    private $centrumSql;
    private $jyxoSql;

    /**
     *
     * @param int $id
     * @param bool $concurrency
     */
    public function __construct($id, $concurrency = null) {
        parent::__construct();
        if($concurrency === null){
//            $this->fluent = $this->connection->select("*")->from("elpod_view_seo_overview_all")->where("web_id = %i", $id);
            $this->initSqlQueries($id);
            $this->fluent = $this->connection->select("*")->from("(select `seznam`.`web_id` AS `web_id`,`seznam`.`word` AS `word`,`seznam`.`position` AS `position_seznam`,`seznam`.`old_position` AS `old_position_seznam`,`google`.`position` AS `position_google`,`google`.`old_position` AS `old_position_google`,`bing`.`position` AS `position_bing`,`bing`.`old_position` AS `old_position_bing`,`centrum`.`position` AS `position_centrum`,`centrum`.`old_position` AS `old_position_centrum`,`jyxo`.`position` AS `position_jyxo`,`jyxo`.`old_position` AS `old_position_jyxo`
            from ((((
            (".$this->seznamSql.")
             `seznam` join
            (".$this->googleSql.")
             `google` on(((`seznam`.`word` = `google`.`word`) and (`seznam`.`web_id` = `google`.`web_id`))))
            join
            (".$this->bingSql.")
             `bing` on(((`google`.`word` = `bing`.`word`) and (`google`.`web_id` = `bing`.`web_id`))))
            join
            (".$this->centrumSql.")
             `centrum` on(((`centrum`.`word` = `bing`.`word`) and (`centrum`.`web_id` = `bing`.`web_id`))))
            join
            (".$this->jyxoSql.")
             `jyxo` on(((`jyxo`.`word` = `centrum`.`word`) and (`jyxo`.`web_id` = `centrum`.`web_id`))))
            group by `seznam`.`web_id`,`seznam`.`word`) `table`");
        }else{
            $this->initSqlQueriesConcurrency($id, $concurrency);
            $this->fluent = $this->connection->select("*")->from("(select `seznam`.`web_id` AS `web_id`,`seznam`.`word` AS `word`,`seznam`.`position` AS `position_seznam`,`seznam`.`old_position` AS `old_position_seznam`,`google`.`position` AS `position_google`,`google`.`old_position` AS `old_position_google`,`bing`.`position` AS `position_bing`,`bing`.`old_position` AS `old_position_bing`,`centrum`.`position` AS `position_centrum`,`centrum`.`old_position` AS `old_position_centrum`,`jyxo`.`position` AS `position_jyxo`,`jyxo`.`old_position` AS `old_position_jyxo`
            from ((((
            (".$this->seznamSql.")
             `seznam` join
            (".$this->googleSql.")
             `google` on(((`seznam`.`word` = `google`.`word`) and (`seznam`.`web_id` = `google`.`web_id`))))
            join
            (".$this->bingSql.")
             `bing` on(((`google`.`word` = `bing`.`word`) and (`google`.`web_id` = `bing`.`web_id`))))
            join
            (".$this->centrumSql.")
             `centrum` on(((`centrum`.`word` = `bing`.`word`) and (`centrum`.`web_id` = `bing`.`web_id`))))
            join
            (".$this->jyxoSql.")
             `jyxo` on(((`jyxo`.`word` = `centrum`.`word`) and (`jyxo`.`web_id` = `centrum`.`web_id`))))
            group by `seznam`.`web_id`,`seznam`.`word`) `table`");

            

            
        }


    }

    private function initSqlQueriesConcurrency($web, $domain){
        $this->seznamSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((`elpod_seo_web` join elpod_seo_concurrency on elpod_seo_web.es_web_id = elpod_seo_concurrency.es_concurrency_web_id and elpod_seo_web.es_web_id = ".  \intval($web)." and elpod_seo_concurrency.es_concurrency_domain = '".\mysql_escape_string($domain)."' join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`))) join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 1) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_concurrency`.`es_concurrency_domain`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";

        $this->googleSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((`elpod_seo_web` join elpod_seo_concurrency on elpod_seo_web.es_web_id = elpod_seo_concurrency.es_concurrency_web_id and elpod_seo_web.es_web_id = ".  \intval($web)." and elpod_seo_concurrency.es_concurrency_domain = '".\mysql_escape_string($domain)."' join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`))) join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 2) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_concurrency`.`es_concurrency_domain`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";


        $this->bingSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((`elpod_seo_web` join elpod_seo_concurrency on elpod_seo_web.es_web_id = elpod_seo_concurrency.es_concurrency_web_id and elpod_seo_web.es_web_id = ".  \intval($web)." and elpod_seo_concurrency.es_concurrency_domain = '".\mysql_escape_string($domain)."' join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`))) join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 3) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_concurrency`.`es_concurrency_domain`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";


        $this->centrumSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((`elpod_seo_web` join elpod_seo_concurrency on elpod_seo_web.es_web_id = elpod_seo_concurrency.es_concurrency_web_id and elpod_seo_web.es_web_id = ".  \intval($web)." and elpod_seo_concurrency.es_concurrency_domain = '".\mysql_escape_string($domain)."' join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`))) join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 4) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_concurrency`.`es_concurrency_domain`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";


        $this->jyxoSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((`elpod_seo_web` join elpod_seo_concurrency on elpod_seo_web.es_web_id = elpod_seo_concurrency.es_concurrency_web_id and elpod_seo_web.es_web_id = ".  \intval($web)." and elpod_seo_concurrency.es_concurrency_domain = '".\mysql_escape_string($domain)."' join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`))) join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 5) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_concurrency`.`es_concurrency_domain`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";
    }

    private function initSqlQueries($web){
        $this->seznamSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((
            `elpod_seo_web`
            join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`) and (elpod_seo_web.es_web_id = ".\intval($web).")))
            join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 1) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_web`.`es_web_url`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";

        $this->googleSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((
            `elpod_seo_web`
            join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`) and (elpod_seo_web.es_web_id = ".\intval($web).")))
            join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 2) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_web`.`es_web_url`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";


        $this->bingSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((
            `elpod_seo_web`
            join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`) and (elpod_seo_web.es_web_id = ".\intval($web).")))
            join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 3) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_web`.`es_web_url`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";


        $this->centrumSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((
            `elpod_seo_web`
            join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`) and (elpod_seo_web.es_web_id = ".\intval($web).")))
            join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 4) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_web`.`es_web_url`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";


        $this->jyxoSql =
            "select `elpod_seo_web`.`es_web_id` AS `web_id`, `elpod_seo_word`.`es_word_word` AS `word`,`elpod_seo_word`.`es_word_last_test` AS `word_latest`,`elpod_seo_test`.`es_test_position` AS `position`,`elpod_seo_test`.`es_test_old_position` AS `old_position`
            from ((((
            `elpod_seo_web`
            join `elpod_seo_web_word` on((`elpod_seo_web`.`es_web_id` = `elpod_seo_web_word`.`es_web_word_web_id`) and (elpod_seo_web.es_web_id = ".\intval($web).")))
            join `elpod_seo_word` on((`elpod_seo_web_word`.`es_web_word_word_id` = `elpod_seo_word`.`es_word_id`)))
            left join `elpod_seo_test` on(((`elpod_seo_word`.`es_word_id` = `elpod_seo_test`.`es_test_word_id`) and (`elpod_seo_test`.`es_test_search_engine_id` = 5) and (`elpod_seo_test`.`es_test_domain` = `elpod_seo_web`.`es_web_url`)))))
            group by `elpod_seo_test`.`es_test_search_engine_id`,`elpod_seo_word`.`es_word_word`,`elpod_seo_web`.`es_web_id`";
    }

    public function filterSearch($search) {
        $searchString = "%$search%";
        $this->fluent->where("word like %s", $searchString);
    }
    

}

?>

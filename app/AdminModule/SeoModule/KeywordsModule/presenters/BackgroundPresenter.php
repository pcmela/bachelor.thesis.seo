<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BackgroundPresenter
 *
 * @author wossa
 */
namespace AdminModule\SeoModule\KeywordsModule;

class BackgroundPresenter extends \BasePresenter{


    public function actionDefault($words, $seznamData, $webId){

        $request = \Nette\Environment::getApplication()->getPresenter()->getRequest();

        if($request->isPost()){

            $post = $request->getPost();

            if($post["seznamData"] !== "null"){
                $cache = \Nette\Environment::getCache();
                $cachedData = $cache[$post["seznamData"]];
                unset($cache[$post["seznamData"]]);

                $this->searchKwAjax(\unserialize($post["words"]), $cachedData, \unserialize($post["webId"]));
            }else{
                $this->searchKwAjax(\unserialize($post["words"]), null, \unserialize($post["webId"]));
            }
        }
        exit ();


    }


    public function searchKwAjax($words, $seznamData, $webId) {
        $cache = \Nette\Environment::getCache();
        $cacheTemp = \Nette\Environment::getCache('Nette.Template.Cache');
        $cache->save("web".$webId, false);
        $modelSeznam = new \Components\Seo\SearchEngine\FindKeywordsSeznam($words, $webId);
        $modelGoogle = new \Components\Seo\SearchEngine\FindKeywordsGoogle($words, $webId);
        $modelBing = new \Components\Seo\SearchEngine\FindKeywordsBing($words, $webId);
        $modelCentrum = new \Components\Seo\SearchEngine\FindKeywordsCentrum($words, $webId);
        $modelJyxo = new \Components\Seo\SearchEngine\FindKeywordsJyxo($words, $webId);

        //try{
            \dibi::begin();
            $modelSeznam = $modelSeznam->getPositionKeywords($webId, true, $seznamData);
            $modelGoogle = $modelGoogle->getPositionKeywords($webId);
            $modelBing = $modelBing->getPositionKeywords($webId);
            $modelCentrum = $modelCentrum->getPositionKeywords($webId);
            $modelJyxo = $modelJyxo->getPositionKeywords($webId);
            \dibi::commit();
        //}catch(\Exception $exception){
            unset ($cache["web".$webId]);
            $cacheTemp->clean(array(\Nette\Caching\Cache::TAGS => array("grid$webId")));
            //\Nette\Debug::log($exception->getMessage());
        //}
        if(isset ($cache["web".$webId])){
            unset ($cache["web".$webId]);
        }
        $cacheTemp = \Nette\Environment::getCache('Nette.Template.Cache');
        $cacheTemp->clean(array(\Nette\Caching\Cache::TAGS => array("grid$webId")));
    }
}
?>

<?php

/**
 * Description of PrintGraphPresenter
 *
 * @author wossa
 */
namespace AdminModule\SeoModule\KeywordsModule;

require_once(LIBS_DIR.'/jpgraph/src/jpgraph.php');
require_once(LIBS_DIR.'/jpgraph/src/jpgraph_line.php');

class PrintGraphPresenter extends \BasePresenter {

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    /**
     *
     * @var GraphModel
     */
    private $graphModel;

//    protected function startup() {
//        parent::startup();
//    }

    private function getGraphModel(){
        if($this->graphModel === null){
            return new GraphModel();
        }

        return $this->graphModel;
    }

    public function actionDefault($engineId, $word, $webName, $lastPosition, $engineName){
        

        $lastDate = $this->getGraphModel()->getLastUpdateWord($word);
        $archiveResult = $this->getGraphModel()->getEngineResultsArchive($word, $engineId, $webName);
        $resultDate = array();
        $resultPosition = array();

        //\dump($archiveResult);

        foreach ($archiveResult as $row){
            $resultDate[] = $row->date;
            $resultPosition[] = $row->position;
        }

        $resultDate[] = $lastDate;
        if($lastPosition!==""){
            $resultPosition[] = $lastPosition;
        }

        $resultDate = $this->validateDate($resultDate);
        //\dump($resultDate);

        $this->initGraph($resultDate, $resultPosition, $engineName);

        exit();
    }


    private function initGraph($dataDate, $dataPosition, $name){
        //$datay1 = array(1,20, null, 3);

//        \dump($dataDate);
//        \dump($dataPostion);
        if(\count($dataPosition) > 0){
            $graph = new \Graph(862,350);
            $graph->SetScale("textlin");

            $theme_class= new \UniversalTheme;
            $graph->SetTheme($theme_class);

            $graph->title->Set($name);
            $graph->SetBox(false);

            $graph->yaxis->HideZeroLabel();

            $graph->xaxis->SetTickLabels($dataDate);
            $graph->ygrid->SetFill(false);

            if(\is_array($dataPosition)){
                $p1 = new \LinePlot($dataPosition);
            }else{
                $p1 = new \LinePlot(array(null));
            }
            $graph->Add($p1);

            $p1->SetColor("#55bbdd");
            $p1->mark->SetType(MARK_FILLEDCIRCLE,'',1.0);
            $p1->mark->SetColor('#55bbdd');
            $p1->mark->SetFillColor('#55bbdd');
            $p1->SetCenter();

            $graph->Stroke();
        }

    }

    private function validateDate($arrayDate){
        //\dump(count($arrayDate));
        for($i = 0; $i < count($arrayDate); $i++){
            if($arrayDate[$i] !== null){
                $date = $arrayDate[$i];
                //\dump("--- a ---");
                $date = \explode(" ", $date);
                //\dump($date);
                $arrayDate[$i] = trim($date[0]);
            }
        }

        return $arrayDate;
    }

    public function renderDefault(){

    }
}
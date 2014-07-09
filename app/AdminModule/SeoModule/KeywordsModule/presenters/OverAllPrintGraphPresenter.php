<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OverAllPrintGraph
 *
 * @author WoSSa
 */
namespace AdminModule\SeoModule\KeywordsModule;

require_once(LIBS_DIR.'/jpgraph/src/jpgraph.php');
require_once(LIBS_DIR.'/jpgraph/src/jpgraph_line.php');

class OverAllPrintGraphPresenter extends \BasePresenter{

    private $key;
    private $type;
    private $cacheData;

    /**
     *  $type:  1 = seznam.cz
     *          2 = google.cz
     *          3 = seznam.cz + google.cz
     *          4 = all engines
     * @param string $key
     * @param string $type 
     */
    public function actionDefault($key, $type){
        $this->key = $key;
        $this->type = $type;
        $cache = \Nette\Environment::getCache();
        $this->cacheData = $cache[$key];

        $this->process();
    }

    private function process(){
        if($this->type == 1){
            $seznamDate = array();
            $seznamPosition = array();

            foreach ($this->cacheData as $data){
                $arrayDate = array();
                $arrayPosition = array();
                foreach ($data->getSumSeznam() as $key => $value){
                    $arrayDate[] = $key;
                    $arrayPosition[] = $value;
                }

                $seznamDate = \array_merge($arrayDate, $seznamDate);
                $seznamPosition[$data->getName()] = $arrayPosition;
            }

            $seznamDate = \array_unique($seznamDate);

            $this->printGrahp($seznamDate, $seznamPosition, "Seznam.cz");


        }else if($this->type == 2){
            $googleDate = array();
            $googlePosition = array();

            foreach ($this->cacheData as $data){
                $arrayDate = array();
                $arrayPosition = array();
                foreach ($data->getSumGoogle() as $key => $value){
                    $arrayDate[] = $key;
                    $arrayPosition[] = $value;
                }

                $googleDate = \array_merge($arrayDate, $googleDate);
                $googlePosition[$data->getName()] = $arrayPosition;
            }

            $googleDate = \array_unique($googleDate);

            $this->printGrahp($googleDate, $googlePosition, "Google.cz");
        }else if($this->type == 3){
            $googleDate = array();
            $googlePosition = array();

            foreach ($this->cacheData as $data){
                $arrayDate = array();
                $arrayPosition = array();
                foreach ($data->getSumSeznamGoogle() as $key => $value){
                    $arrayDate[] = $key;
                    $arrayPosition[] = $value;
                }

                $googleDate = \array_merge($arrayDate, $googleDate);
                $googlePosition[$data->getName()] = $arrayPosition;
            }

            $googleDate = \array_unique($googleDate);

            $this->printGrahp($googleDate, $googlePosition, "Seznam.cz, Google.cz");
        }else if($this->type == 4){
            $googleDate = array();
            $googlePosition = array();

            foreach ($this->cacheData as $data){
                $arrayDate = array();
                $arrayPosition = array();
                foreach ($data->getSumAll() as $key => $value){
                    $arrayDate[] = $key;
                    $arrayPosition[] = $value;
                }

                $googleDate = \array_merge($arrayDate, $googleDate);
                $googlePosition[$data->getName()] = $arrayPosition;
            }

            $googleDate = \array_unique($googleDate);

            $this->printGrahp($googleDate, $googlePosition, "Seznam.cz, Google.cz, Bing.com, Centrum.cz a Jyxo.cz");
        }
    }

    private function printGrahp($dataDate, $dataPosition, $name) {

        $arrayColor = array(
            0 => "#800080",
            1 => "#FF00FF",
            2 => "#000080",
            3 => "#0000FF",
            4 => "#008080",
            5 => "#00FFFF",
            6 => "#008000",
            7 => "#00FF00",
            8 => "#808000",
            9 => "#FFFF00",
            10 => "#800000",
            11 => "#FF0000",
            12 => "#000000",
            13 => "#808080",
            14 => "#C0C0C0",
            15 => "#6495ED",
            16 => "#B22222",
            17 => "#55bbdd"
        );

        //\dump($data);
        $graph = new \Graph(862, 650);
        $graph->SetScale("textlin");

        $theme_class = new \UniversalTheme;
        $graph->SetTheme($theme_class);

        $graph->title->Set($name);
        $graph->SetBox(false);

        $graph->yaxis->HideZeroLabel();


        $graph->xaxis->SetTickLabels($dataDate);
        $graph->ygrid->SetFill(false);

        $i = 0;
        foreach ($dataPosition as $key => $value){
            $p1 = new \LinePlot($value);
            
            $p1->SetColor($arrayColor[$i]);
            $p1->SetLegend($key);

            $p1->SetColor($arrayColor[$i]);
            $p1->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
            $p1->mark->SetColor($arrayColor[$i]);
            $p1->mark->SetFillColor($arrayColor[$i]);
            $p1->SetCenter();
            
            $graph->Add($p1);
            $i++;
        }

//        $p1 = new \LinePlot($data["seznam"]);
//        $graph->Add($p1);
//        $p1->SetColor("#6495ED");
//        $p1->SetLegend('Seznam.cz');
//
//        // Create the second line
//        $p2 = new \LinePlot($data["google"]);
//        $graph->Add($p2);
//        $p2->SetColor("#B22222");
//        $p2->SetLegend('Google.cz');
//
//        // Create the third line
//        $p3 = new \LinePlot($data["bing"]);
//        $graph->Add($p3);
//        $p3->SetColor("#FF1493");
//        $p3->SetLegend('Bing.com');
//
//        $p1->SetColor("#55bbdd");
//        $p1->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
//        $p1->mark->SetColor('#55bbdd');
//        $p1->mark->SetFillColor('#55bbdd');
//        $p1->SetCenter();
//
//        $p2->SetColor("#B22222");
//        $p2->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
//        $p2->mark->SetColor('#B22222');
//        $p2->mark->SetFillColor('#B22222');
//        $p2->SetCenter();
//
//        $p3->SetColor("#FF1493");
//        $p3->mark->SetType(MARK_FILLEDCIRCLE, '', 1.0);
//        $p3->mark->SetColor('#FF1493');
//        $p3->mark->SetFillColor('#FF1493');
//        $p3->SetCenter();

        $graph->Stroke();
        exit;
    }

}
?>

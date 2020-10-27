<?php
/**
 * Created by PhpStorm.
 * User: Akram
 * Date: 14/07/2019
 * Time: 02:38
 */

namespace App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine;

use App\Controller\Statistique\SubElements\StatistiqueSubElement;
use App\Model\Post;

class EvolutionDossiersAdhoc extends StatistiqueSubElement
{


    public function __construct()
    {


    }

    public function render()
    {

        $this->EvolutionDossiersAdhoc();

    }

    public function EvolutionDossiersAdhoc()
    {

        $canvas = $this->_getEvolutionDossiersAdhocCanvas();

        $this->renderCanvas($canvas);
    }

    public function _getEvolutionDossiersAdhocCanvas()
    {

        $posts = Post::_getLast3YearsOfData(Post::$postTypes[2]);

        $canvasInfo = array(
            "id" => "evolutionDossiersAdhoc",
            "title" => __("Evolution des dossiers Ad-hoc", "auess-import-export-stat")
        );

        return $this->_getEvolutionDossiersAdhocData($posts, $canvasInfo);

    }

    public function _getEvolutionDossiersAdhocData($posts, $canvasInfo)
    {


        $statisticData = array();

        foreach ($posts as $row) {

            $year = \DateTime::createFromFormat('d/m/Y', $row['wpcf-date-de-la-commission'])->format('Y');

            $statisticData[$year] = isset($statisticData[$year]) ? $statisticData[$year] + 1 : 1;
        }

        $data = array();

        foreach ($statisticData as $year => $value) {
            $data['years'][] = $year;
        }
        foreach ($statisticData as $year => $value) {
            $data[__("Dossiers Ad-hoc", "auess-import-export-stat")][] = $statisticData[$year];
        }


        $canvas = array(
            "id" => $canvasInfo['id'],
            "labels" => isset($data['years']) ? $data['years'] : array(),
            "title" => $canvasInfo['title'],
            "xAxesLabel" => __("Année", "auess-import-export-stat"),
            "yAxesLabel" => __("Nombre de dossiers", "auess-import-export-stat")
        );

        $datasetsNames = array(__("Dossiers Ad-hoc", "auess-import-export-stat"));

        for ($i = 0; $i < count($datasetsNames); $i++) {
            $canvas["datasets"][] = array(
                "label" => $datasetsNames[$i],
                "backgroundColor" => $this->colorNames[$i],
                "borderColor" => $this->colorNames[$i],
                "data" => isset($data[$datasetsNames[$i]]) ? $data[$datasetsNames[$i]] : array(),
            );
        }


        return $canvas;
    }
}
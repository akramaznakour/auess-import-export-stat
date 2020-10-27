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

class EvolutionInstruction extends StatistiqueSubElement
{

    public function __construct()
    {
    }

    public function render()
    {
        $this->EvolutionInstruction();
    }

    public function EvolutionInstruction()
    {
        $canvas = $this->_getEvolutionInstructionCanvas();

        $this->renderCanvas($canvas);
    }

    public function _getEvolutionInstructionCanvas()
    {

        $posts = Post::_getLast3YearsOfData();

        $canvasInfo = array(
            "id" => "evolutionInstruction",
            "title" => __("Evolution des dossiers d\' instruction", "auess-import-export-stat")
        );

        return $this->_getEvolutionInstructionData($posts, $canvasInfo);

    }
    

    public function _getEvolutionInstructionData($posts, $canvasInfo)
    {


        $statisticData = array();


        foreach ($posts as $row) {

            $year = \DateTime::createFromFormat('d/m/Y', $row['wpcf-date-de-la-commission'])->format('My');
            $avis = $row['wpcf-avis-de-la-commission'];

            $statisticData[$year][__("Instructions", "auess-import-export-stat")] = isset($statisticData[$year][__("Instructions", "auess-import-export-stat")]) ? $statisticData[$year][__("Instructions", "auess-import-export-stat")] + 1 : 1;

            foreach (
                array(
                    __("Avis Favorable", "auess-import-export-stat") => array("fr" => "favorable", "ar" => "الموافقة"),
                    __("Avis Défavorable", "auess-import-export-stat") => array("fr" => "défavorable", "ar" => "الرفض"),
                    __("Ajourné", "auess-import-export-stat") => array("fr" => "ajourné", "ar" => "تأجيل"),
                ) as $key => $value
            ) {
                if ($avis == $value["fr"] || substr_count($avis, $value["ar"]) >= 1) {
                    $statisticData[$year][$key] = isset($statisticData[$year][$key]) ? $statisticData[$year][$key] + 1 : 1;
                }
            }

        }


        foreach ($statisticData as $key => $value) {

            $data['years'][] = $key;
        }


        $datasetsNames = array(__("Instructions", "auess-import-export-stat"), __("Avis Favorable", "auess-import-export-stat"), __("Avis Défavorable", "auess-import-export-stat"), __("Ajourné", "auess-import-export-stat"));

        foreach ($statisticData as $year => $value) {
            foreach ($datasetsNames as $element) {
                $data[$element][] = isset($statisticData[$year][$element]) ? $statisticData[$year][$element] : 0;
            }
        }

        $canvas = array(
            "id" => $canvasInfo['id'],
            "labels" => $data['years'],
            "title" => $canvasInfo['title'],
            "xAxesLabel" => __("Mois", "auess-import-export-stat"),
            "yAxesLabel" => __("Nombre de dossiers", "auess-import-export-stat")
        );


        for ($i = 0; $i < count($datasetsNames); $i++) {
            $canvas["datasets"][] = array(
                "label" => $datasetsNames[$i],
                "backgroundColor" => $this->colorNames[$i],
                "borderColor" => $this->colorNames[$i],
                "data" => $data[$datasetsNames[$i]],
            );
        }

        return $canvas;
    }
}
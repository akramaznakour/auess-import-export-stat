<?php


namespace App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine;


use App\Controller\Statistique\SubElements\StatistiqueSubElement;
use App\Model\Post;


class EvolutionAvisFavorable extends StatistiqueSubElement
{


    public function __construct()
    {


    }

    public function render($numberOfColumns = 1)
    {


        if ($numberOfColumns == 1) {
            $this->evolutionAvisPetitProjetFavorable();
            $this->evolutionAvisGrandProjetFavorable();
        } else {
            $this->twoColumnsTemplate("evolutionAvisPetitProjetFavorable", "evolutionAvisGrandProjetFavorable");
        }

    }

    public function evolutionAvisPetitProjetFavorable()
    {

        $canvas = $this->_getEvolutionAvisPetitProjetFavorableCanvas();

        $this->renderCanvas($canvas);
    }

    public function _getEvolutionAvisPetitProjetFavorableCanvas()
    {


        $posts = Post::_getLast3YearsOfPetitPorjet();

        $canvasInfo = array(
            "id" => "evolutionAvisPetitProjetFavorable",
            "title" => __("Evolution des avis favorables des petits projets ", "auess-import-export-stat")

        );

        return $this->_getEvolutionAvisFavorableData($posts, $canvasInfo);
    }


    public function evolutionAvisGrandProjetFavorable()
    {

        $canvas = $this->_getEvolutionAvisGrandProjetFavorableCanvas();

        $this->renderCanvas($canvas);

    }

    public function _getEvolutionAvisGrandProjetFavorableCanvas()
    {

        $posts = Post::_getLast3YearsOfgrandPorjet();

        $canvasInfo = array(
            "id" => "evolutionAvisGrandProjetFavorable",
            "title" => __("Evolution des avis favorables des grands projets", "auess-import-export-stat")
        );

        return $this->_getEvolutionAvisFavorableData($posts, $canvasInfo);

    }

    public function _getEvolutionAvisFavorableData($posts, $canvasInfo)
    {

        $statisticData = array();

        foreach ($posts as $row) {

            if (!isset($row["wpcf-avis-de-la-commission"])) {
                continue;
            }


            $year = \DateTime::createFromFormat('d/m/Y', $row['wpcf-date-de-la-commission'])->format('Y');
            $type = $row['wpcf-type'];


            if ($row['wpcf-avis-de-la-commission'] == "favorable" || substr_count($row['wpcf-avis-de-la-commission'], "الموافقة") >= 1) {
                $statisticData[$year][$type] = isset($statisticData[$year][$type]) ? $statisticData[$year][$type] + 1 : 1;
            }


        }

        $data = array();

        foreach ($statisticData as $year => $value) {
            $data['years'][] = $year;
        }
        foreach ($statisticData as $key => $value) {
            $data[__("Avis Favorable Urbain", "auess-import-export-stat")][] = isset($statisticData[$key][1]) ? $statisticData[$key][1] : 0;
        }
        foreach ($statisticData as $key => $value) {
            $data[__("Avis Favorable Rural", "auess-import-export-stat")][] = isset($statisticData[$key][2]) ? $statisticData[$key][2] : 0;
        }
        foreach ($statisticData as $key => $value) {
            $ur = isset($statisticData[$key][1]) ? $statisticData[$key][1] : 0;
            $ru = isset($statisticData[$key][2]) ? $statisticData[$key][2] : 0;
            $data[__("Avis Favorable", "auess-import-export-stat")][] = $ur + $ru;
        }


        $canvas = array(
            "id" => $canvasInfo['id'],
            "labels" => $data['years'],
            "title" => $canvasInfo['title'],
            "xAxesLabel" => __("Année", "auess-import-export-stat"),
            "yAxesLabel" => __("Nombre de dossiers", "auess-import-export-stat")
        );

        $datasetsNames = array(__("Avis Favorable", "auess-import-export-stat"), __("Avis Favorable Rural", "auess-import-export-stat"), __("Avis Favorable Urbain", "auess-import-export-stat"));

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
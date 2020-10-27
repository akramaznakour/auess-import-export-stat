<?php


namespace App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine;


use App\Controller\Statistique\SubElements\StatistiqueSubElement;
use App\Model\Post;

class EvolutionDossiersTraite extends StatistiqueSubElement
{


    public function __construct()
    {

    }

    public function render($numberOfColumns = 1)
    {

        if ($numberOfColumns == 1) {
            $this->evolutionDossiersPetitProjetTraites();
            $this->evolutionDossiersGrandProjetTraites();
        } else {
            $this->twoColumnsTemplate("evolutionDossiersPetitProjetTraites", "evolutionDossiersGrandProjetTraites");
        }
    }


    public function evolutionDossiersPetitProjetTraites()
    {
        $canvas = $this->_getEvolutionDossiersPetitProjetTraitesCanvas();

        $this->renderCanvas($canvas);
    }

    public function _getEvolutionDossiersPetitProjetTraitesCanvas()
    {
        $posts = Post::_getLast3YearsOfPetitPorjet();

        $canvasInfo = array(
            "id" => "evolutionDossiersPetitProjetTraites",
            "title" => __("Evolution de dossiers petit projet traités", "auess-import-export-stat", "auess-import-export-stat")

        );

        return $this->_getEvolutionDossiersTraitesData($posts, $canvasInfo);
    }


    public function evolutionDossiersGrandProjetTraites()
    {

        $canvas = $this->_getEvolutionDossiersGrandProjetTraitesCanvas();

        $this->renderCanvas($canvas);


    }

    public function _getEvolutionDossiersGrandProjetTraitesCanvas()
    {


        $posts = Post::_getLast3YearsOfgrandPorjet();

        $canvasInfo = array(
            "id" => "evolutionDossiersGrandProjetTraites",
            "title" => __("Evolution de dossiers grand projet traités", "auess-import-export-stat"),

        );

        return $this->_getEvolutionDossiersTraitesData($posts, $canvasInfo);
    }

    public function _getEvolutionDossiersTraitesData($posts, $canvasInfo)
    {

        $statisticData = array();


        foreach ($posts as $row) {

            $year = \DateTime::createFromFormat('d/m/Y', $row['wpcf-date-de-la-commission'])->format('Y');
            $type = $row['wpcf-type'];

            $statisticData[$year][$type] = isset($statisticData[$year][$type]) ? $statisticData[$year][$type] + 1 : 1;
        }

        foreach ($statisticData as $year => $value) {
            $data['years'][] = $year;
        }
        foreach ($statisticData as $year => $value) {
            $data[__("Dossier Urbain", "auess-import-export-stat")][] = isset($statisticData[$year][1]) ? $statisticData[$year][1] : 0;
        }
        foreach ($statisticData as $year => $value) {
            $data[__("Dossier Rural", "auess-import-export-stat")][] = isset($statisticData[$year][2]) ? $statisticData[$year][2] : 0;
        }
        foreach ($statisticData as $year => $value) {
            $ur = isset($statisticData[$year][1]) ? $statisticData[$year][1] : 0;
            $ru = isset($statisticData[$year][2]) ? $statisticData[$year][2] : 0;
            $data[__("Dossier", "auess-import-export-stat")][] = $ur + $ru;
        }


        $canvas = array(
            "id" => $canvasInfo['id'],
            "labels" => $data['years'],
            "title" => $canvasInfo['title'],
            "xAxesLabel" => __("Année", "auess-import-export-stat"),
            "yAxesLabel" => __("Nombre de dossiers", "auess-import-export-stat")
        );

        $datasetsNames = array(__("Dossier", "auess-import-export-stat"), __("Dossier Rural", "auess-import-export-stat"), __("Dossier Urbain", "auess-import-export-stat"));

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
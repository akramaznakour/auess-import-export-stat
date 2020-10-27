<?php

namespace App\Controller\ImportExport\SubPages;

final class GrandProjetSubPage extends ImportExportSubPageController
{

    protected $pageSlug = "auessaouira_grands_projets";
    protected $postType = "grand-projet";

    protected $valideProjectTypes = array(
        array("name" => "GPU", "id" => "3"),
        array("name" => "GPR", "id" => "4"),
    );

    protected $columnsNames = array(
        'type',
        'date de la commission',
        'intitule',
        'architecte',
        'situation du projet',
        'commune',
        'nature du projet',
        'proprietaire',
        'numero de dossier',
        'avis de la commission',
        'observation',
    );


}
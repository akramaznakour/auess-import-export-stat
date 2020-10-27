<?php



namespace App\Controller\ImportExport\SubPages;


final class PetitProjetSubPage extends ImportExportSubPageController
{

    protected $pageSlug = "auessaouira_petits_projets";
    protected $postType = "petit-Projet";


    protected $valideProjectTypes = array(
        array("name" => "PPU", "id" => "1"),
        array("name" => "PPR", "id" => "2"),
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

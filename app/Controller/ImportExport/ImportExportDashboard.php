<?php
/**
 * Created by PhpStorm.
 * User: Akram
 * Date: 04/07/2019
 * Time: 00:10
 */

namespace App\Controller\ImportExport;


use App\Controller\ImportExport\SubPages\CommissionsProvincialesSubPage;
use App\Controller\ImportExport\SubPages\PetitProjetSubPage;
use App\Controller\ImportExport\SubPages\GrandProjetSubPage;

class ImportExportDashboard extends \AdminPageFramework
{


    public function setUp()
    {

         $this->setRootMenuPage('Import / Export', 'dashicons-media-spreadsheet');

        $this->addSubMenuItems(
            array(
                'title' => __('Petits Projets', 'auessaouira'),
                'page_slug' => 'auessaouira_petits_projets',
            ),
            array(
                'title' => __('Grands Projets', 'auessaouira'),
                'page_slug' => 'auessaouira_grands_projets',
            ),
            array(
                'title' => __('Commissions Provinciales', 'auessaouira'),
                'page_slug' => 'auessaouira_commissions_provinciales',
            )
        );


    }

    public function do_auessaouira_petits_projets()
    {
        new PetitProjetSubPage();
    }

    public function do_auessaouira_grands_projets()
    {
        new GrandProjetSubPage();
    }

    public function do_auessaouira_commissions_provinciales()
    {
        new CommissionsProvincialesSubPage();
    }


}

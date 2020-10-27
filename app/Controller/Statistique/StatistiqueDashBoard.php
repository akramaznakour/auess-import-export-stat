<?php
/**
 * Created by PhpStorm.
 * User: Akram
 * Date: 04/07/2019
 * Time: 00:10
 */

namespace App\Controller\Statistique;


use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionDossiersAdhoc;
use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionDossiersTraite;
use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionAvisFavorable;
use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionInstruction;

class StatistiqueDashBoard extends \AdminPageFramework
{


    public function setUp()
    {
        $this->setRootMenuPage('Statistiques', 'dashicons-chart-line');

        $this->enqueueScript(plugin_dir_path(__FILE__) . '../../../assets/Chart.js');
        $this->enqueueScript(plugin_dir_path(__FILE__) . '../../../assets/utils.js');

        $this->addSubMenuItems(array(
            'title' => __('Gestion Urbaine', 'auessaouira'),
            'page_slug' => 'auessaouira_gestion_urbaine',
        ), array(
            'title' => __('Management & Qualité', 'auessaouira'),
            'page_slug' => 'auessaouira_management_qualite',
        ), array(
            'title' => __('Planification Urbaine', 'auessaouira'),
            'page_slug' => 'auessaouira_planification_urbaine',
        ));


    }

    public function do_auessaouira_gestion_urbaine()
    {


        $evolutionDossiersTraite = new EvolutionDossiersTraite();
        $evolutionDossiersTraite->render(2);

        $evolutionAvisFavorable = new EvolutionAvisFavorable();
        $evolutionAvisFavorable->render(2);

        $evolutionInstruction = new EvolutionInstruction();
        $evolutionInstruction->render();

        $evolutionDossiersAdhoc = new EvolutionDossiersAdhoc();
        $evolutionDossiersAdhoc->render();

    }

    public function do_auessaouira_management_qualite()
    {

    }

    public function do_auessaouira_planification_urbaine()
    {

    }


}

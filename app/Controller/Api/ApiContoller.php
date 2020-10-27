<?php

/**
 * Created by PhpStorm.
 * User: Akram
 * Date: 25/07/2019
 * Time: 11:01
 */

namespace App\Controller\Api;


use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionAvisFavorable;
use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionInstruction;
use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionDossiersTraite;
use App\Controller\Statistique\SubElements\StatistiquesGestionUrbaine\EvolutionDossiersAdhoc;
use App\Model\Post;
use App\Controller\Api\ApiHelper;

class ApiContoller
{

    public function __construct()
    {

        add_action('rest_api_init', function () {

            $a = array(
                array("route" => "actualites", "function" => "getActualites"),
                array("route" => "bulletins", "function" => "getBulletins"),
                array("route" => "publications", "function" => "getPublications"),
                array("route" => "resultatsCommissions", "function" => "searchResultatsCommissions"),
                array("route" => "statistiques", "function" => "statistiques"),
            );

            foreach ($a as $routeApi) :

                register_rest_route('api/v1', $routeApi["route"], [
                    'methods' => 'GET',
                    'callback' => array($this, $routeApi["function"]),
                ]);

            endforeach;
        });
    }

    public function getActualites()
    {
        $args = array(
            'post_type' => 'post',
            'orderby' => 'wpcf-date-de-publication',
            'order' => 'DESC',
            'posts_per_page' => -1);

        $posts = get_posts($args);

        $actualites = array();

        foreach ($posts as $key => $post) :

            $actualite["title"] = $post->post_title;
            $actualite["content"] = ApiHelper::imgTagRemoval($post->post_content);
            $actualite["image"] = ApiHelper::imagelinkExtractor($post->post_content);
            $actualite["date"] = substr($post->post_date, 0, 10);
            $actualite["url"] = $post->guid;

            $actualites[] = $actualite;

        endforeach;

        return $actualites;
    }


    public function getBulletins()
    {


        $args = array(
            'posts_per_page' => 10,
            'post_type' => 'bulletin-infos',
            'orderby' => 'wpcf-numero',
            'order' => 'DESC'
        );

        $resultats = get_posts($args);

        $bulletinsInfos = array();

        foreach ($resultats as $key => $post):

            $bulletinInfos["title"] = $post->post_title;
            $bulletinInfos["content"] = $post->post_content;
            $bulletinInfos["url"] = $post->guid;
            $bulletinInfos["date"] = substr($post->post_date, 0, 10);
            $bulletinInfos["attachment"] = get_post_meta($post->ID, 'wpcf-pdf', true);

            $bulletinsInfos[] = $bulletinInfos;


        endforeach;


        return $bulletinsInfos;

    }

    public function getPublications()
    {

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'auess_publication',
            'orderby' => 'wpcf-numero',
            'order' => 'DESC'
        );

        $posts = get_posts($args);

        $actualites = array();

        foreach ($posts as $post) :

            $actualite["title"] = $post->post_title;
            $actualite["content"] = $post->post_content;
            $actualite["date"] = substr($post->post_date, 0, 10);
            $actualite["url"] = $post->guid;

            if ($actualite["content"] != "")
                $actualites[] = $actualite;

        endforeach;

        return $actualites;
    }

    public function searchResultatsCommissions()
    {

        $posts = Post::_getAllPosts();

        $posts = Post::_filterCorrectPosts($posts);

        $posts = array_filter($posts, function ($post) {

            if (isset($_GET['type']) && $_GET['type'] != $post['post_type'])
                return false;


            if (
                isset($_GET['critereDeRecherche']) &&
                isset($_GET['critereDeRechercheValue']) &&
                substr_count($post["wpcf-" . $_GET['critereDeRecherche']], $_GET['critereDeRechercheValue']) < 1
            )
                return false;


            return true;
        });

        foreach ($posts as $key => $value) {
            $r[] = $value;
        }
        return $r;
    }

    public function statistiques()
    {


        $evolutionDossiersTraite = new EvolutionDossiersTraite();
        $evolutionAvisFavorable = new EvolutionAvisFavorable();
        $evolutionInstruction = new EvolutionInstruction();
        // $evolutionDossiersAdhoc = new EvolutionDossiersAdhoc();

        return [
            $evolutionDossiersTraite->_getEvolutionDossiersPetitProjetTraitesCanvas(),
            $evolutionDossiersTraite->_getEvolutionDossiersGrandProjetTraitesCanvas(),
            $evolutionAvisFavorable->_getEvolutionAvisPetitProjetFavorableCanvas(),
            $evolutionAvisFavorable->_getEvolutionAvisGrandProjetFavorableCanvas(),
            $evolutionInstruction->_getEvolutionInstructionCanvas(),
            //   $evolutionDossiersAdhoc->_getEvolutionDossiersAdhocCanvas(),
        ];
    }
}

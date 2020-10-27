<?php

namespace App\Model;


class Post
{

    public static $postTypes = array("petit-projet", "grand-projet", "commission-regionale");

    public static function _getLast3YearsOfgrandPorjet($type = null)
    {
        $type = self::$postTypes[1];

        return self::_getLast3YearsOfData($type);
    }

    public static function _getLast3YearsOfPetitPorjet()
    {

        $type = self::$postTypes[0];

        return self::_getLast3YearsOfData($type);

    }

    public static function _getLast3YearsOfData($type = null)
    {

        $posts = self::_getAllPosts($type);
        $posts = self::_filterCorrectPosts($posts);
        $posts = self::_sortPost($posts);
        $years = self::_getYearFromPosts($posts);
        $posts = self::_filterPostsByYears($posts, $years);

        return $posts;
    }

    public static function _filterCorrectPosts($posts)
    {


        $filteredPosts = array_filter($posts, function ($post) {

            if ($post['post_type'] == self::$postTypes[0] || $post['post_type'] == self::$postTypes[1]) {
                if (
                    !isset($post["wpcf-date-de-la-commission"]) ||
                    !isset($post["wpcf-avis-de-la-commission"]) ||
                    \DateTime::createFromFormat('d/m/Y', $post["wpcf-date-de-la-commission"]) == false
                ) {
                    return false;
                } else {
                    return true;
                }
            } elseif ($post['post_type'] == self::$postTypes[2]) {
                return true;
            } else {
                return false;
            }

        });


        return $filteredPosts;
    }

    public static function _getYearFromPosts($posts)
    {

        $years = [(int)date("Y") ,(int)date("Y") - 1,(int)date("Y") - 2,];



        rsort($years);

        $years = array_splice($years, 0, 3);

        return $years;
    }

    public static function _getAllPosts($type = null)
    {
        $dbPrefix = "wp";

        if ($type) {
            $postType = " '" . $type . "'";
        } else {
            $postType = " '" . self::$postTypes[0] . "', '" . self::$postTypes[1] . "'";
        }


        global $wpdb;

        $quer = "SELECT  *   FROM    " . $dbPrefix . "_posts JOIN " . $dbPrefix . "_postmeta ON (".$dbPrefix."_posts.ID = ".$dbPrefix."_postmeta.post_id) AND ".$dbPrefix."_posts.post_type IN ( $postType )  AND post_status = 'publish'";


        $results = $wpdb->get_results($quer, ARRAY_A);

        $posts = array();


        foreach ($results as $row) {
            if (!isset($posts[$row["post_id"]]["post_type"]))
                $posts[$row["post_id"]]["post_type"] = $row["post_type"];
            $posts[$row["post_id"]][$row["meta_key"]] = $row["meta_value"];
        }

        return $posts;
    }

    public static function _sortPost($posts)
    {

        usort($posts, function ($a, $b) {

            $aDate = $a['wpcf-date-de-la-commission'];
            $aDate = \DateTime::createFromFormat('d/m/Y', $aDate)->format('Y/m/d');

            $bDate = $b['wpcf-date-de-la-commission'];
            $bDate = \DateTime::createFromFormat('d/m/Y', $bDate)->format('Y/m/d');

            if ($aDate == $bDate) {
                return 0;
            }

            return ($aDate < $bDate) ? -1 : 1;
        });

        return $posts;
    }

    public static function _filterPostsByYears($posts, $years)
    {
        $posts = array_filter($posts, function ($post) use ($years) {
            return in_array(\DateTime::createFromFormat('d/m/Y', $post['wpcf-date-de-la-commission'])->format('Y'), $years);
        });

        return $posts;
    }

}
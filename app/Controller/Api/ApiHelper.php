<?php
/**
 * Created by PhpStorm.
 * User: Akram
 * Date: 31/07/2019
 * Time: 23:35
 */

namespace App\Controller\Api;


class ApiHelper
{
    public static function imagelinkExtractor($html)
    {
        $url = "";
        if (preg_match_all('/<img\s+.*?src=[\"\']?([^\"\' >]*)[\"\']?[^>]*>/i', $html, $matches, PREG_SET_ORDER))
            $url = $matches[0][1];
        return $url;
    }

    public static function imgTagRemoval($html)
    {
        $imgTag = "";
        if (preg_match_all('/<img\s+.*?src=[\"\']?([^\"\' >]*)[\"\']?[^>]*>/i', $html, $matches, PREG_SET_ORDER))
            $imgTag = $matches[0][0];

        return str_replace($imgTag,"",$html);
    }
}
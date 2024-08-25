<?php

namespace src;

require_once 'src/Viewer.php';

class Controller
{
    public static function getPage($url)
    {
        if ($url == '/') {
            $url = 'main';
        }
        $path = "views/$url.php";

        if (!file_exists($path)) {
            header('HTTP/1.0 404 Not Found');
            $page = require 'views/404.php';
        } else {
            $page = require $path;
            $route = '^/(?<slug>[a-z]+)$';
            if (preg_match("#$route#", $url, $params)) {
                $page = require "views/$params[slug].php";
            }
        }
        return $page;
    }
}
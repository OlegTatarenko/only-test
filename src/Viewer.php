<?php

namespace src;

class Viewer
{
    public static function viewPage(array $page)
    {
        $layout = file_get_contents('views/layout.php');
        $layout = str_replace('{{ title }}', $page['title'], $layout);
        $layout = str_replace('{{ main }}', $page['main'], $layout);
        echo $layout;
    }
}
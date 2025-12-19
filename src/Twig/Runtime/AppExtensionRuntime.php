<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
    }

    public function slugify($string)
    {
        $string = preg_replace("/ +/", "-", trim($string));
        $string = preg_replace("/[^A-Za-z0-9-]+/", "", $string);
        $string = mb_strtolower($string, 'UTF-8');

        return $string;
    }
}

<?php

namespace Languara\Plugin\Library;
include 'lib_languara.php';

class LanguaraWrapper extends \Lib_Languara
{
    public function __construct()
    {
        $this->conf                 = \Config::get('plugin::conf');
        $this->endpoints            = \Config::get('plugin::endpoints');
        $this->language_location    = \Config::get('plugin::language_location');
    }
}
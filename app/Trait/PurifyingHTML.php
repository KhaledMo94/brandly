<?php

namespace App\Trait;
use HTMLPurifier;
use HTMLPurifier_Config;

trait PurifyingHTML
{
    public static function PurifyingHTML($content){
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($content);

        return $clean_html;
    }
}

<?php
/* be sure to require `hashids` in your `composer.json` file first */

use Hashids\Hashids;

require_once(__DIR__ . '/HashGenerator.php');
require_once(__DIR__ . '/Hashids.php');


function hash_id($string,$character = 5)
{

    try {
        $hash_id = new Hashids('', $character);
    } catch (Exception $e) {
    }


    return $hash_id->encode($string);
}
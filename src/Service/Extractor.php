<?php

namespace App\Service;

use _PHPStan_d3e3292d7\Symfony\Component\Console\Exception\LogicException;

class Extractor
{
    public function __construct()
    {
    }

    /**
     * @param string $link
     * @return string
     */
    public function extractVideoId(string $link): string
    {
        if(preg_match("/^https:\/\/www\.youtube\.com\/shorts\/[a-zA-Z\d\-_]{11}$/", $link))
        {
            $ref = explode("/", $link);
            /**
             * @var string $id
             */
            $id = end($ref);
        }
        elseif (preg_match("/^https:\/\/www\.youtube\.com\/watch\?v=[a-zA-Z\d\-_]{11}$/", $link))
        {
            $ref = explode("=", $link);
            /**
             * @var string $id
             */
            $id = end($ref);
        }
        else{
            throw new LogicException("Invalid Url ... Try Something else");
        }

        return $id;
    }
}
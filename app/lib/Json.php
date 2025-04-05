<?php

namespace app\lib;

class Json
{

    public static function encode($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public static function decode($data)
    {
        return json_decode($data);
    }

}
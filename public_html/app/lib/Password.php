<?php

namespace app\lib;

class Password
{

    public static function hash($string)
    {
        return password_hash($string, PASSWORD_DEFAULT, ['cost' => 14]);
    }

    public static function verify($string, $hash)
    {
        return password_verify($string, $hash);
    }

}
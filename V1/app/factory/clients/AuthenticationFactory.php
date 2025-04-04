<?php

namespace app\factory\clients;

use app\api\clients\Authentication;

class AuthenticationFactory
{

    public static function login($inputs)
    {
        $authentication = new Authentication();
        return $authentication->login($inputs);
    }

    public static function register($inputs)
    {
        $authentication = new Authentication();
        return $authentication->register($inputs);
    }

}
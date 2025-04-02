<?php

namespace app\lib;

class ReCaptcha
{

    public static function verify($secret, $response)
    {
        $data = [
            'secret' => $secret,
            'response' => $response
        ];

        $result = Curl::post('https://www.google.com/recaptcha/api/siteverify', $data);

        return Json::decode($result)->success;
    }

}
<?php

namespace app\lib;

class Forms
{

    public static function empty($method, $arry)
    {
        foreach ($arry as $item) {
            if(empty($method[$item]))
            {
                return true;
            }
        }
        return false;
    }

    public static function getTokenID()
    {
        if(isset($_SESSION['token_id']))
        {
            return $_SESSION['token_id'];
        }
        $token_id = Forms::random(60);
        $_SESSION['token_id'] = $token_id;
        return $token_id;
    }

    public static function getToken()
    {
        if(isset($_SESSION['token_value']))
        {
            return $_SESSION['token_value'];
        }
        $token = hash('sha256', Forms::random(500));
        $_SESSION['token_value'] = $token;
        return $token;
    }

    public static function checkValid($method) {
        if(isset($method[Forms::getTokenID()]) && ($method[Forms::getTokenID()] == Forms::getToken())) {
            return true;
        }
        return false;
    }

    public static function formNames($names, $regenerate) {

        $values = array();
        foreach ($names as $n) {
            if($regenerate == true) {
                unset($_SESSION[$n]);
            }
            $s = isset($_SESSION[$n]) ? $_SESSION[$n] : Forms::random(60);
            $_SESSION[$n] = $s;
            $values[$n] = Forms::sqlinject($s);
        }
        return $values;
    }

    private static function random($tamanho)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';
        $caracteres .= $lmin;
        $caracteres .= $lmai;
        $caracteres .= $num;
        $caracteres .= $simb;
        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }

    private static function sqlinject($str)
    {
        return addslashes(strip_tags($str));
    }

}
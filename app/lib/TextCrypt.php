<?php

namespace app\lib;

class TextCrypt
{

    public static function hash($lenght, $uppercase, $lowercase, $numbers, $symbols){
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ";
        $mi = "abcdefghijklmnopqrstuvyxwz";
        $nu = "0123456789";
        $si = "!@#$%¨&*()_+=";

        $hash = "";

        if ($uppercase){
            $hash .= str_shuffle($ma);
        }

        if ($lowercase){
            $hash .= str_shuffle($mi);
        }

        if ($numbers){
            $hash .= str_shuffle($nu);
        }

        if ($symbols){
            $hash .= str_shuffle($si);
        }

        return substr(str_shuffle($hash),0, $lenght);
    }

    public static function encrypt($texto, $senha, $iv_len = 16)
    {
        $texto .= "x13";
        $n = strlen($texto);
        if ($n % 16) $texto .= str_repeat("", 16 - ($n % 16));
        $i = 0;
        $Enc_Texto = TextCrypt::random($iv_len);
        $iv = substr($senha ^ $Enc_Texto, 0, 512);
        while ($i < $n) {
            $Bloco = substr($texto, $i, 16) ^ pack('H*', md5($iv));
            $Enc_Texto .= $Bloco;
            $iv = substr($Bloco . $iv, 0, 512) ^ $senha;
            $i += 16;
        }
        return base64_encode($Enc_Texto);
    }

    public static function decrypt($Enc_Texto, $senha, $iv_len = 16)
    {
        $Enc_Texto = base64_decode($Enc_Texto);
        $n = strlen($Enc_Texto);
        $i = $iv_len;
        $texto = '';
        $iv = substr($senha ^ substr($Enc_Texto, 0, $iv_len), 0, 512);
        while ($i < $n) {
            $Bloco = substr($Enc_Texto, $i, 16);
            $texto .= $Bloco ^ pack('H*', md5($iv));
            $iv = substr($Bloco . $iv, 0, 512) ^ $senha;
            $i += 16;
        }
        return str_replace([ 'x13', 'x00*$' ], '', $texto);
    }

    private static function random($iv_len)
    {
        $iv = '';
        while ($iv_len-- > 0) {
            $iv .= chr(mt_rand() & 0xff);
        }
        return $iv;
    }

}
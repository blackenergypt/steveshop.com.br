<?php

namespace app\lib;

class Curl
{

    public static function get($url, $data = [])
    {
        if(count($data) == 0)
        {
            return file_get_contents($url);
        }

        $http_query = http_build_query($data);

        return file_get_contents($url . '?' . $http_query);
    }

    public static function post($url, $data)
    {

        $fields = '';

        foreach($data as $key => $value) {
            $fields .= $key.'='.$value.'&';
        }

        rtrim($fields, '&');

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

}
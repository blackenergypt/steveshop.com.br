<?php

namespace app\api\webstores\admin;

use app\lib\Model;

class PreConfig extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function register($client_id, $plan, $title, $currency, $domain)
    {
        $obj = [
            'config_CLIENT_ID' => $client_id,
            'config_PLAN' => $plan,
            'config_TITLE' => $title,
            'config_CURRENCY' => $currency,
            'config_DOMAIN' => $domain
        ];

        $insert = $this->insert($obj, 'webstores_preconfigs');

        return $insert['id'];
    }

    public function get($config_id)
    {
        $query = "SELECT `config_CLIENT_ID` as client_id, `config_PLAN` as plan, `config_TITLE` as title, `config_CURRENCY` as currency, `config_DOMAIN` as domain FROM `webstores_preconfigs` WHERE `config_ID`={$config_id}";
        return $this->select($query);
    }

}
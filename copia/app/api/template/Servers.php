<?php

namespace app\api\template;

use app\lib\Model;

class Servers extends Model
{

    private $wid;
    private $data;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;

        $this->data = $this->select("SELECT * FROM `webstores_servers` WHERE `server_WID`={$wid}", 'all');
    }

    public function listAll()
    {
        return $this->data;
    }

    public function name($sid)
    {
        return $this->select("SELECT * FROM `webstores_servers` WHERE `server_ID`={$sid}")->server_NAME;
    }

}
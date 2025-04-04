<?php

namespace app\api\webstores\admin;

use app\lib\Model;

class Webstore extends Model
{

    private $wid;

    public function __construct()
    {
        parent::__construct();

        $this->wid = $_SESSION['WebstoreLogged'];
    }

    public function setWebstore($wid)
    {
        $this->wid = $wid;
    }

    public function plan()
    {
        return $this->get()->webstore_PLAN;
    }

    public function token()
    {
        return $this->get()->webstore_TOKEN;
    }

    public function domain()
    {
        return $this->get()->webstore_DOMAIN;
    }

    private function get()
    {
        return $this->select("SELECT * FROM `webstores` WHERE `webstore_ID`={$this->wid}");
    }

}
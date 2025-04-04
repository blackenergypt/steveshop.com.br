<?php

namespace app\api\template;

use app\lib\Model;

class Products extends Model
{

    private $wid;
    private $sid;
    private $cid;


    public function __construct($wid, $sid, $cid = null)
    {
        parent::__construct();

        $this->wid = $wid;
        $this->sid = $sid;
        $this->cid = $cid;
    }

    public function listAll()
    {
        $category = ($this->cid != null) ? "AND `package_CATEGORY`={$this->cid}" : '';

        return $this->select("SELECT * FROM `webstores_packages` WHERE `package_WID`={$this->wid} AND `package_SERVER`={$this->sid} {$category}", 'all');
    }

}
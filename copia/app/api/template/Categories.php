<?php

namespace app\api\template;

use app\lib\Model;

class Categories extends Model
{

    private $sid;

    public function __construct($sid)
    {
        parent::__construct();

        $this->sid = $sid;
    }

    public function has()
    {
        $count = count($this->select("SELECT * FROM `webstores_categories` WHERE `category_SID`={$this->sid}", 'all'));
        return $count > 0;
    }

    public function listAll()
    {
        return $this->select("SELECT * FROM `webstores_categories` WHERE `category_SID`={$this->sid}", 'all');
    }

}
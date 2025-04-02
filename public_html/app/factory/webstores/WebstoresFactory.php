<?php

namespace app\factory\webstores;

use app\lib\Model;

class WebstoresFactory
{

    public static function data()
    {
        $model = new Model();

        $select = $model->select("SELECT * FROM `webstores` WHERE `webstore_ID`={$_SESSION['WebstoreLogged']}");

        return $select;
    }


}
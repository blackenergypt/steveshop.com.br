<?php

namespace app\controller\api;

use app\lib\Controller;
use app\lib\Json;
use app\lib\Model;

class authenticationController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $model = new Model();

        header('Content-Type: application/json');

        if(empty($_GET['token']))
        {
            echo Json::encode([ 'status' => false, 'code' => 1002, 'message' => 'Uninvited token' ]);
            exit();
        }

        $verify = $model->select("SELECT COUNT(*) as rows FROM `webstores` WHERE `webstore_TOKEN`='{$_GET['token']}'")->rows;

        if($verify > 0)
        {
            echo Json::encode([ 'status' => true, 'message' => 'Success!' ]);
            exit();
        }

        echo Json::encode([ 'status' => false, 'code' => 1001, 'message' => 'Unauthorized!' ]);
        exit();

    }

}
<?php

namespace app\controller\api;

use app\lib\Controller;
use app\lib\Json;
use app\lib\Model;

class activeController extends Controller
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
            echo Json::encode([ 'success' => false, 'code' => 1002, 'message' => 'Uninvited token' ]);
            die();
        }

        if(empty($_GET['sid']))
        {
            echo Json::encode([ 'success' => false, 'code' => 1004, 'message' => 'Uninvited server id' ]);
            die();
        }

        if(empty($_GET['did']))
        {
            echo Json::encode([ 'success' => false, 'code' => 1006, 'message' => 'Uninvited dispense id' ]);
            die();
        }

        $verifyToken = $model->select("SELECT COUNT(*) as rows FROM `webstores` WHERE `webstore_TOKEN`='{$_GET['token']}'")->rows;

        if($verifyToken == 0)
        {
            echo Json::encode([ 'status' => false, 'code' => 1001, 'message' => 'Token unauthorized!' ]);
            die();
        }

        $data = $model->select("SELECT * FROM `webstores` WHERE `webstore_TOKEN`='{$_GET['token']}'");
        $wid = $data->webstore_ID;

        $verifyServer = $model->select("SELECT COUNT(*) as total FROM `webstores_servers` WHERE `server_WID`={$wid} AND `server_ID`={$_GET['sid']}")->total;

        if($verifyServer == 0)
        {
            echo Json::encode([ 'status' => false, 'code' => 1003, 'message' => 'Server ID not exists!' ]);
            die();
        }

        $verifyID = $model->select("SELECT COUNT(*) as total FROM `webstores_dispense` WHERE `dispense_ID`={$_GET['did']} AND `dispense_ACTIVED`=0")->total;

        if($verifyID == 0)
        {
            echo Json::encode([ 'status' => false, 'code' => 1005, 'message' => 'Dispense ID does not exist or has already been activated!' ]);
            die();
        }

        $model->update([ 'dispense_ACTIVED' => 1 ], [ 'dispense_ID' => $_GET['did'] ], 'webstores_dispense');

        $response['status'] = true;
        $response['message'] = 'Success!';

        echo Json::encode($response);
        die();
    }

}
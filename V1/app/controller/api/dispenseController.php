<?php

namespace app\controller\api;

use app\lib\Controller;
use app\lib\Json;
use app\lib\Model;

class dispenseController extends Controller
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

        $packages = $model->select("SELECT * FROM `webstores_dispense` WHERE `dispense_WID`={$wid} AND `dispense_SID`={$_GET['sid']}", 'all');

        $response = [];

        $response['status'] = true;
        $response['sync'] = false;

        $i = 0;
        $dispense = [];

        foreach ($packages as $package)
        {
            if($package->dispense_ACTIVED == 1)
            {
                continue;
            }

            $i++;

            $cmds = [];

            $commands = $model->select("SELECT * FROM `webstores_packages_commands` WHERE `command_PID`={$package->dispense_PID}", 'all');

            foreach ($commands as $command)
            {
                if($command->command_TYPE != 1)
                {
                    continue;
                }
                $cmds[] = str_replace('@p', $package->dispense_USERNAME, $command->command_TXT);
            }

            $dispense[] = [
                'dispense_id' => $package->dispense_ID,
                'nickname' => $package->dispense_USERNAME,
                'package_id' => $package->dispense_PID,
                'date' => $package->dispense_DATE,
                'commands' => $cmds
            ];
        }

        if($i > 0) {
            $response['sync'] = true;
        }

        $response['packages'] = $dispense;

        echo Json::encode($response);
        die();
    }

}
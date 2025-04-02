<?php

namespace app\api\webstores\admin;

use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Servers extends Model
{

    private $webstore;

    public function __construct()
    {
        parent::__construct();

        $this->webstore = new Webstore();
    }

    public function name($sid)
    {
        return $this->select("SELECT * FROM `webstores_servers` WHERE `server_ID`={$sid}")->server_NAME;
    }

    public function add()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST['name']))
        {
            return Json::encode([ 'success' => false, 'message' => 'Insira o nome do servidor' ]);
        }
        if($this->webstore->plan() == 1)
        {
            if($this->counter($_SESSION['WebstoreLogged']) >= 1)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }
        if($this->webstore->plan() == 2)
        {
            if($this->counter($_SESSION['WebstoreLogged']) >= 2)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }
        if($this->webstore->plan() == 3)
        {
            if($this->counter($_SESSION['WebstoreLogged']) >= 4)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }

        $show = isset($_POST['show']) ? 1 : 0;

        $obj = [
            'server_WID' => $_SESSION['WebstoreLogged'],
            'server_NAME' => $_POST['name'],
            'server_SHOW' => $show
        ];

        $this->insert($obj, 'webstores_servers');

        return Json::encode(['success' => true, 'message' => 'Servidor registrado']);
    }

    public function att()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST['name']))
        {
            $this->delete([ 'server_ID' => $_POST['id'] ], 'webstores_servers');
            return true;
        }

        $this->update([ 'server_NAME' => $_POST['name'] ], [ 'server_ID' => $_POST['id'], 'server_WID' => $_SESSION['WebstoreLogged'] ], 'webstores_servers');
    }

    public function del()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        $this->delete([ 'server_ID' => $_POST['id'], 'server_WID' => $_SESSION['WebstoreLogged'] ], 'webstores_servers');
    }

    public function setShow()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        $id = $_POST['id'];
        $show = $_POST['mode'] == 'on' ? 1 : 0;

        $this->update([ 'server_SHOW' => $show ], [ 'server_ID' => $id ], 'webstores_servers');
    }

    public function row()
    {
        return $this->select("SELECT * FROM `webstores_servers` WHERE `server_WID`={$_SESSION['WebstoreLogged']}", 'all');
    }

    public function counter($wid)
    {
        return $this->select("SELECT COUNT(*) as total FROM `webstores_servers` WHERE `server_WID`={$wid}")->total;
    }

}
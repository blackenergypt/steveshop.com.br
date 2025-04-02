<?php

namespace app\api\webstores\admin;

use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Categories extends Model
{

    private $webstore;

    public function __construct()
    {
        parent::__construct();

        $this->webstore = new Webstore();
    }

    public function add()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST['name']))
        {
            return Json::encode([ 'success' => false, 'message' => 'Insira o nome da categoria' ]);
        }
        if(empty($_POST['server']))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe o servidor' ]);
        }
        if($this->webstore->plan() == 1)
        {
            if($this->counter($_POST['server']) >= 2)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }
        if($this->webstore->plan() == 2)
        {
            if($this->counter($_POST['server']) >= 4)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }
        if($this->webstore->plan() == 3)
        {
            if($this->counter($_POST['server']) >= 8)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }

        $show = isset($_POST['show']) ? 1 : 0;

        $obj = [
            'category_SID' => $_POST['server'],
            'category_WID' => $_SESSION['WebstoreLogged'],
            'category_NAME' => $_POST['name'],
            'category_SHOW' => $show
        ];

        $this->insert($obj, 'webstores_categories');

        return Json::encode(['success' => true, 'message' => 'Categoria registrada']);
    }

    public function att()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST['name']))
        {
            $this->delete([ 'category_ID' => $_POST['id'] ], 'webstores_categories');
            return false;
        }

        $this->update([ 'category_NAME' => $_POST['name'] ], [ 'category_ID' => $_POST['id'], 'category_WID' => $_SESSION['WebstoreLogged'] ], 'webstores_categories');

        return true;
    }

    public function del()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        $this->delete([ 'category_ID' => $_POST['id'], 'category_WID' => $_SESSION['WebstoreLogged'] ], 'webstores_categories');
        return true;
    }

    public function setShow()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        $id = $_POST['id'];
        $show = $_POST['mode'] == 'on' ? 1 : 0;

        $this->update([ 'category_SHOW' => $show ], [ 'category_ID' => $id ], 'webstores_categories');

        return true;
    }

    public function row()
    {
        return $this->select("SELECT * FROM `webstores_categories` WHERE `category_WID`={$_SESSION['WebstoreLogged']}", 'all');
    }

    public function rows($wid)
    {
        return $this->select("SELECT COUNT(*) as total FROM `webstores_categories` WHERE `category_WID`={$wid}")->total;
    }

    public function data($sid)
    {
        return $this->select("SELECT * FROM `webstores_categories` WHERE `category_SID`={$sid}", 'all');
    }

    public function counter($sid)
    {
        return $this->select("SELECT COUNT(*) as total FROM `webstores_categories` WHERE `category_SID`={$sid}")->total;
    }

}
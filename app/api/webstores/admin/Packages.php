<?php

namespace app\api\webstores\admin;

use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;
use app\lib\Upload;

class Packages extends Model
{

    public $webstore;

    public function __construct()
    {
        parent::__construct();

        $this->webstore = new Webstore();
    }

    public function add()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'status' => false, 'message' => 'Blocked' ]);
        }
        if(!isset($_POST))
        {
            return Json::encode([ 'status' => false, 'message' => 'Blocked' ]);
        }
        if(empty($_POST))
        {
            return Json::encode([ 'status' => false, 'message' => 'Blocked' ]);
        }
        if(Forms::empty($_POST, [ 'name', 'description', 'price', 'server' ]))
        {
            return Json::encode([ 'status' => false, 'message' => 'Informe todos os dados obrigatórios' ]);
        }

        if($this->webstore->plan() == 1)
        {
            if($this->counter($_SESSION['WebstoreLogged']) >= 10)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }
        if($this->webstore->plan() == 2)
        {
            if($this->counter($_SESSION['WebstoreLogged']) >= 30)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }
        if($this->webstore->plan() == 3)
        {
            if($this->counter($_SESSION['WebstoreLogged']) >= 60)
            {
                return Json::encode([ 'success' => false, 'message' => 'Máximo de servidores atingido' ]);
            }
        }

        $upload = Upload::image('./cdn/images/', $_FILES['image']);

        if($upload['status'] == false)
        {
            return Json::encode([ 'status' => false, 'message' => $upload['message'] ]);
        }

        if($upload['src'] == 'NULL')
        {
            return Json::encode([ 'status' => false, 'message' => 'Selecione uma imagem' ]);
        }

        if(empty($_POST['types']))
        {
            return Json::encode([ 'status' => false, 'message' => 'Nenhum comando informado' ]);
        }

        if(empty($_POST['commands']))
        {
            return Json::encode([ 'status' => false, 'message' => 'Nenhum comando informado' ]);
        }

        $obj = [
            'package_WID' => $_SESSION['WebstoreLogged'],
            'package_NAME' => $_POST['name'],
            'package_IMAGE' => $upload['src'],
            'package_DESCRIPTION' => $_POST['description'],
            'package_PRICE' => $_POST['price'],
            'package_SERVER' => $_POST['server'],
            'package_CATEGORY' => empty($_POST['category']) ? 0 : $_POST['category']
        ];

        $insert = $this->insert($obj, 'webstores_packages');

        $i=-1;

        foreach ($_POST['types'] as $type)
        {
            $i++;

            $obj = [
                'command_PID' => $insert['id'],
                'command_TYPE' => ($type == 'Aprovado') ? 1 : 2,
                'command_TXT' => $_POST['commands'][$i]
            ];

            $this->insert($obj, 'webstores_packages_commands');
        }

        return Json::encode([ 'status' => true, 'message' => 'Pacote cadastrado!' ]);
    }

    public function counter($wid)
    {
        return $this->select("SELECT COUNT(*) as total FROM `webstores_packages` WHERE `package_WID`={$wid}")->total;
    }

    public function edit()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'status' => false, 'message' => 'Blocked' ]);
        }
        if(!isset($_POST))
        {
            return Json::encode([ 'status' => false, 'message' => 'Blocked' ]);
        }
        if(empty($_POST))
        {
            return Json::encode([ 'status' => false, 'message' => 'Blocked' ]);
        }
        if(Forms::empty($_POST, [ 'name', 'description', 'price', 'server' ]))
        {
            return Json::encode([ 'status' => false, 'message' => 'Informe todos os dados obrigatórios' ]);
        }

        $upload = Upload::image('./cdn/images/', $_FILES['image']);

        if($upload['status'] == false)
        {
            return Json::encode([ 'status' => false, 'message' => $upload['message'] ]);
        }

        if($upload['src'] == 'NULL')
        {
            $upload['src'] = $this->data($_POST['id'])->package_IMAGE;
        }

        $obj = [
            'package_NAME' => $_POST['name'],
            'package_IMAGE' => $upload['src'],
            'package_DESCRIPTION' => $_POST['description'],
            'package_PRICE' => $_POST['price'],
            'package_SERVER' => $_POST['server'],
            'package_CATEGORY' => empty($_POST['category']) ? 0 : $_POST['category']
        ];

        $this->update($obj, [ 'package_ID' => $_POST['id'], 'package_WID' => $_SESSION['WebstoreLogged'] ], 'webstores_packages');

        return Json::encode([ 'status' => true, 'message' => 'Pacote atualizado!' ]);
    }

    public function dataAll()
    {
        return $this->select("SELECT * FROM `webstores_packages` WHERE `package_WID`={$_SESSION['WebstoreLogged']}", 'all');
    }

    public function del()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        $this->delete([ 'package_ID' => $_POST['id'], 'package_WID' => $_SESSION['WebstoreLogged'] ], 'webstores_packages');
    }

    public function data($pid)
    {
        return $this->select("SELECT * FROM `webstores_packages` WHERE `package_ID`={$pid} AND `package_WID`={$_SESSION['WebstoreLogged']}");
    }

    public function dataWID($pid)
    {
        return $this->select("SELECT * FROM `webstores_packages` WHERE `package_ID`={$pid}");
    }

}
<?php

namespace app\api\webstores;

use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Domain extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function att()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(empty($_POST['domain']))
        {
            return Json::encode(['success' => false, 'message' => 'Informe o domínio']);
        }

        $this->update([ 'webstore_DOMAIN' => trim($_POST['domain']) ], [ 'webstore_ID' => $_SESSION['WebstoreLogged'] ], 'webstores');

        return Json::encode([ 'success' => true, 'message' => 'Domínio autorizado!' ]);
    }

}
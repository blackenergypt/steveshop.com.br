<?php

namespace app\api\webstores;

use app\api\webstores\admin\Packages;
use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class ManualSend extends Model
{

    private $wid;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;
    }

    public function send()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(Forms::empty($_POST, [ 'username', 'sid', 'pid' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe todos os dados' ]);
        }

        $obj = [
            'dispense_WID' => $this->wid,
            'dispense_SID' => $_POST['sid'],
            'dispense_PID' => $_POST['pid'],
            'dispense_USERNAME' => $_POST['username'],
            'dispense_DATE' => date("Y-m-d H:i:s"),
            'dispense_ACTIVED' => 0
        ];

        $this->insert($obj, 'webstores_dispense');

        return Json::encode([ 'success' => true, 'message' => 'Pacote enviado com sucesso!' ]);
    }

    public function dispense($pid, $username)
    {
        $products = new Packages();
        $product = $products->dataWID($pid);

        $obj = [
            'dispense_WID' => $this->wid,
            'dispense_SID' => $product->package_SERVER,
            'dispense_PID' => $pid,
            'dispense_USERNAME' => $username,
            'dispense_DATE' => date("Y-m-d H:i:s"),
            'dispense_ACTIVED' => 0
        ];

        $this->insert($obj, 'webstores_dispense');

        return Json::encode([ 'success' => true, 'message' => 'Pacote enviado com sucesso!' ]);
    }

}
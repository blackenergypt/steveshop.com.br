<?php

namespace app\api\webstores;

use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Gifts extends Model
{

    private $wid;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;
    }

    public function add()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(Forms::empty($_POST, [ 'cupom', 'discount' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe todos os campos' ]);
        }

        $obj = [
            'gift_WID' => $this->wid,
            'gift_CUPOM' => $_POST['cupom'],
            'gift_DISCOUNT' => $_POST['discount']
        ];

        $this->insert($obj, 'webstores_gifts');

        return Json::encode([ 'success' => true, 'message' => 'Código adicionado!' ]);
    }

    public function has($cupom) {
        return $this->select("SELECT * FROM `webstores_gifts` WHERE `gift_WID`={$this->wid} AND `gift_CUPOM`='{$cupom}'");
    }

    public function del()
    {
        $this->delete([ 'gift_WID' => $this->wid, 'gift_ID' => $_POST['id'] ], 'webstores_gifts');
    }

    public function data()
    {
        return $this->select("SELECT * FROM `webstores_gifts` WHERE `gift_WID`={$this->wid}", 'all');
    }

}
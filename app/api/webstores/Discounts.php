<?php

namespace app\api\webstores;

use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Discounts extends Model
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
        if(Forms::empty($_POST, [ 'cupom', 'discount', 'server', 'category', 'expire' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe todos os campos' ]);
        }

        $obj = [
            'discount_WID' => $this->wid,
            'discount_CUPOM' => $_POST['cupom'],
            'discount_PERCENT' => $_POST['discount'],
            'discount_SID' => $_POST['server'],
            'discount_CID' => $_POST['category'],
            'discount_EXPIRE' => $_POST['expire']
        ];

        $this->insert($obj, 'webstores_discounts');

        return Json::encode([ 'success' => true, 'message' => 'Cupom adicionado!' ]);
    }

    public function del()
    {
        $this->delete([ 'discount_WID' => $this->wid, 'discount_ID' => $_POST['id'] ], 'webstores_discounts');
    }

    public function has($cupom) {
        return $this->select("SELECT * FROM `webstores_discounts` WHERE `discount_WID`={$this->wid} AND `discount_CUPOM`='{$cupom}' AND `discount_EXPIRE` > CURRENT_DATE();");
    }

    public function data()
    {
        return $this->select("SELECT * FROM `webstores_discounts` WHERE `discount_WID`={$this->wid}", 'all');
    }

}
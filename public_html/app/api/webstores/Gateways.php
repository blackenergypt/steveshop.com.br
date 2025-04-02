<?php

namespace app\api\webstores;

namespace app\api\webstores;

use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Gateways extends Model
{

    private $wid;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;

        $count = $this->select("SELECT * FROM `webstores_gateways` WHERE `gateway_WID`={$wid}");

        if(!$count) {

            $obj = [
                'gateway_WID' => $wid,
                'pagseguro_EMAIL' => 'não cadastrado',
                'pagseguro_TOKEN' => 'não cadastrado',
                'mercadopago_ID' => 'não cadastrado',
                'mercadopago_SECRET' => 'não cadastrado',
                'paypal_EMAIL' => 'não cadastrado',
                'pagseguro_SHOW' => 0,
                'mercadopago_SHOW' => 0,
                'paypal_SHOW' => 0
            ];

            $this->insert($obj, 'webstores_gateways');
        }
    }

    public function att()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(!isset($_POST))
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(empty($_POST))
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        $show = (isset($_POST['show'])) ? 1 : 0;
        $gStr = $_POST['gateway'] . '_SHOW';
        $_POST[$gStr] = $show;

        unset($_POST['show']);
        unset($_POST['gateway']);

        $this->update($_POST, [ 'gateway_WID' => $this->wid ], 'webstores_gateways');

        return Json::encode([ 'success' => true, 'message' => 'Gateway atualizada!' ]);
    }

    public function data()
    {
        return $this->select("SELECT * FROM `webstores_gateways` WHERE `gateway_WID`={$this->wid}");
    }



}
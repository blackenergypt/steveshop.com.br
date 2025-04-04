<?php

namespace app\api\clients;

use app\factory\clients\ProfileFactory;
use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Webstores extends Model
{

    private $client_id;
    private $currencies = [
        'BRL' => 'ok',
        'USD' => 'ok',
        'EUR' => 'ok'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->client_id = ProfileFactory::getId();

        if($this->has())
        {
            $this->autoLoggin();
        }
    }

    public function autoLoggin()
    {
        $user_id = ProfileFactory::getId();
        if(!isset($_SESSION['WebstoreLogged']))
        {
            $_SESSION['WebstoreLogged'] = $this->lastStore($user_id);
        }
    }

    public function lastStore($user_id)
    {
        return $this->select("SELECT * FROM `webstores` WHERE `webstore_CLIENT_ID`={$user_id} ORDER BY `webstore_ID` DESC LIMIT 1")->webstore_ID;
    }

    public function free($planId)
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'status' => false, 'message' => 'Denied' ]);
        }
        if(Forms::empty($_POST, [ 'plan', 'name', 'currency', 'subdomain' ]))
        {
            return Json::encode([ 'status' => false, 'message' => 'Informe todos os dados' ]);
        }
        if($this->has($_POST['subdomain']))
        {
            return Json::encode([ 'status' => false, 'message' => 'O domínio que você escolheu já existe, tente outro' ]);
        }
        if(!key_exists($_POST['currency'], $this->currencies))
        {
            return Json::encode([ 'status' => false, 'message' => 'Moeda inválida' ]);
        }

        $profile = new Profile();

        if(!$profile->isActived())
        {
            return Json::encode([ 'status' => false, 'message' => 'É necessário confirmar a conta para ativar um serviço' ]);
        }

        $select = $this->select("SELECT * FROM `webstores` WHERE `webstore_PLAN`=1 AND `webstore_CLIENT_ID`=" . ProfileFactory::getId(), 'all');

        if(count($select) > 0) {
            return Json::encode([ 'status' => false, 'message' => 'Você já possui um serviço gratuito!' ]);
        }

        $expire = "2025-12-30";
        $delete_in = "2025-12-30";

        $webstore_id = $this->create($planId, $_POST['name'], $_POST['subdomain'], $_POST['currency']);
        $this->setExpire($webstore_id, $expire, $delete_in);

        $_SESSION['WebstoreLogged'] = $webstore_id;

        return Json::encode([ 'status' => true, 'free' => true, 'message' => 'Loja criada!' ]);
    }

    public function expireIn($id)
    {
        return $this->select("SELECT * FROM `webstores_maturity` WHERE `maturity_WID`={$id}")->maturity_EXPIRE;
    }

    public function create($plan, $name, $subdomain, $currency)
    {
        $hash = uniqid();
        $hash = md5($hash);
        $hash = substr($hash, 0, -6);

        $obj = [
            'webstore_CLIENT_ID' => ProfileFactory::getId(),
            'webstore_PLAN' => $plan,
            'webstore_TOKEN' => $hash,
            'webstore_ID_NAME' => $name,
            'webstore_SUBDOMAIN' => $subdomain,
            'webstore_TEMPLATE' => 'default',
            'webstore_CURRENCY' => $currency,
            'webstore_GAME' => 'MINECRAFT'
        ];

        return $this->insert($obj, 'webstores')['id'];
    }

    public function setExpire($wid, $expire, $delete_in)
    {
        $obj = [
            'maturity_WID' => $wid,
            'maturity_EXPIRE' => $expire,
            'maturity_DELETE_IN' => $delete_in
        ];

        $this->insert($obj, 'webstores_maturity');
    }

    public function listAll()
    {
        return $this->select("SELECT * FROM `webstores` WHERE `webstore_CLIENT_ID`={$this->client_id}", 'all');
    }

    public function has($subdomain = "")
    {
        if($subdomain == "")
        {
            $total = $this->select("SELECT COUNT(*) as total FROM `webstores` WHERE `webstore_CLIENT_ID`={$this->client_id}")->total;
            return $total > 0;
        }

        $total = $this->select("SELECT COUNT(*) as total FROM `webstores` WHERE `webstore_SUBDOMAIN`='{$subdomain}'")->total;
        return $total > 0;
    }

}
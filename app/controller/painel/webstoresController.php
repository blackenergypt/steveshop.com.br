<?php

namespace app\controller\painel;

use app\api\clients\Authentication;
use app\api\clients\Profile;
use app\api\clients\Webstores;
use app\api\Plans;
use app\api\webstores\admin\PreConfig;
use app\factory\clients\ProfileFactory;
use app\lib\Config;
use app\lib\Controller;
use app\lib\Json;
use app\lib\Model;

class webstoresController extends Controller
{

    public $webstores;
    public $mp;

    public function __construct()
    {
        parent::__construct();

        $this->webstores = new Webstores();
        try {
            $this->mp = new \MP(Config::MERCADOPAGO_CLIENT, Config::MERCADOPAGO_SECRET);
        }catch (\MercadoPagoException $e)
        {
            die($e->getMessage());
        }


        $this->setLayout("core");
    }

    public function index()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            exit();
        }
        if(!$this->webstores->has())
        {
            header('Location: /webstores/create');
            die();
        }
        $this->view();
    }

    private function sanitizeString($str) {
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        $str = preg_replace('/[^a-z0-9]/i', '', $str);
        $str = preg_replace('/_+/', '', $str); // ideia do Bacco :)
        return $str;
    }


    public function create()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            exit();
        }
        if($this->getParams(0) == 'create')
        {
            $plan = $_POST['plan'];
            $currency = $_POST['currency'];
            $name = $_POST['name'];
            $subdomain = $_POST['subdomain'];

            $planId = 0;

            switch ($plan) {
                case 'starter':
                    $planId = 1;
                    break;
                case 'standard':
                    $planId = 2;
                    break;
                case 'premium':
                    $planId = 3;
                    break;
                case 'enterprise':
                    $planId = 4;
                    break;
            }

            if($plan === 'starter') {

                echo $this->webstores->free($planId);
                die();
            }

            if($this->webstores->has($_POST['subdomain']))
            {
                echo Json::encode([ 'status' => false, 'message' => 'O domínio que você escolheu já existe, tente outro' ]);
                die();
            }

            $profile = new Profile();

            if(!$profile->isActived())
            {
                echo Json::encode([ 'status' => false, 'message' => 'É necessário confirmar a conta para ativar um serviço' ]);
                die();
            }

            $config = [
                'plan' => $planId,
                'name' => $name,
                'currency' => $currency,
                'subdomain' => $this->sanitizeString($subdomain)
            ];

            $preconfig = new PreConfig();
            $reference = $preconfig->register(ProfileFactory::getId(), $planId, $name, $currency, $subdomain);

            $item = [
                "title" => Plans::SERVICES[$config['plan']]['name'],
                "quantity" => 1,
                "currency_id" => "BRL",
                "unit_price" => (double) Plans::SERVICES[$config['plan']]['price']
            ];

            $items = [ $item ];

            $backUrls = [
                "success" => 'https://app.cashmarket.com.br/webstores',
                "failure" => 'https://app.cashmarket.com.br/webstores',
                "pending" => 'https://app.cashmarket.com.br/webstores'
            ];

            $preference_data = [
                "items" => $items,
                "back_urls" => $backUrls,
                "auto_return" => "all",
                "notification_url" => "https://app.cashmarket.com.br/webservice/notifications/mercadopago/KR95485231648897",
                "external_reference" => $reference
            ];

            $preference = $this->mp->create_preference($preference_data);

            $url = $preference['response']['init_point'];

            echo Json::encode([ 'status' => true, 'free' => false, 'link' => $url ]);
            die();
        }
        $this->view();
    }

    public function actions()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            exit();
        }
        if($this->getParams(0) == 'delete')
        {
            $profile = new Profile();

            $profile_id = $profile->getId();

            $model = new Model();

            $isMy = $model->select("SELECT COUNT(*) as total FROM `webstores` WHERE `webstore_CLIENT_ID`={$profile_id} AND `webstore_ID`={$_POST['id']}")->total;

            if($isMy > 0)
            {
                $model->delete([ 'webstore_ID' => $_POST['id'] ], 'webstores');
                unset($_SESSION['WebstoreLogged']);
            }
        }
        if($this->getParams(0) == 'login')
        {
            $profile = new Profile();

            $profile_id = $profile->getId();

            $model = new Model();

            $isMy = $model->select("SELECT COUNT(*) as total FROM `webstores` WHERE `webstore_CLIENT_ID`={$profile_id} AND `webstore_ID`={$_POST['id']}")->total;

            if($isMy > 0)
            {
                $_SESSION['WebstoreLogged'] = $_POST['id'];
            }
        }
    }

}
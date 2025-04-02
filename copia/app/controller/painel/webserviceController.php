<?php

namespace app\controller\painel;

use app\api\clients\Webstores;
use app\api\webstores\admin\PreConfig;
use app\lib\Config;
use app\lib\Controller;

class webserviceController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function notifications()
    {
        $gateway = $this->getParams(0);
        $client  = $this->getParams(1);

        if($gateway == 'paypal')
        {
            $arry = [
                'id'        => $_POST['txn_id'],
                'status'    => $_POST['payment_status'],
                'reference' => $_POST['custom'],
                'amount'    => $_POST['mc_gross'] - $_POST['mc_fee'],
                'gross'     => $_POST['mc_gross'],
                'email'     => $_POST['payer_email'],
                'name'      => $_POST['first_name'] . " " . $_POST['last_name'],
                'method'    => $_POST['payment_type'],
                'paid'      => ($_POST['payment_status'] == 'Completed') ? 1 : 0
            ];

            $paypal = json_decode(json_encode($arry));

            $reference = $paypal->reference;
            $email     = $paypal->email;

            $code = $paypal->id;
            $name = $paypal->name;
            $method = $paypal->method;
            $gross = $paypal->gross;
            $amount = $paypal->amount;
            $status = $paypal->status;

            if($paypal->status == "Completed")
            {
                if($client == 'KR95485231648897')
                {

                    $preconfig = new PreConfig();
                    $config = $preconfig->get($reference);

                    $webstores = new Webstores();

                    if(!$webstores->has($config->domain))
                    {
                        $webstore = $webstores->create($config->plan, $config->name, $config->domain, $config->currency);

                        $expire = date("Y-m-d", strtotime(date("Y-m-d") . ' + 30 days'));
                        $delete_in = date("Y-m-d", strtotime(date("Y-m-d") . ' + 37 days'));

                        $webstores->setExpire($webstore, $expire, $delete_in);
                    }

                    die();

                }
            }
        }

        if ($gateway == 'mercadopago')
        {
            if($client == 'KR95485231648897')
            {
                try {
                    $mp = new \MP(Config::MERCADOPAGO_CLIENT, Config::MERCADOPAGO_SECRET);
                }catch (\MercadoPagoException $e)
                {
                    die();
                }

                $payment_info = $mp->get_payment_info($_GET['id']);
                if ($payment_info["status"] == 200) {
                    $data = $payment_info['response']['collection'];
                    $code      = $data['id'];
                    $status    = $data['status'];
                    $gross     = $data['transaction_amount'];
                    $amount    = $data['net_received_amount'];
                    $method    = $data['payment_type'];
                    $reference = $data['external_reference'];

                    $name = (isset($data['payer']['first_name'])) ? $data['payer']['first_name']." ".$data['payer']['last_name'] : 'indefinido';
                    $email     = $data['payer']['email'];
                    if($amount == 0 || empty($amount))
                    {
                        $amount = $gross + ((4.99 * $gross) / 100);
                    }

                    if($status == "approved") {
                        $preconfig = new PreConfig();
                        $config = $preconfig->get($reference);

                        $webstores = new Webstores();

                        if(!$webstores->has($config->domain))
                        {
                            $webstore = $webstores->create($config->plan, $config->name, $config->domain, $config->currency);

                            $expire = date("Y-m-d", strtotime(date("Y-m-d") . ' + 30 days'));
                            $delete_in = date("Y-m-d", strtotime(date("Y-m-d") . ' + 37 days'));

                            $webstores->setExpire($webstore, $expire, $delete_in);
                        }

                        die();
                    }

                }
            }
            return;
        }

    }

}
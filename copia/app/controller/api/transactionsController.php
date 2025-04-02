<?php

namespace app\controller\api;

use app\api\http\PayPal;
use app\api\http\SendGrid;
use app\api\webstores\Gateways;
use app\api\webstores\ManualSend;
use app\api\webstores\References;
use app\api\webstores\Transaction;
use app\lib\Controller;
use PagSeguro\Configuration\Configure;
use PagSeguro\Library;

class transactionsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function notification()
    {
        $wid = $this->getParams(1);
        $gateway = $this->getParams(0);

        $gateways = new Gateways($wid);
        $gateways = $gateways->data();

        $transaction = new Transaction();

        if($gateway == 'pagseguro')
        {
            if(isset($_POST['notificationType']) && $_POST['notificationType'] == 'transaction') {
                $email = $gateways->pagseguro_EMAIL;
                $token = $gateways->pagseguro_TOKEN;

                $url = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/' . $_POST['notificationCode'] . '?email=' . $email . '&token=' . $token;

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $transactionCH = curl_exec($curl);
                curl_close($curl);

                if ($transactionCH == 'Unauthorized') {
                    exit;
                }

                $response = simplexml_load_string($transactionCH);
                $response = $this->xml2array($response);

                $code = $response['code'];
                $name = $response['sender']['name'];
                $email = $response['sender']['email'];
                $reference = $response['reference'];
                $net = $response['netAmount'];
                $gross = $response['grossAmount'];
                $status = $response['status'];
                $payment_type = $response['paymentMethod']['code'];

                $transaction->save($wid, 'PagSeguro', $code, $name, $email, $reference, $gross, $net, $payment_type, $status, 0);

                if($status == 3)
                {
                    if(!$transaction->hasPaid($code))
                    {
                        $transaction->setPaid($code);

                        $references = new References();

                        $reference = $references->get($reference);
                        $products = $reference->reference_PRODUCTS;
                        $products = explode(",", $products);

                        $manualSend = new ManualSend($wid);

                        foreach ($products as $product)
                        {
                            $info = explode(":", $product);

                            $pid = $info[0];
                            $qnt = $info[1];

                            for ($i = 0; $i < $qnt; $i++)
                            {
                                $manualSend->dispense($pid, $reference->reference_USERNAME);
                            }
                        }
                    }
                }

            }

        }

        if($gateway == 'mercadopago')
        {
            try {
                $mp = new \MP($gateways->mercadopago_ID, $gateways->mercadopago_SECRET);
            }catch (\MercadoPagoException $e)
            {
                die($e->getMessage());
            }

            $payment_info = $mp->get_payment_info($_GET['id']);

            if ($payment_info["status"] == 200) {
                $data = $payment_info['response']['collection'];

                $code      = $data['id'];
                $status    = $data['status'];
                $gross     = $data['transaction_amount'];
                $net    = $data['net_received_amount'];
                $reference = $data['external_reference'];
                $name = (isset($data['payer']['first_name'])) ? $data['payer']['first_name'] . " " . $data['payer']['last_name'] : 'indefinido';
                $email     = empty($data['payer']['email']) ? 'indefinido' : $data['payer']['email'];
                $payment_type = $data['payment_type'];

                if($net == 0 || empty($net))
                {
                    $net = ($gross - (4.99 * $gross) / 100);
                }

                $transaction->save($wid, 'MercadoPago', $code, $name, $email, $reference, $gross, $net, $payment_type, $status, 0);

                if($status == "approved")
                {
                    if(!$transaction->hasPaid($code))
                    {
                        $transaction->setPaid($code);

                        $references = new References();

                        $reference = $references->get($reference);
                        $products = $reference->reference_PRODUCTS;
                        $products = explode(",", $products);

                        $manualSend = new ManualSend($wid);

                        foreach ($products as $product)
                        {
                            $info = explode(":", $product);

                            $pid = $info[0];
                            $qnt = $info[1];

                            for ($i = 0; $i < $qnt; $i++)
                            {
                                $manualSend->dispense($pid, $reference->reference_USERNAME);
                            }
                        }
                    }
                }
            }
        }

        if($gateway == 'paypal')
        {
            $paypal = new PayPal();

            if($paypal->isIPNValid($_POST))
            {
                $reference = $_POST['custom'];
                $email = $_POST['payer_email'];
                $code = $_POST['txn_id'];
                $gross = $_POST['mc_gross'];
                $net = $_POST['mc_gross'] - $_POST['mc_fee'];
                $status = $_POST['payment_status'];
                $name = $_POST['first_name'] . " " . $_POST['last_name'];
                $payment_type = 'PayPal';

                $transaction->save($wid, 'PayPal', $code, $name, $email, $reference, $gross, $net, $payment_type, $status, 0);

                if ($status == "Completed") {
                    if(!$transaction->hasPaid($code))
                    {
                        $transaction->setPaid($code);
                        $references = new References();

                        $reference = $references->get($reference);
                        $products = $reference->reference_PRODUCTS;
                        $products = explode(",", $products);

                        $manualSend = new ManualSend($wid);

                        foreach ($products as $product)
                        {
                            $info = explode(":", $product);

                            $pid = $info[0];
                            $qnt = $info[1];

                            for ($i = 0; $i < $qnt; $i++)
                            {
                                $manualSend->dispense($pid, $reference->reference_USERNAME);
                            }
                        }
                    }
                }

            }
        }
    }

    private function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

        return $out;
    }

}
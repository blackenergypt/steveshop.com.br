<?php

namespace app\controller\default_template;

use app\api\http\PayPal;
use app\api\webstores\Cart;
use app\api\webstores\Discounts;
use app\api\webstores\Gateways;
use app\api\webstores\Gifts;
use app\api\webstores\References;
use app\lib\Controller;
use app\lib\Model;
use PagSeguro\Configuration\Configure;
use PagSeguro\Domains\Requests\Payment;
use PagSeguro\Library;

class checkoutController extends Controller
{

    public $wid;

    public function __construct()
    {
        parent::__construct();

        $model = new Model();

        $subdomain = SUBDOMAIN;
        $store_id = $model->select("SELECT * FROM `webstores` WHERE `webstore_SUBDOMAIN`='{$subdomain}'")->webstore_ID;

        $this->wid = $store_id;

        $this->setLayout("core");
    }

    public function index()
    {
        $wid = $this->wid;

        $cart = new Cart();
        $gateways = new Gateways($wid);

        $gateway = $_POST['gateway'];
        $products = $cart->products();

        $references = new References();
        $reference = $references->add($products, $_SESSION['Player'], '-');

        if($gateway == 'pagseguro')
        {
            try {
                Library::initialize();
                Library::cmsVersion()->setName("SteveShop")->setRelease("1.0.0");
                Library::moduleVersion()->setName("SteveShop")->setRelease("1.0.0");

                $environment = 'production';

                Configure::setEnvironment($environment);
                Configure::setAccountCredentials($gateways->data()->pagseguro_EMAIL, $gateways->data()->pagseguro_TOKEN);
                Configure::setCharset('UTF-8');

                $payment = new Payment();

                if(isset($_SESSION['WebstoreCupom'])) {
                    $discounts = new Discounts($this->wid);
                    $gifts = new Gifts($this->wid);

                    $discount = $discounts->has($_SESSION['WebstoreCupom']);
                    $gift = $gifts->has($_SESSION['WebstoreCupom']);

                    $subtotal = $cart->subtotal();

                    $discountAmount = "";

                    if($discount) {
                        $percent = $discount->discount_PERCENT;
                        $discountAmount = ($subtotal * $percent) / 100;
                    }elseif($gift) {
                        $discountAmount = $subtotal - $gift->gift_DISCOUNT;
                    }

                    $perProduct = $discountAmount / count($products);

                    foreach ($products as $product)
                    {
                        $discountPerProduct = $perProduct / $product['qnt'];
                        $payment->addItems()->withParameters(
                            $product['id'],
                            $product['title'],
                            $product['qnt'],
                            number_format(($product['price'] - $discountPerProduct), 2, '.', '')
                        );
                    }
                }else{
                    foreach ($products as $product)
                    {
                        $payment->addItems()->withParameters(
                            $product['id'],
                            $product['title'],
                            $product['qnt'],
                            number_format($product['price'], 2, '.', '')
                        );
                    }
                }

                $payment->addMetadata()->withParameters('GAME_NAME', 'MINECRAFT');

                $payment->setCurrency('BRL');
                $payment->setReference($reference);
                $payment->setRedirectUrl('https://' . SUBDOMAIN . '.steveshop.com.br/');
                $payment->setNotificationUrl('https://api.steveshop.com.br/transactions/notification/pagseguro/'.$wid);

                $url = $payment->register(Configure::getAccountCredentials());

                unset($_SESSION['Cart']);

                unset($_SESSION['WebstoreCupom']);

                echo json_encode(['success' => true, 'link' => $url ]);

                die();
            }catch (\Exception $e)
            {
                die(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        if($gateway == 'mercadopago')
        {
            try {
                $mp = new \MP($gateways->data()->mercadopago_ID, $gateways->data()->mercadopago_SECRET);
            }catch (\MercadoPagoException $e)
            {
                die($e->getMessage());
            }

            $items = [];

            if(isset($_SESSION['WebstoreCupom'])) {
                $discounts = new Discounts($this->wid);
                $gifts = new Gifts($this->wid);

                $discount = $discounts->has($_SESSION['WebstoreCupom']);
                $gift = $gifts->has($_SESSION['WebstoreCupom']);

                $subtotal = $cart->subtotal();

                $discountAmount = "";

                if($discount) {
                    $percent = $discount->discount_PERCENT;
                    $discountAmount = ($subtotal * $percent) / 100;
                }elseif($gift) {
                    $discountAmount = $subtotal - $gift->gift_DISCOUNT;
                }

                $perProduct = $discountAmount / count($products);

                foreach ($products as $product)
                {
                    $discountPerProduct = $perProduct / $product['qnt'];
                    $items[] = [
                        "title" => $product['title'],
                        "quantity" => intval($product['qnt']),
                        "currency_id" => "BRL",
                        "unit_price" => floatval(($product['price'] - $discountPerProduct))
                    ];
                }
            }else{
                foreach ($products as $product)
                {
                    $items[] = [
                        "title" => $product['title'],
                        "quantity" => intval($product['qnt']),
                        "currency_id" => "BRL",
                        "unit_price" => floatval($product['price'])
                    ];
                }
            }

            $backUrls = array("success" => 'https://' . SUBDOMAIN . '.steveshop.com.br/', "failure" => 'https://' . SUBDOMAIN . '.steveshop.com.br/', "pending" => 'https://' . SUBDOMAIN . '.steveshop.com.br/');

            $preference_data = [
                "items" => $items,
                "back_urls" => $backUrls,
                "auto_return" => "all",
                "notification_url" => 'https://api.steveshop.com.br/transactions/notification/mercadopago/'.$wid,
                "external_reference" => $reference
            ];

            $preference = $mp->create_preference($preference_data);

            $url = $preference['response']['init_point'];

            unset($_SESSION['Cart']);
            unset($_SESSION['WebstoreCupom']);

            echo json_encode([ 'success' => true, 'link' => $url ]);

            die();
        }
        if($gateway=='paypal'){

            $paypal = new PayPal();

            $paypal->setCredential($gateways->data()->paypal_EMAIL);
            $paypal->setReference($reference);

            if(isset($_SESSION['WebstoreCupom'])) {
                $discounts = new Discounts($this->wid);
                $gifts = new Gifts($this->wid);

                $discount = $discounts->has($_SESSION['WebstoreCupom']);
                $gift = $gifts->has($_SESSION['WebstoreCupom']);

                $subtotal = $cart->subtotal();

                $discountAmount = "";

                if($discount) {
                    $percent = $discount->discount_PERCENT;
                    $discountAmount = ($subtotal * $percent) / 100;
                }elseif($gift) {
                    $discountAmount = $subtotal - $gift->gift_DISCOUNT;
                }

                $perProduct = $discountAmount / count($products);

                foreach ($products as $product)
                {
                    $discountPerProduct = $perProduct / $product['qnt'];
                    $paypal->addItem($product['title'], ($product['price'] - $discountPerProduct), $product['qnt']);
                }
            }else{
                foreach ($products as $product)
                {
                    $paypal->addItem($product['title'], $product['price'], $product['qnt']);
                }
            }

            $paypal->setReturnURL('https://' . SUBDOMAIN . '.steveshop.com.br/');
            $paypal->setCancelURL('https://' . SUBDOMAIN . '.steveshop.com.br/');
            $paypal->setNotificationURL('https://api.steveshop.com.br/transactions/notification/paypal/'.$wid);

            $link = $paypal->checkout();

            unset($_SESSION['Cart']);

            unset($_SESSION['WebstoreCupom']);

            echo json_encode([ 'success' => true, 'link' => $link ]);

            die();
        }
    }

}
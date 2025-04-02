<?php

namespace app\controller\default_template;

use app\api\template\Config;
use app\api\template\Template;
use app\api\webstores\Cart;
use app\api\webstores\Discounts;
use app\api\webstores\Gifts;
use app\api\webstores\Pages;
use app\api\webstores\SEO;
use app\lib\Controller;
use app\lib\Model;

class cartController extends Controller
{

    public $seo;
    public $wid;
    public $cart;
    public $subtitle = 'Carrinho';
    public $config;
    public $pages;
    public $template;
    public $discountAmount = 0;

    public function __construct()
    {
        parent::__construct();

        $this->cart = new Cart();
        $model = new Model();

        $subdomain = SUBDOMAIN;
        $store_id = $model->select("SELECT * FROM `webstores` WHERE `webstore_SUBDOMAIN`='{$subdomain}'")->webstore_ID;

        $this->wid = $store_id;

        $this->seo = new SEO($store_id);
        $this->config = new Config($store_id);
        $this->pages = new Pages($store_id);
        $this->template = new Template($store_id);

        $this->setLayout("core");
    }

    public function index()
    {
        if(!isset($_SESSION['Player'])) {

            if(isset($_POST['nickname']))
            {
                $_SESSION['Player'] = trim($_POST['nickname']);
                header('Location: /cart');
            }

            $this->view("setname");
            return;
        }

        if(isset($_GET['cupom']) && !empty($_GET['cupom'])) {
            $discounts = new Discounts($this->wid);
            $gifts = new Gifts($this->wid);

            $discount = $discounts->has($_GET['cupom']);
            $gift = $gifts->has($_GET['cupom']);

            $subtotal = $this->cart->subtotal();

            if($discount) {
                $percent = $discount->discount_PERCENT;

                $discountAmount = ($subtotal * $percent) / 100;

                $this->discountAmount = $discountAmount;

                $_SESSION['WebstoreCupom'] = $_GET['cupom'];
            }elseif($gift) {
                $discountAmount = $subtotal - $gift->gift_DISCOUNT;
                $this->discountAmount = $discountAmount;

                $_SESSION['WebstoreCupom'] = $_GET['cupom'];
            }
        }

        $this->view();
    }

    public function actions()
    {
        if($this->getParams(0) == 'add')
        {
            $this->cart->add($_POST['id']);
            die();
        }
        if($this->getParams(0) == 'minus')
        {
            $this->cart->minus($_POST['id']);
            die();
        }
        if($this->getParams(0) == 'remove')
        {
            $this->cart->remove($_POST['id']);
            die();
        }
    }

}
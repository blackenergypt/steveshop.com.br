<?php

namespace app\controller\default_template;

use app\api\template\Config;
use app\api\template\Template;
use app\api\webstores\Pages;
use app\api\webstores\SEO;
use app\lib\Controller;
use app\lib\Model;

class categoryController extends Controller
{

    public $seo;
    public $wid;
    public $subtitle = 'Produtos';
    public $config;
    public $pages;
    public $template;

    public function __construct()
    {
        parent::__construct();

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

                $category = empty($_GET['category']) ? '' : '&category='.$_GET['category'];

                header('Location: /category?server='.$_GET['server'].$category);
            }

            $this->view("setname");
            return;
        }

        $this->view();
    }

}
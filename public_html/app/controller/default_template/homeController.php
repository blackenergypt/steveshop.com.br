<?php

namespace app\controller\default_template;

use app\api\template\Config;
use app\api\template\Template;
use app\api\webstores\Pages;
use app\api\webstores\SEO;
use app\api\webstores\Widgets;
use app\lib\Controller;
use app\lib\Model;

class homeController extends Controller
{

    public $seo;
    public $wid;
    public $subtitle = 'Bem-vindo';
    public $config;
    public $widgets;
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
        $this->widgets = new Widgets($store_id);
        $this->pages = new Pages($store_id);
        $this->template = new Template($store_id);

        $this->setLayout("core");
    }

    public function index()
    {
        $this->view();
    }

}
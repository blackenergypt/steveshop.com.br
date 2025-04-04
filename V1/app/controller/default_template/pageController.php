<?php

namespace app\controller\default_template;

use app\api\template\Config;
use app\api\webstores\Pages;
use app\api\webstores\SEO;
use app\lib\Controller;
use app\lib\Model;

class pageController extends Controller
{

    public $seo;
    public $wid;
    public $config;
    public $subtitle = 'Bem-vindo';
    public $content = "";
    public $pages;

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

        $data = $this->pages->getByLink($this->getPage());

        $this->subtitle = $data->page_TITLE;
        $this->content = $data->page_CONTENT;

        $this->setLayout("core");
    }

    public function index()
    {
        $this->view();
    }

}
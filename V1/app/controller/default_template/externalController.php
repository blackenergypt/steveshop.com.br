<?php

namespace app\controller\default_template;

use app\api\template\Config;
use app\api\template\Template;
use app\api\webstores\Pages;
use app\api\webstores\SEO;
use app\api\webstores\Widgets;
use app\lib\Controller;
use app\lib\Model;

class externalController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("core");
    }

    public function index()
    {
        die();
    }

    public function css()
    {
        $model = new Model();

        $subdomain = SUBDOMAIN;
        $store_id = $model->select("SELECT * FROM `webstores` WHERE `webstore_SUBDOMAIN`='{$subdomain}'")->webstore_ID;

        $template = new Template($store_id);

        header("Content-type: text/css; charset: UTF-8");

        echo $template->get('styles');
    }

    public function js()
    {
        $model = new Model();

        $subdomain = SUBDOMAIN;
        $store_id = $model->select("SELECT * FROM `webstores` WHERE `webstore_SUBDOMAIN`='{$subdomain}'")->webstore_ID;

        $template = new Template($store_id);

        header("Content-Type: text/javascript");

        echo $template->get('scripts');
    }

}
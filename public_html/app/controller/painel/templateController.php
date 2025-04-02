<?php

namespace app\controller\painel;

use app\api\clients\Authentication;
use app\api\clients\Webstores;
use app\api\template\Config;
use app\api\webstores\admin\Webstore;
use app\api\webstores\Pages;
use app\api\webstores\SEO;
use app\api\webstores\Widgets;
use app\lib\Controller;

class templateController extends Controller
{

    private $webstores;
    public $seo;
    public $config;
    public $widgets;
    public $pages;
    public $webstore;

    public function __construct()
    {
        parent::__construct();

        $this->webstores = new Webstores();
        $this->webstore = new Webstore();

        if(!$this->webstores->has())
        {
            header("Location: /");
            die();
        }

        if(!isset($_SESSION['WebstoreLogged']))
        {
            header("Location: /webstores");
            die();
        }

        $this->seo = new SEO($_SESSION['WebstoreLogged']);
        $this->config = new Config($_SESSION['WebstoreLogged']);
        $this->widgets = new Widgets($_SESSION['WebstoreLogged']);
        $this->pages = new Pages($_SESSION['WebstoreLogged']);

        $this->setLayout("core");
    }

    public function index()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'update')
        {
            echo $this->seo->save();
            die();
        }

        $this->view();
    }

    public function custom()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'update')
        {
            echo $this->config->save();
            die();
        }

        $this->view();
    }

    public function widgets()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'update')
        {
            echo $this->widgets->save();
            die();
        }

        $this->view();
    }

    public function pages()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'actions')
        {
            if($this->getParams(1) == 'add')
            {
                echo $this->pages->add();
                die();
            }
            if($this->getParams(1) == 'delete')
            {
                $this->pages->del();
                die();
            }
            if($this->getParams(1) == 'show')
            {
                $this->pages->setShow();
                die();
            }
            if($this->getParams(1) == 'update')
            {
                echo $this->pages->edit();
                die();
            }
        }

        $this->view();
    }

}
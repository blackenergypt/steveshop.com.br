<?php

namespace app\controller\painel;

use app\api\clients\Authentication;
use app\api\clients\Webstores;
use app\api\webstores\admin\Webstore;
use app\api\webstores\Domain;
use app\api\webstores\Gateways;
use app\lib\Controller;

class configController extends Controller
{

    public $webstores;
    public $gateways;
    public $webstore;

    public function __construct()
    {
        parent::__construct();

        $this->webstores = new Webstores();
        $this->webstore = new Webstore();
        $this->gateways = new Gateways($_SESSION['WebstoreLogged']);

        if(!isset($_SESSION['WebstoreLogged']))
        {
            header("Location: /webstores");
            die();
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
        header("Location: /config");
        die();
    }

    public function gateways()
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
        if($this->getParams(0) == 'update')
        {
            echo $this->gateways->att();
            die();
        }

        $this->view();
    }

    public function integration()
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

    public function domain()
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
        if($this->getParams(0) == 'update')
        {
            $domain = new Domain();

            echo $domain->att();

            die();
        }

        $this->view();
    }

}
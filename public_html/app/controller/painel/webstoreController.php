<?php

namespace app\controller\painel;

use app\api\clients\Authentication;
use app\api\clients\Webstores;
use app\api\webstores\admin\Categories;
use app\api\webstores\admin\Packages;
use app\api\webstores\admin\Servers;
use app\api\webstores\Discounts;
use app\api\webstores\Gifts;
use app\api\webstores\ManualSend;
use app\lib\Controller;
use app\lib\Model;

class webstoreController extends Controller
{

    public $server;
    public $categories;
    private $webstores;
    public $packages;
    public $gifts;
    public $discounts;

    public function __construct()
    {
        parent::__construct();

        $this->server = new Servers();
        $this->categories = new Categories();
        $this->webstores = new Webstores();
        $this->packages = new Packages();
        $this->discounts = new Discounts($_SESSION['WebstoreLogged']);
        $this->gifts = new Gifts($_SESSION['WebstoreLogged']);

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

        $this->setLayout("core");
    }

    public function index()
    {
        header("Location: /");
        die();
    }

    public function servers()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            exit();
        }
        if($this->getParams(0) == 'add')
        {
            echo $this->server->add();
            die();
        }
        if($this->getParams(0) == 'delete')
        {
            $this->server->del();
            die();
        }
        if($this->getParams(0) == 'show')
        {
            $this->server->setShow();
            die();
        }
        if($this->getParams(0) == 'update')
        {
            echo $this->server->att();
            die();
        }

        $this->view();
    }

    public function categories()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            exit();
        }
        if($this->getParams(0) == 'add')
        {
            echo $this->categories->add();
            die();
        }
        if($this->getParams(0) == 'delete')
        {
            $this->categories->del();
            die();
        }
        if($this->getParams(0) == 'show')
        {
            $this->categories->setShow();
            die();
        }
        if($this->getParams(0) == 'update')
        {
            echo $this->categories->att();
            die();
        }

        $this->view();
    }

    public function packages()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'categories')
        {
            $categories = $this->categories->data($_GET['id']);

            foreach ($categories as $category) {
                echo "<option value='{$category->category_ID}'>{$category->category_NAME}</option>";
            }

            die();
        }

        $this->view();
    }

    public function discounts()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }

        if($this->getParams(0) == 'add')
        {
            echo $this->discounts->add();
            die();
        }
        if($this->getParams(0) == 'delete')
        {
            $this->discounts->del();
            die();
        }

        $this->view();
    }

    public function gifts()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'add')
        {
            echo $this->gifts->add();
            die();
        }
        if($this->getParams(0) == 'delete')
        {
            $this->gifts->del();
            die();
        }

        $this->view();
    }

    public function send()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'add')
        {
            $sender = new ManualSend($_SESSION['WebstoreLogged']);

            echo $sender->send();
            die();
        }
        if($this->getParams(0) == 'packages')
        {
            $model = new Model();

            $packages = $model->select("SELECT * FROM `webstores_packages` WHERE `package_WID`={$_SESSION['WebstoreLogged']} AND `package_SERVER`={$_GET['sid']}", 'all');

            foreach ($packages as $package)
            {
                echo "<option value='{$package->package_ID}'>{$package->package_NAME}</option>";
            }

            die();
        }
        $this->view();
    }

    public function logs()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }

        $this->view();
    }

    public function transactions()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }

        $this->view();
    }

    public function actions()
    {
        if(!Authentication::logged())
        {
            header('Location: /account/login');
            die();
        }
        if($this->getParams(0) == 'package')
        {
            if($this->getParams(1) == 'add')
            {
                echo $this->packages->add();
                die();
            }
            if($this->getParams(1) == 'edit')
            {
                echo $this->packages->edit();
                die();
            }
            if($this->getParams(1) == 'delete')
            {
                echo $this->packages->del();
                die();
            }
        }
    }

}
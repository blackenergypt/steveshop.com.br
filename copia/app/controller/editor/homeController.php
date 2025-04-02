<?php

namespace app\controller\editor;

use app\api\template\Template;
use app\lib\Controller;
use app\lib\Security;

class homeController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->setLayout('core');
    }

    public function index() {

        if(!isset($_SESSION['Theme@CashMarket']))
        {
            header('Location: /login');
            die();
        }
        return $this->view();
    }

    public function get() {
        if(!isset($_SESSION['Theme@CashMarket']))
        {
            header('Location: /login');
            die();
        }
        if(!Security::ajax())
        {
            die();
        }

        header("Content-Type: text/plain");

        $template = new Template($_SESSION['Theme@CashMarket']);

        echo $template->get($_POST['value']);
    }

    public function save() {
        if(!isset($_SESSION['Theme@CashMarket']))
        {
            header('Location: /login');
            die();
        }
        if(!Security::ajax())
        {
            die();
        }

        $template = new Template($_SESSION['Theme@CashMarket']);

        $template->save($_POST['value'], addslashes($_POST['content']));
        die();
    }

    public function reset() {
        if(!isset($_SESSION['Theme@CashMarket']))
        {
            header('Location: /login');
            die();
        }

        $template = new Template($_SESSION['Theme@CashMarket']);

        $template->reset();

        header('Location: /');
    }

    public function logout() {
        if(!isset($_SESSION['Theme@CashMarket']))
        {
            header('Location: /login');
            die();
        }

        session_destroy();

        header('Location: /login');
    }
}
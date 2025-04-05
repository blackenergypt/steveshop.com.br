<?php

namespace app\controller\painel;

use app\api\clients\Authentication;
use app\api\clients\Webstores;
use app\api\webstores\Transactions;
use app\lib\Controller;

class homeController extends Controller
{

    public $webstores;
    public $transactions;

    public function __construct()
    {
        parent::__construct();

        $this->webstores = new Webstores();
        $this->transactions = new Transactions($_SESSION['WebstoreLogged']);

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

        if(!isset($_SESSION['WebstoreLogged']))
        {
            header("Location: /webstores");
            die();
        }
        $this->view();
    }

}
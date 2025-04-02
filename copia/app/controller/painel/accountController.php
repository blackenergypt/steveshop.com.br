<?php

namespace app\controller\painel;

use app\api\clients\Authentication;
use app\api\clients\Webstores;
use app\factory\clients\AuthenticationFactory;
use app\lib\Controller;
use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;

class accountController extends Controller
{

    public $title = '';
    public $tokenId, $tokenValue, $inputs;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("_account");
    }

    public function index()
    {
        if(Authentication::logged())
        {
            header('Location: /');
            exit();
        }

        header('Location: /account/login');
        exit();
    }

    public function login()
    {
        if(Authentication::logged())
        {
            header('Location: /');
            exit();
        }
        $this->title = 'Login';

        $this->tokenId = Forms::getTokenID();
        $this->tokenValue = Forms::getToken();
        $this->inputs = Forms::formNames(['email', 'password', 'mode'], false);

        if($this->getParams(0) == 'action')
        {
            sleep(2);

            $try = AuthenticationFactory::login($this->inputs);

            echo $try;

            $json = Json::decode($try);

            if($json->success)
            {
                $this->inputs = Forms::formNames(['email', 'password', 'mode'], true);
            }

            die();
        }

        $this->view();
    }

    public function register()
    {
        if(Authentication::logged())
        {
            header('Location: /');
            die();
        }
        $this->title = 'Criando usuário';

        $this->tokenId = Forms::getTokenID();
        $this->tokenValue = Forms::getToken();
        $this->inputs = Forms::formNames(['name', 'email', 'password', 'repeat'], false);

        if($this->getParams(0) == 'action')
        {
            $try = AuthenticationFactory::register($this->inputs);

            echo $try;

            $json = Json::decode($try);

            if($json->success)
            {
                $this->inputs = Forms::formNames(['name', 'email', 'password', 'repeat'], true);
            }

            sleep(2);

            die();
        }

        $this->view();
    }

    public function recovery()
    {
        if(Authentication::logged())
        {
            header('Location: /');
            die();
        }
        $this->title = 'Recuperar senha';
        $authentication = new Authentication();

        if($this->getParams(0) == 'action')
        {
            echo $authentication->recovery();
            die();
        }
        if($this->getParams(0) == 'set')
        {
            echo $authentication->recoveryConfirm($_POST['code']);
            die();
        }

        if(!isset($_GET['hash'])) {
            $this->view();
        }else{
            $this->view('recovery_confirm');
        }
    }

    public function active()
    {
        $model = new Model();

        $count = $model->select("SELECT COUNT(*) as row FROM `system_confirmation_email` WHERE `email_HASH` = '{$this->getParams(0)}'");
        $count = $count->row;

        if($count == 1)
        {

            $client_id = $model->select("SELECT * FROM `system_confirmation_email` WHERE `email_HASH`='{$this->getParams(0)}'")->email_CLIENT_ID;

            $model->update([ 'account_ACTIVE' => 1 ], [ 'account_ID'=> $client_id ], 'clients_accounts');

            $model->delete([ 'email_HASH' => $this->getParams(0) ], 'system_confirmation_email');

            header("Location: /");
            die('Redirecionando...');

        }else{

            header("Location: /");
            die('Redirecionando...');

        }
    }

    public function logout()
    {
        if(!Authentication::logged())
        {
            header('Location: /');
            exit();
        }

        Authentication::logout();
        header('Location: /');

        exit('Redirecionando...');
    }
}
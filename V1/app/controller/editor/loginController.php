<?php /** @noinspection ALL */

namespace app\controller\editor;

use app\lib\Controller;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class loginController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->setLayout('auth');
    }

    public function index() {

        if(isset($_SESSION['Theme@CashMarket']))
        {
            header('Location: /');
            die();
        }
        return $this->view();
    }

    public function auth() {
        if(!Security::ajax())
        {
            echo Json::encode([ 'success' => false, 'message' => 'Bloqueado' ]);
            die();
        }
        if(isset($_SESSION['Theme@CashMarket']))
        {
            echo Json::encode([ 'success' => false, 'message' => 'Você já está logado!' ]);
            die();
        }
        if(empty($_POST['code']))
        {
            echo Json::encode([ 'success' => false, 'message' => 'Informe o código de acesso.' ]);
            die();
        }
        
        $model = new Model();

        $code = strip_tags(addslashes($_POST['code']));

        $select = $model->select("SELECT * FROM `webstores` WHERE `webstore_TOKEN`='{$code}'");

        if(!$select) {
            echo Json::encode([ 'success' => false, 'message' => 'Não encontramos seu código de acesso.' ]);
            die();
        }

        $_SESSION['Theme@CashMarket'] = $select->webstore_ID;

        echo Json::encode([ 'success' => true ]);
        die();
    }
}
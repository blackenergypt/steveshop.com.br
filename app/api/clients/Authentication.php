<?php

namespace app\api\clients;

use app\api\http\SendGrid;
use app\lib\EmailSender;
use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Password;
use app\lib\ReCaptcha;
use app\lib\Security;
use app\lib\TextCrypt;

class Authentication extends Model
{

    const RECAPTCHA_SECRET = '6LemDMQUAAAAAI80d_n4IFMGDMYn5GXA5ffide0R';
    const TRY_AGAIN = 3;

    public function __construct()
    {
        if(!isset($_SESSION['P2P_Account_Try']))
        {
            $_SESSION['P2P_Account_Try'] = 0;
        }
        parent::__construct();
    }

    public function login($inputs)
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST))
        {
            return Json::encode([ 'success' => false, 'message' => 'POST could not be read' ]);
        }
        if(Forms::empty($_POST, [ $inputs['email'], $inputs['password'] ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe seus dados' ]);
        }
        if(ReCaptcha::verify(Authentication::RECAPTCHA_SECRET, $_POST['g-recaptcha-response']) == false)
        {
            return Json::encode([ 'success' => false, 'message' => 'Ops... indentificamos algo estranho! Tente novamente' ]);
        }

        $email = $_POST[$inputs['email']];

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return Json::encode([ 'success' => false, 'message' => 'Este e-mail não é válido' ]);
        }
        if(!$this->hasEmail($email))
        {
            return Json::encode([ 'success' => false, 'message' => 'Não existe uma conta neste e-mail' ]);
        }
        if($this->hasBlocked($email))
        {
            return Json::encode([ 'success' => false, 'message' => 'Sua conta está bloqueada por 30 minutos.<br>Para desbloquear, enviamos um link para o seu e-mail.' ]);
        }
        if(!Password::verify($_POST[$inputs['password']], $this->getPassword($email)))
        {
            return Json::encode([ 'success' => false, 'message' => 'Senha inválida!<br> Tente novamente' ]);
        }

        $mode = isset($_POST[$inputs['mode']]) ? true : false;
        $this->setSession('P2P_Account_Login', $this->getId($email), $mode);

        if(!isset($_COOKIE['P2P_Account_Verification']))
        {
            $html_body = file_get_contents('./app/content/emails/newlogin.html');
            $body = str_replace([ '{ip}', '{date}', '{link}' ], [ $_SERVER['REMOTE_ADDR'], date("d/m/Y à\s H:i:s"), 'https://app.steveshop.com.br/account/disconnect/' . TextCrypt::encrypt($email, 'P2PAuth') ], $html_body);

            $this->setSession('P2P_Account_Verification', 'Ok!', true);

            EmailSender::noreply($email, 'Ei, psiu, foi você?', $body);
        }else{
            $this->setSession('P2P_Account_Verification', 'Ok!', true);
        }

        return Json::encode([ 'success' => true, 'message' => 'Redirecionando...' ]);
    }

    public function register($inputs)
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST))
        {
            return Json::encode([ 'success' => false, 'message' => 'POST could not be read' ]);
        }
        if(Forms::empty($_POST, [ $inputs['name'], $inputs['email'], $inputs['password'], $inputs['repeat'] ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Preencha todos os campos abaixo' ]);
        }
        if(ReCaptcha::verify(Authentication::RECAPTCHA_SECRET, $_POST['g-recaptcha-response']) == false)
        {
            return Json::encode([ 'success' => false, 'message' => 'Ops... indentificamos algo estranho! Tente novamente' ]);
        }
        if(!filter_var($_POST[$inputs['email']], FILTER_VALIDATE_EMAIL))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe um e-mail válido!' ]);
        }
        if($this->hasEmail($_POST[$inputs['email']]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Já existe uma conta cadastrada neste e-mail.' ]);
        }
        if($_POST[$inputs['password']] != $_POST[$inputs['repeat']])
        {
            return Json::encode([ 'success' => false, 'message' => 'As senhas não estão iguais, verifique' ]);
        }
        if($this->hasPreviousAttempt())
        {
            return Json::encode([ 'success' => false, 'message' => 'Você já criou uma conta recentemente!' ]);
        }

        $data = [
            'account_NAME' => $_POST[$inputs['name']],
            'account_EMAIL' => $_POST[$inputs['email']],
            'account_PASSWORD' => Password::hash($_POST[$inputs['password']])
        ];

        $this->insert($data, 'clients_accounts');
        $id = $this->select("SELECT * FROM `clients_accounts` WHERE `account_EMAIL`='{$_POST[$inputs['email']]}'")->account_ID;

        $name = explode(" ", $_POST[$inputs['name']])[0];
        $html_body = file_get_contents('./app/content/emails/welcome.html');
        $body = str_replace([ '{name}', '{link}' ], [ $name, 'https://app.steveshop.com.br/account/active/' . hash('sha256', $_POST[$inputs['email']]) ], $html_body);

        EmailSender::noreply($_POST[$inputs['email']], 'Bem-vindo à SteveShop', $body);

        $this->insert([ 'email_CLIENT_ID' => $id, 'email_HASH' => hash('sha256', $_POST[$inputs['email']]) ], 'system_confirmation_email');

        $this->setSession('P2P_Account_Created', 'Ok');
        $this->setSession('P2P_Account_Login', $id);

        return Json::encode([ 'success' => true, 'message' => 'Redirecionando...' ]);
    }

    public static function logged()
    {
        return (isset($_SESSION['P2P_Account_Login'])) ? true : (isset($_COOKIE['P2P_Account_Login']) ? true : false);
    }

    public static function logout()
    {
        if(isset($_SESSION['P2P_Account_Login']))
        {
            unset($_SESSION['P2P_Account_Login']);
        }
        unset($_COOKIE['P2P_Account_Login']);
        setcookie('P2P_Account_Login', '', time() - 3600, '/');
    }

    public function recovery() {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST))
        {
            return Json::encode([ 'success' => false, 'message' => 'POST could not be read' ]);
        }
        if(Forms::empty($_POST, [ 'email' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe seu e-mail' ]);
        }

        $user = $this->select("SELECT * FROM `clients_accounts` WHERE `account_EMAIL`='{$_POST['email']}'");

        if(!$user) {
            return Json::encode([ 'success' => false, 'message' => 'Não encontramos seu e-mail em nosso sistema' ]);
        }

        $hash = password_hash($_POST['email'], PASSWORD_BCRYPT);

        $this->insert([
            'hash' => $hash,
            'client_id' => $user->account_ID
        ], 'recover_hash');

        $html_body = file_get_contents('./app/content/emails/recovery.html');
        $body = str_replace([ '{link}' ], [ 'https://app.steveshop.com.br/account/recovery?hash=' . $hash ], $html_body);

        EmailSender::noreply($_POST['email'], 'Recupere sua senha', $body);

        return Json::encode([ 'success' => true, 'message' => 'Enviamos um link de confirmação para o seu e-mail' ]);
    }

    public function recoveryConfirm($code) {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Request blocked' ]);
        }
        if(empty($_POST))
        {
            return Json::encode([ 'success' => false, 'message' => 'POST could not be read' ]);
        }
        if(Forms::empty($_POST, [ 'password', 'repeat' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe seus dados' ]);
        }
        if($_POST['password'] != $_POST['repeat'])
        {
            return Json::encode([ 'success' => false, 'message' => 'Senhas não são iguais' ]);
        }

        $verify = $this->select("SELECT * FROM `recover_hash` WHERE `hash`='{$code}'");

        if(count($verify) == 0) {
            return Json::encode([ 'success' => false, 'message' => 'Código de validação não existe ou já foi utilizado' ]);
        }

        $this->update([
            'account_PASSWORD' => Password::hash($_POST['password']),
        ], [ 'account_ID' => $verify->client_id ], 'clients_accounts');

        $this->delete(['hash' => $code], 'recover_hash');

        return Json::encode([ 'success' => true, 'message' => 'Senha alterada com sucesso!' ]);
    }

    private function setSession($index, $value, $mode = false)
    {
        if($mode)
        {
            return setcookie($index, $value, time()+3600*24*30, "/");
        }
        return $_SESSION[$index] = $value;
    }

    private function getPassword($email)
    {
        return $this->select("SELECT * FROM `clients_accounts` WHERE `account_EMAIL`='{$email}';")->account_PASSWORD;
    }

    private function hasEmail($email)
    {
        $select = $this->select("SELECT COUNT(*) as total FROM `clients_accounts` WHERE `account_EMAIL`='{$email}'");
        return $select->total > 0;
    }

    private function hasBlocked($email)
    {
        $id = $this->getId($email);

        $count = $this->select("SELECT COUNT(*) as total FROM `system_accounts_blocked` WHERE `block_CLIENT_ID`={$id};")->total;

        if($count > 0)
        {
            $now = date("Y-m-d H:i:s");
            $end = $this->select("SELECT * FROM `system_accounts_blocked` WHERE `block_CLIENT_ID`={$id} ORDER BY `block_ID` DESC;")->block_END_IN;

            if(strtotime($now) >= strtotime($end))
            {
                return false;
            }

            return true;
        }

        return false;
    }

    private function getId($email)
    {
        return $this->select("SELECT `account_ID` FROM `clients_accounts` WHERE `account_EMAIL`='{$email}';")->account_ID;
    }

    private function hasPreviousAttempt()
    {
        return isset($_SESSION['P2P_Account_Created']);
    }

}

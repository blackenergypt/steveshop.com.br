<?php

namespace app\controller\site;

use app\api\template\View;

class accountController {

    private $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function index()
    {
        // Redirecionar para login se acessar account/
        header('Location: ' . APP_ROOT . 'account/login');
        exit;
    }

    public function login()
    {
        // Página de login
        $this->view->setTitle("Login");
        $this->view->render('site/account/login');
    }

    public function register()
    {
        // Página de registro
        $this->view->setTitle("Criar Conta");
        $this->view->render('site/account/register');
    }

    public function recover()
    {
        // Página de recuperação de senha
        $this->view->setTitle("Recuperar Senha");
        $this->view->render('site/account/recover');
    }

} 
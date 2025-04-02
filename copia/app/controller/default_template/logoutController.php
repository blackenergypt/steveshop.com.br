<?php

namespace app\controller\default_template;

use app\lib\Controller;

class logoutController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        unset($_SESSION['Player']);
        header('Location: /');
        die();
    }

}
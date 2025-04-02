<?php

namespace app\controller\store;

use app\lib\Controller;

class homeController extends Controller
{

    public $title;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout("core");
    }

    public function index()
    {
        $this->title = 'Minha loja';
        $this->view();
    }

}
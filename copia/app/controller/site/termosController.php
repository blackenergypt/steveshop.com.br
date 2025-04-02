<?php

namespace app\controller\site;

use app\lib\Controller;

class termosController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->setLayout('core');
    }

    public function index()
    {
        $this->view();
    }

}
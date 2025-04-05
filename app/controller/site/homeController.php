<?php

namespace app\controller\site;

use app\api\http\SendGrid;
use app\lib\Controller;

class homeController extends Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->setLayout("core");
    }

    public function index()
    {
        $this->view();
    }

}
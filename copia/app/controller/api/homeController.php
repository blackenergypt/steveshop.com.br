<?php

namespace app\controller\api;

use app\lib\Controller;
use app\lib\Json;

class homeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        header("Content-Type: application/json");

        echo Json::encode([ 'status' => false, 'error' => 'Method not allowed' ]);

    }

}
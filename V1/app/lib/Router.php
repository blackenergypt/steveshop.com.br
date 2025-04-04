<?php

namespace app\lib;

class Router {

    protected $routers = [
        'site' => 'site',
        'painel' => 'painel',
        'store' => 'store',
        'default_template' => 'default_template',
        'transactions' => 'transactions'
    ];
    protected $routerOnRaiz = 'site';
    protected $onRaiz = true;

}
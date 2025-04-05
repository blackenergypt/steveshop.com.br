<?php
// Configurações de sessão PRIMEIRO
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// DEPOIS inicie a sessão
session_start();

header("Access-Control-Allow-Origin: *");

header_remove( 'X-Powered-By' );
header("X-XSS-Protection: 1; mode=block");
header("X-WebKit-CSP: policy");
header('Content-Type: text/html; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

setlocale(LC_TIME, 'pt_BR', 'pt_BR.UTF-8', 'pt_BR.utf-8', 'brazilian');
date_default_timezone_set('America/Sao_Paulo');

define('DOMAIN', "atm10.pt");


if (! empty($_SERVER['HTTPS'])) {
    $config['base_url'] = 'https://'.DOMAIN.'/';
} else {
    $config['base_url'] = 'http://'.DOMAIN.'/';
}

$http_host = $_SERVER['HTTP_HOST'];
$www = explode('.'.DOMAIN, $http_host)[0];

define('APP_ROOT', $config['base_url']);

require_once 'app/helper/Autoload.php';
require_once 'vendor/autoload.php';


use app\lib\System;

$n = 0;

for($i = -1; $i < strpos($_SERVER['HTTP_HOST'], 'steveshop'); $i++ ) {
    $n++;
}

if($n == 0)
{
    $model = new \app\lib\Model();
    $select = $model->select("SELECT COUNT(*) as total FROM `webstores` WHERE `webstore_DOMAIN`='{$www}'")->total;

    if($select == 0) {
        define('SUBDOMAIN', 'error');
    }else{
        $subdomain = $model->select("SELECT * FROM `webstores` WHERE `webstore_DOMAIN`='{$http_host}'")->webstore_SUBDOMAIN;
        define('SUBDOMAIN', $subdomain);
    }
}else{
    define('SUBDOMAIN', getSubdomain());
}

$system = new System(SUBDOMAIN);
$system->Run();

function getSubdomain()
{
    $host = $_SERVER['HTTP_HOST'];

    $info = explode('.'.DOMAIN, $host);

    if(!strpos($host, '.'.DOMAIN) || $info[0] == 'www')
    {
        return 'index';
    }

    return $info[0];
}



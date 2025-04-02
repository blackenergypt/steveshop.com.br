<?php

namespace app\lib;

use app\api\clients\Webstores;
use app\api\template\Template;
use app\api\webstores\Pages;

class System extends Router {

    private $url,
        $exploder,
        $area,
        $controller,
        $runController,
        $action,
        $params,
        $page;

    private $model;

    public function __construct($store)
    {
        if($store != 'index')
        {
            $this->model = new Model();

            $this->setUrl();
            $this->setExploder();
            $this->setStore($store);
            $this->setController();
            $this->setAction();
            $this->setParams();

            return;

        }else{
            $this->setUrl();
            $this->setExploder();
            $this->setArea();
            $this->setController();
            $this->setAction();
            $this->setParams();

            return;
        }
    }

    private function setUrl()
    {
        $this->url = isset($_GET['url']) ? trim(strip_tags($_GET['url'])) : 'home/index';
    }

    private function setExploder()
    {
        $this->exploder = explode("/", $this->url);
    }

    private function setStore($store)
    {
        if($store == 'editor')
        {
            $this->area = 'editor';

            if(!defined('APP_AREA'))
            {
                define('APP_AREA', $this->area);
            }

            return;
        }

        if($store == 'app')
        {
            $this->area = 'painel';

            if(!defined('APP_AREA'))
            {
                define('APP_AREA', $this->area);
            }

            return;
        }

        if($store == 'api')
        {
            $this->area = 'api';

            if(!defined('APP_AREA'))
            {
                define('APP_AREA', $this->area);
            }

            return;
        }

        if($store == 'transactions')
        {
            die('Blocked');
        }

        if($store == 'error')
        {
            header('HTTP/1.0 404 Not Found');
            define('STORE_SUBDOMAIN', 'Domínio não autorizado');
            include("./app/content/store/layouts/unauthorized.phtml");
            die();
        }

        $store = strtolower($store);
        $query = $this->model->select("SELECT * FROM `webstores` WHERE `webstore_SUBDOMAIN`='{$store}';");

        if(!$query)
        {
            header('HTTP/1.0 404 Not Found');
            define('STORE_SUBDOMAIN', $store);
            include("./app/content/store/layouts/notfound.phtml");
            die();
        }

        $wid = $query->webstore_ID;

        $webstore = new Webstores();

        $expire = $webstore->expireIn($wid);
        $now = date("Y-m-d");
        $datediff = strtotime($now) - strtotime($expire);
        $days = round($datediff / (60 * 60 * 24)) * (-1);

        if($days <= 0) {
            header('HTTP/1.0 404 Not Found');
            define('STORE_SUBDOMAIN', $store);
            include("./app/content/store/layouts/suspensed.phtml");
            die();
        }

        $template = new Template($wid);

        if($template->getTemplate() == 'default')
        {
            $this->area = 'default_template';
        }else{
            $this->area = 'template';
        }


        if(!defined('APP_AREA'))
        {
            define('APP_AREA', $this->area);
        }
    }

    private function setArea()
    {
        foreach ($this->routers as $index => $value)
        {
            if($this->onRaiz && $this->exploder[0] == $index)
            {
                $this->area   = $value;
                $this->onRaiz = false;
            }
        }

        $this->area = empty($this->area) ? $this->routerOnRaiz : $this->area;

        if(!defined('APP_AREA'))
        {
            define('APP_AREA', $this->area);
        }
    }

    public function actived($page)
    {
        return ($this->getController() == $page) ? 'active' : '';
    }

    public function getArea()
    {
        return $this->area;
    }

    private function setController()
    {
        if($this->area == 'default_template') {



            $model = new Model();

            $subdomain = SUBDOMAIN;
            $store_id = $model->select("SELECT * FROM `webstores` WHERE `webstore_SUBDOMAIN`='{$subdomain}'")->webstore_ID;

            $pages = new Pages($store_id);

            $set = (empty($this->exploder[0]) || is_null($this->exploder[0]) || !isset($this->exploder[0]));

            if(!$set) {

                if($pages->has($this->exploder[0])) {

                    $this->controller = 'page';
                    $this->page = $this->exploder[0];

                }else{
                    $this->controller = $this->exploder[0];
                }

            }else{
                $this->controller = 'home';
            }

        }else{
            $this->controller = $this->onRaiz ? $this->exploder[0] :
                (empty($this->exploder[1]) || is_null($this->exploder[1]) || !isset($this->exploder[1]) ? 'home' : $this->exploder[1]);
        }

    }

    public function getController()
    {
        return $this->controller;
    }

    private function setAction()
    {
        $this->action = $this->onRaiz ?
            (!isset($this->exploder[1]) || is_null($this->exploder[1]) || empty($this->exploder[1]) ? 'index' : $this->exploder[1]) :
            (!isset($this->exploder[2]) || is_null($this->exploder[2]) || empty($this->exploder[2]) ? 'index' : $this->exploder[2]);
    }

    public function getAction()
    {
        return $this->action;
    }

    private function setParams()
    {
        if($this->onRaiz)
        {
            unset($this->exploder[0], $this->exploder[1]);
        }else{
            unset($this->exploder[0], $this->exploder[1], $this->exploder[2]);
        }

        if(end($this->exploder) == null)
        {
            array_pop($this->exploder);
        }

        if(empty($this->exploder))
        {
            $this->params = [];
        }else {
            $params = [];
            foreach ($this->exploder as $value)
            {
                $params[] = $value;
            }
            $this->params = $params;
        }
    }

    public function getParams($index)
    {
        return isset($this->params[$index]) ? $this->params[$index] : null;
    }

    public function getPage()
    {
        return $this->page;
    }

    private function validateController()
    {
        if(!(class_exists($this->runController)))
        {
            header('HTTP/1.0 404 Not Found');
            define('APP_ERROR', 'Não foi possível localizar o controller '.$this->controller);
            include("./app/content/site/layouts/error.phtml");
            return false;
        }
        return true;
    }

    private function validateAction()
    {
        if(!(method_exists($this->runController, $this->action)))
        {
            header('HTTP/1.0 404 Not Found');
            define('APP_ERROR', 'Não foi possível localizar a action '.$this->action);
            include("./app/content/site/layouts/error.phtml");
        }
    }

    public function Run()
    {
        $this->runController = 'app\\controller\\' . $this->getArea() . '\\'. $this->controller . 'Controller';

        if($this->validateController())
        {
            $this->runController = new $this->runController();

            $this->validateAction();

            $act = $this->action;

            $this->runController->$act();
        }

    }
}
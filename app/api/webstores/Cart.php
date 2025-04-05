<?php

namespace app\api\webstores;

use app\api\webstores\admin\Packages;
use app\lib\Model;

class Cart
{

    public $products;

    public function __construct()
    {
        $this->products = new Packages();

        if (!isset($_SESSION['Cart'])) {
            $_SESSION['Cart'] = [];
        }
    }

    public function add($id)
    {
        if (!isset($_SESSION['Cart'][$id])) {
            $_SESSION['Cart'][$id] = 1;
        }else{
            $_SESSION['Cart'][$id] = $_SESSION['Cart'][$id] + 1;
        }
    }

    public function minus($id)
    {
        if (isset($_SESSION['Cart'][$id])) {
            if($_SESSION['Cart'][$id] == 1)
            {
                unset($_SESSION['Cart'][$id]);
            }else{
                $_SESSION['Cart'][$id] = $_SESSION['Cart'][$id] - 1;
            }
        }
    }

    public function remove($id)
    {
        $_SESSION['Cart'][$id] = 1;
        unset($_SESSION['Cart'][$id]);
    }

    public function products()
    {
        $products = [];

        $model = new Model();

        foreach ($_SESSION['Cart'] as $id => $qnt)
        {

            $product = $this->products->dataWID($id);

            $server = $model->select("SELECT * FROM `webstores_servers` WHERE `server_ID`={$product->package_SERVER}")->server_NAME;

            $products[$id]['id'] = $id;
            $products[$id]['image'] = $product->package_IMAGE;
            $products[$id]['title'] = $product->package_NAME;
            $products[$id]['server'] = $server;
            $products[$id]['price'] = ($product->package_PROMOTIONAL == 0) ? $product->package_PRICE : $product->package_PROMOTIONAL;
            $products[$id]['qnt'] = $qnt;

        }

        return $products;
    }

    public function subtotal()
    {
        $products = $this->products();
        $subtotal = 0;

        foreach ($products as $product) {
            $subtotal += $product['price'] * $product['qnt'];
        }

        return $subtotal;
    }

    public static function count()
    {
        if (!isset($_SESSION['Cart'])) {
            $_SESSION['Cart'] = [];
        }
        return count($_SESSION['Cart']);
    }

}
<?php

namespace app\api\webstores;

use app\lib\Model;

class References extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($products, $username, $cupom)
    {
        $text = "";

        foreach ($products as $product)
        {
            $text .= "{$product['id']}:{$product['qnt']},";
        }

        $size = strlen($text);
        $text = substr($text,0, $size-1);

        $insert = $this->insert([ 'reference_PRODUCTS' => $text, 'reference_USERNAME' => $username, 'reference_CUPOM' => $cupom ], 'webstores_references');

        return $insert['id'];
    }

    public function get($id)
    {
        return $this->select("SELECT * FROM `webstores_references` WHERE `reference_ID`={$id}");
    }

}
<?php

namespace app\api\template;

use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;
use app\lib\Upload;

class Config extends Model
{

    private $wid;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;

        $this->create();
    }

    public function save()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        $data = $this->data();


        $bg = Upload::image('./cdn/images/', $_FILES['config_BACKGROUND_IMAGE']);

        if($bg['status'] == false)
        {
            return Json::encode([ 'success' => false, 'message' => $bg['message'] ]);
        }

        if($bg['src'] == 'NULL')
        {
            $bg['src'] = '-';
        }


        $obj = [
            'config_BACKGROUND_COLOR' => (empty($_POST['config_BACKGROUND_COLOR'])) ? $data->config_BACKGROUND_COLOR : $_POST['config_BACKGROUND_COLOR'],
            'config_BACKGROUND_IMAGE' => $bg['src'],
            'config_MENU_BACKGROUND' => (empty($_POST['config_MENU_BACKGROUND'])) ? $data->config_MENU_BACKGROUND : $_POST['config_MENU_BACKGROUND'],
            'config_MENU_COLOR' => (empty($_POST['config_MENU_COLOR'])) ? $data->config_MENU_COLOR : $_POST['config_MENU_COLOR'],
            'config_PRIMARY_COLOR' => (empty($_POST['config_PRIMARY_COLOR'])) ? $data->config_PRIMARY_COLOR : $_POST['config_PRIMARY_COLOR']
        ];

        $this->update($obj, [ 'config_WID' => $this->wid ], 'webstores_template_config');

        return Json::encode([ 'success' => true, 'message' => 'Atualizado com sucesso' ]);
    }

    public function data()
    {
        return $this->select("SELECT * FROM `webstores_template_config` WHERE `config_WID`={$this->wid}");
    }

    private function create()
    {
        $select = $this->select("SELECT COUNT(*) as total FROM `webstores_template_config` WHERE `config_WID`={$this->wid}")->total;

        if($select == 0)
        {
            $obj = [
                'config_WID' => $this->wid,
                'config_BACKGROUND_COLOR' => '#F5F5F5',
                'config_BACKGROUND_IMAGE' => '-',
                'config_MENU_BACKGROUND' => '#f8f9fa',
                'config_MENU_COLOR' => '#212121',
                'config_PRIMARY_COLOR' => '#007bff'
            ];

            $this->insert($obj, 'webstores_template_config');
        }
    }

}
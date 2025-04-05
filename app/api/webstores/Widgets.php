<?php

namespace app\api\webstores;

use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Widgets extends Model
{

    private $wid;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;
    }

    public function save()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        $unique = $_POST['unique'];
        $mode = isset($_POST['mode']) ? 1 : 0;

        unset($_POST['unique']);
        if($mode == 1)
        {
            unset($_POST['mode']);
        }

        $obj = [
            'widget_WID' => $this->wid,
            'widget_UNIQUE' => $unique,
            'widget_CONFIGURATION' => json_encode($_POST),
            'widget_ACTIVED' => $mode
        ];

        $has = $this->select("SELECT COUNT(*) as total FROM `webstores_widgets` WHERE `widget_WID`={$this->wid} AND `widget_UNIQUE`='{$unique}'")->total;

        if($has == 0)
        {
            $this->insert($obj, 'webstores_widgets');

            return Json::encode([ 'success' => true, 'message' => 'Widget adicionado' ]);
        }else{
            unset($obj['widget_WID']);
            unset($obj['widget_UNIQUE']);
            $this->update($obj, [ 'widget_WID' => $this->wid, 'widget_UNIQUE' => $unique ], 'webstores_widgets');

            return Json::encode([ 'success' => true, 'message' => 'Widget atualizado' ]);
        }
    }

    public function data()
    {
        return $this->select("SELECT * FROM `webstores_widgets` WHERE `widget_WID`={$this->wid}", 'all');
    }

    public function get($unique)
    {
        return $this->select("SELECT * FROM `webstores_widgets` WHERE `widget_WID`={$this->wid} AND `widget_UNIQUE`='{$unique}'");
    }

    public function has()
    {
        return $this->select("SELECT COUNT(*) as total FROM `webstores_widgets` WHERE `widget_WID`={$this->wid} AND `widget_ACTIVED`=1")->total > 0;
    }

}
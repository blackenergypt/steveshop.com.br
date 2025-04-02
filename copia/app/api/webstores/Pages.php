<?php

namespace app\api\webstores;

use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;

class Pages extends Model
{

    private $wid;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;
    }

    public function add()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(Forms::empty($_POST, [ 'title', 'link', 'content' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe todos os campos' ]);
        }
        if($this->has($_POST['link']))
        {
            return Json::encode([ 'success' => false, 'message' => 'Já existe uma página com este endereço' ]);
        }

        $_POST['link'] = str_replace(" ", '-', trim($_POST['link']));

        $obj = [
            'page_WID' => $this->wid,
            'page_TITLE' => trim($_POST['title']),
            'page_LINK' => $_POST['link'],
            'page_CONTENT' => $_POST['content'],
            'page_SHOW' => 1
        ];

        $this->insert($obj, 'webstores_pages');

        return Json::encode([ 'success' => true, 'message' => 'Página adicionada com sucesso' ]);
    }

    public function edit()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        if(Forms::empty($_POST, [ 'id', 'title', 'link', 'content' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe todos os campos' ]);
        }
        if($this->has($_POST['link'], $_POST['id']))
        {
            return Json::encode([ 'success' => false, 'message' => 'Já existe uma página com este endereço' ]);
        }

        $obj = [
            'page_TITLE' => trim($_POST['title']),
            'page_LINK' => $_POST['link'],
            'page_CONTENT' => $_POST['content'],
        ];

        $this->update($obj, [ 'page_WID' => $this->wid, 'page_ID' => $_POST['id'] ], 'webstores_pages');

        return Json::encode([ 'success' => true, 'message' => 'Página atualizada!' ]);
    }

    public function setShow()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }
        $id = $_POST['id'];
        $show = $_POST['mode'] == 'on' ? 1 : 0;

        $this->update([ 'page_SHOW' => $show ], [ 'page_ID' => $id ], 'webstores_pages');
    }

    public function has($page, $id = null)
    {
        if($id != null)
        {
            $select1 = $this->select("SELECT * FROM `webstores_pages` WHERE `page_ID`={$id}")->page_LINK == $page;

            if($select1) {
                return false;
            }

            return $this->select("SELECT COUNT(*) as total FROM `webstores_pages` WHERE `page_ID`={$id} AND `page_LINK`='{$page}'")->total > 0;
        }
        return $this->select("SELECT COUNT(*) as total FROM `webstores_pages` WHERE `page_WID`={$this->wid} AND `page_LINK`='{$page}'")->total > 0;
    }

    public function del()
    {
        if(!Security::ajax())
        {
            return Json::encode([ 'success' => false, 'message' => 'Blocked' ]);
        }

        $this->delete([ 'page_ID' => $_POST['id'], 'page_WID' => $this->wid ], 'webstores_pages');
    }

    public function getByID($id)
    {
        return $this->select("SELECT * FROM `webstores_pages` WHERE `page_ID`='{$id}'");
    }

    public function getByLink($link)
    {
        return $this->select("SELECT * FROM `webstores_pages` WHERE `page_WID`={$this->wid} AND `page_LINK`='{$link}'");
    }

    public function data()
    {
        return $this->select("SELECT * FROM `webstores_pages` WHERE `page_WID`={$this->wid}", 'all');
    }

}
<?php

namespace app\api\webstores;

use app\api\template\Template;
use app\lib\Forms;
use app\lib\Json;
use app\lib\Model;
use app\lib\Security;
use app\lib\Upload;

class SEO extends Model
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
        if(Forms::empty($_POST, [ 'name', 'title', 'keywords', 'description', 'welcome' ]))
        {
            return Json::encode([ 'success' => false, 'message' => 'Informe todos os dados' ]);
        }

        $logo = Upload::image('./cdn/images/', $_FILES['logo']);
        $favicon = Upload::image('./cdn/images/', $_FILES['favicon']);

        if($logo['status'] == false)
        {
            return Json::encode([ 'success' => false, 'message' => $logo['message'] ]);
        }

        if($favicon['status'] == false)
        {
            return Json::encode([ 'success' => false, 'message' => $favicon['message'] ]);
        }

        $data = $this->data();

        if($favicon['src'] == 'NULL')
        {
            $favicon['src'] = $data->seo_FAVICON;
        }

        if($logo['src'] == 'NULL')
        {
            $logo['src'] = $data->seo_LOGO;
        }

        $obj = [
            'seo_NAME' => $_POST['name'],
            'seo_TITLE' => $_POST['title'],
            'seo_KEYWORDS' => $_POST['keywords'],
            'seo_DESCRIPTION' => $_POST['description'],
            'seo_LOGO' => $logo['src'],
            'seo_FAVICON' => $favicon['src'],
            'seo_WELCOME' => $_POST['welcome']
        ];

        $this->update($obj, [ 'seo_WID' => $this->wid ], 'webstores_seo');

        return Json::encode([ 'success' => true, 'message' => 'Atualizado com sucesso!' ]);

    }

    public function data()
    {
        return $this->select("SELECT * FROM `webstores_seo` WHERE `seo_WID`={$this->wid}");
    }

    private function create()
    {
        $select = $this->select("SELECT COUNT(*) as total FROM `webstores_seo` WHERE `seo_WID`={$this->wid}")->total;

        if($select == 0)
        {
            $template = new Template($this->wid);

            $obj = [
                'seo_WID' => $this->wid,
                'seo_NAME' => $template->getSiteName(),
                'seo_TITLE' => $template->getSiteName(),
                'seo_KEYWORDS' => 'Minecraft, Minecraft Server, Servidor de Minecraft, Servidores de Minecraft, '.$template->getSiteName(),
                'seo_DESCRIPTION' => 'Loja do servidor '.$template->getSiteName(),
                'seo_LOGO' => '-',
                'seo_FAVICON' => '-',
                'seo_WELCOME' => 'Bem-vindo à sua nova loja. Você pode alterar a descrição desta página dentro de seu painel de controle.'
            ];

            $this->insert($obj, 'webstores_seo');
        }
    }

}
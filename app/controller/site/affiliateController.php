<?php

namespace app\controller\site;

use app\api\template\View;

class affiliateController {

    private $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function index()
    {
        // Definir o título da página e carregá-la
        $this->view->setTitle("Programa de Afiliados");
        
        // Verificar se o arquivo da view existe antes de renderizar
        $viewFile = APP_PATH . "app/view/site/affiliate/index.phtml";
        if (!file_exists($viewFile)) {
            error_log("Erro crítico: Arquivo da view não encontrado: {$viewFile}");
        }
        
        // Carregar a view usando o formato correto
        $this->view->render('site/affiliate/index');
    }

} 
<?php

namespace app\api\template;

/**
 * Classe responsável por renderizar as visualizações
 */
class View
{
    /**
     * Variáveis que serão passadas para a view
     * @var array
     */
    private $data = [];

    /**
     * Título da página
     * @var string
     */
    private $title = 'SteveShop';

    /**
     * Define um valor para ser acessado na view
     *
     * @param string $key Nome da variável
     * @param mixed $value Valor da variável
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Define o título da página
     *
     * @param string $title Título da página
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Obtém o título da página
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Renderiza uma view
     *
     * @param string $viewName Nome da view (ex: site/home/index)
     * @param array $data Dados adicionais para serem passados para a view
     * @return void
     */
    public function render($viewName, $data = [])
    {
        // Mescla os dados extras com os dados já definidos
        $this->data = array_merge($this->data, $data);
        
        // Extrai as variáveis para que possam ser usadas diretamente na view
        extract($this->data);
        
        // Define a variável de título
        $pageTitle = $this->title;
        
        // Arquivo de layout padrão
        $layoutFile = APP_PATH . "app/content/site/layouts/core.phtml";
        
        // Arquivo da view específica
        $viewFile = APP_PATH . "app/view/" . $viewName . ".phtml";
        
        // Verifica se a view existe
        if (!file_exists($viewFile)) {
            header('HTTP/1.0 404 Not Found');
            define('APP_ERROR', 'Não foi possível encontrar a view ' . $viewName);
            include(APP_PATH . "app/content/site/layouts/error.phtml");
            return;
        }
        
        // Se o layout existir, inclui ele com a view dentro
        if (file_exists($layoutFile)) {
            include($layoutFile);
        } else {
            // Se não, inclui apenas a view
            include($viewFile);
        }
    }
} 
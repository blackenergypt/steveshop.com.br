<?php

namespace app\lib;

/**
 * Classe para gerenciamento de traduções (i18n)
 */
class I18n {
    /**
     * Idioma atual
     * @var string
     */
    private static $currentLang = 'pt-br';
    
    /**
     * Traduções carregadas
     * @var array
     */
    private static $translations = [];
    
    /**
     * Idiomas suportados
     * @var array
     */
    private static $supportedLangs = ['pt-br', 'en', 'pt-pt'];
    
    /**
     * Nome do cookie para armazenar a preferência de idioma
     * @var string
     */
    private static $cookieName = 'steveshop_lang';
    
    /**
     * Duração do cookie em segundos (30 dias)
     * @var int
     */
    private static $cookieExpire = 2592000;
    
    /**
     * Flag para controlar se as traduções já foram carregadas
     * @var bool
     */
    private static $translationsLoaded = false;
    
    /**
     * Inicializa o sistema de traduções
     * 
     * @param string $lang Idioma a ser usado
     * @return void
     */
    public static function init($lang = null) {
        // Prioridade: 1. Parâmetro GET, 2. Cookie, 3. Padrão
        if ($lang !== null && in_array($lang, self::$supportedLangs)) {
            // Se o idioma foi passado por parâmetro e é suportado
            self::$currentLang = $lang;
            self::setLangCookie($lang);
        } else if (isset($_COOKIE[self::$cookieName]) && in_array($_COOKIE[self::$cookieName], self::$supportedLangs)) {
            // Se existe um cookie com idioma válido
            self::$currentLang = $_COOKIE[self::$cookieName];
        }
        
        // Carrega o arquivo de traduções do idioma atual
        self::loadTranslations();
    }
    
    /**
     * Carrega as traduções para o idioma atual
     * 
     * @return void
     */
    private static function loadTranslations() {
        if (self::$translationsLoaded) {
            return;
        }
        
        $langFile = APP_PATH . 'app/content/site/i18n/' . self::$currentLang . '.json';
        
        if (file_exists($langFile)) {
            $content = file_get_contents($langFile);
            self::$translations = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Se houver erro no JSON, inicializa um array vazio
                error_log("Erro ao carregar arquivo de tradução {$langFile}: " . json_last_error_msg());
                self::$translations = [];
            }
        } else {
            // Se o arquivo não existir, inicializa um array vazio
            error_log("Arquivo de tradução não encontrado: {$langFile}");
            self::$translations = [];
        }
        
        self::$translationsLoaded = true;
    }
    
    /**
     * Traduz uma chave para o idioma atual
     * 
     * @param string $key Chave a ser traduzida
     * @param array $params Parâmetros para substituir na tradução
     * @return string
     */
    public static function t($key, $params = []) {
        // Garantir que as traduções foram carregadas
        if (!self::$translationsLoaded) {
            self::loadTranslations();
        }
        
        // Se a chave existir diretamente nas traduções, retorna a tradução
        if (isset(self::$translations[$key])) {
            $text = self::$translations[$key];
            
            // Substitui os parâmetros por seus valores
            if (!empty($params)) {
                foreach ($params as $param => $value) {
                    $text = str_replace('{{'.$param.'}}', $value, $text);
                    $text = str_replace('{'.$param.'}', $value, $text);
                }
            }
            
            return $text;
        }
        
        // Se a chave não existir, retorna a própria chave
        error_log("Chave de tradução não encontrada: {$key} no idioma " . self::$currentLang);
        return $key;
    }
    
    /**
     * Retorna o idioma atual
     * 
     * @return string
     */
    public static function getCurrentLang() {
        return self::$currentLang;
    }
    
    /**
     * Define o idioma atual
     * 
     * @param string $lang Idioma a ser definido
     * @return bool
     */
    public static function setLang($lang) {
        if (in_array($lang, self::$supportedLangs)) {
            self::$currentLang = $lang;
            self::$translationsLoaded = false; // Reset para forçar o carregamento das novas traduções
            self::loadTranslations();
            self::setLangCookie($lang);
            return true;
        }
        
        return false;
    }
    
    /**
     * Define o cookie com a preferência de idioma
     * 
     * @param string $lang Idioma a ser armazenado no cookie
     * @return void
     */
    private static function setLangCookie($lang) {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $domain = defined('DOMAIN') ? '.' . DOMAIN : '';
        
        setcookie(
            self::$cookieName,
            $lang,
            time() + self::$cookieExpire,
            '/',
            $domain,
            $secure,
            true
        );
    }
    
    /**
     * Retorna a lista de idiomas suportados
     * 
     * @return array
     */
    public static function getSupportedLangs() {
        return self::$supportedLangs;
    }
} 
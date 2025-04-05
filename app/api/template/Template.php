<?php

namespace app\api\template;

use app\lib\Model;

class Template extends Model
{

    private $wid;
    protected $data;
    protected $template;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;

        $this->data = $this->select("SELECT * FROM `webstores` WHERE `webstore_ID`={$this->wid}");
        $this->template = $this->select("SELECT * FROM `webstore_template` WHERE `wid`={$this->wid}");

        $this->table();
        $this->populate();
    }

    public function getTemplate()
    {
        return $this->data->webstore_TEMPLATE;
    }

    public function getSiteName()
    {
        return $this->data->webstore_ID_NAME;
    }

    public function get($value) {
        $select = $this->select("SELECT * FROM `webstore_template` WHERE `wid`={$this->wid}");

        return $select->$value;
    }

    public function save($value, $content) {
        $this->update([
            $value => $content,
        ], [ 'wid' => $this->wid ], 'webstore_template');
    }

    private function populate() {
        if(!$this->template) {
            $page_home = addslashes(file_get_contents('./app/content/default_template/variables/page_home.txt'));
            $page_username = addslashes(file_get_contents('./app/content/default_template/variables/page_username.txt'));
            $page_products = addslashes(file_get_contents('./app/content/default_template/variables/page_products.txt'));
            $page_cart = addslashes(file_get_contents('./app/content/default_template/variables/page_cart.txt'));
            $page_empty_cart = addslashes(file_get_contents('./app/content/default_template/variables/page_empty_cart.txt'));
            $user_logged_component = addslashes(file_get_contents('./app/content/default_template/variables/user_logged_component.txt'));
            $header_component = addslashes(file_get_contents('./app/content/default_template/variables/header_component.txt'));
            $navbar_component = addslashes(file_get_contents('./app/content/default_template/variables/navbar_component.txt'));
            $footer_component = addslashes(file_get_contents('./app/content/default_template/variables/footer_component.txt'));
            $package_component = addslashes(file_get_contents('./app/content/default_template/variables/package_component.txt'));
            $styles = addslashes(file_get_contents('./app/content/default_template/variables/styles.txt'));

            $this->query("INSERT INTO `webstore_template`(`wid`, `page_home`, `page_username`, `page_products`, `page_cart`, `page_empty_cart`, `user_logged_component`, `header_component`, `navbar_component`, `footer_component`, `package_component`, `styles`, `scripts`) VALUES ({$this->wid}, '{$page_home}', '{$page_username}', '{$page_products}', '{$page_cart}', '{$page_empty_cart}', '{$user_logged_component}', '{$header_component}', '{$navbar_component}', '{$footer_component}', '{$package_component}', '{$styles}', 'var num = 1;')");
        }
    }

    public function reset() {
        $page_home = addslashes(file_get_contents('./app/content/default_template/variables/page_home.txt'));
        $page_username = addslashes(file_get_contents('./app/content/default_template/variables/page_username.txt'));
        $page_products = addslashes(file_get_contents('./app/content/default_template/variables/page_products.txt'));
        $page_cart = addslashes(file_get_contents('./app/content/default_template/variables/page_cart.txt'));
        $page_empty_cart = addslashes(file_get_contents('./app/content/default_template/variables/page_empty_cart.txt'));
        $user_logged_component = addslashes(file_get_contents('./app/content/default_template/variables/user_logged_component.txt'));
        $header_component = addslashes(file_get_contents('./app/content/default_template/variables/header_component.txt'));
        $navbar_component = addslashes(file_get_contents('./app/content/default_template/variables/navbar_component.txt'));
        $footer_component = addslashes(file_get_contents('./app/content/default_template/variables/footer_component.txt'));
        $package_component = addslashes(file_get_contents('./app/content/default_template/variables/package_component.txt'));
        $styles = addslashes(file_get_contents('./app/content/default_template/variables/styles.txt'));

        $this->update([
            'page_home' => $page_home,
            'page_username' => $page_username,
            'page_products' => $page_products,
            'page_cart' => $page_cart,
            'page_empty_cart' => $page_empty_cart,
            'user_logged_component' => $user_logged_component,
            'header_component' => $header_component,
            'navbar_component' => $navbar_component,
            'footer_component' => $footer_component,
            'package_component' => $package_component,
            'styles' => $styles
        ], [ 'wid' => $this->wid ], 'webstore_template');
    }

    private function table()
    {

        $sql = "CREATE TABLE IF NOT EXISTS `webstore_template` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `wid` INT(11) NOT NULL , `page_home` TEXT NOT NULL , `page_username` TEXT NOT NULL , `page_products` TEXT NOT NULL , `page_cart` TEXT NOT NULL , `page_empty_cart` TEXT NOT NULL , `user_logged_component` TEXT NOT NULL , `header_component` TEXT NOT NULL , `navbar_component` TEXT NOT NULL , `footer_component` TEXT NOT NULL , `package_component` TEXT NOT NULL , `styles` TEXT NOT NULL , `scripts` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
        $this->query($sql);
    }
}
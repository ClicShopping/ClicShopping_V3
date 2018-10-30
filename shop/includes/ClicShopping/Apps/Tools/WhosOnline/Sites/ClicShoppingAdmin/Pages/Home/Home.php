<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Tools\WhosOnline\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\WhosOnline\WhosOnline;

  use ClicShopping\Apps\Tools\WhosOnline\Classes\ClicShoppingAdmin\ShoppingCartAdmin;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_WhosOnline = new WhosOnline();
      Registry::set('WhosOnline', $CLICSHOPPING_WhosOnline);

      $this->app = Registry::get('WhosOnline');

      if (!Registry::exists('ShoppingCartAdmin')) {
        $CLICSHOPPING_ShoppingCartAdmin = new ShoppingCartAdmin();
        Registry::set('ShoppingCartAdmin', $CLICSHOPPING_ShoppingCartAdmin);
      }

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

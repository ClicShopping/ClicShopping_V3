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

  namespace ClicShopping\Apps\Marketing\Specials\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Specials\Specials;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_Specials = new Specials();
      Registry::set('Specials', $CLICSHOPPING_Specials);

      $this->app = Registry::get('Specials');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

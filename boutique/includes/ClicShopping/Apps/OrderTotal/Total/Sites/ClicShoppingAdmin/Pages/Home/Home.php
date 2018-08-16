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

  namespace ClicShopping\Apps\OrderTotal\Total\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\OrderTotal\Total\Total;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_Total = new Total();
      Registry::set('Total', $CLICSHOPPING_Total);

      $this->app = $CLICSHOPPING_Total;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

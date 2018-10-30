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

  namespace ClicShopping\Apps\OrderTotal\TotalTax\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\OrderTotal\TotalTax\TotalTax;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_TotalTax = new TotalTax();
      Registry::set('TotalTax', $CLICSHOPPING_TotalTax);

      $this->app = $CLICSHOPPING_TotalTax;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

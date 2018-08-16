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

  namespace ClicShopping\Apps\Report\StatsLowStock\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsLowStock\StatsLowStock;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_StatsLowStock = new StatsLowStock();
      Registry::set('StatsLowStock', $CLICSHOPPING_StatsLowStock);

      $this->app = Registry::get('StatsLowStock');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

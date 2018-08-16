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

  namespace ClicShopping\Apps\Report\StatsProductsViewed\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsProductsViewed\StatsProductsViewed;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_StatsProductsViewed = new StatsProductsViewed();
      Registry::set('StatsProductsViewed', $CLICSHOPPING_StatsProductsViewed);

      $this->app = Registry::get('StatsProductsViewed');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

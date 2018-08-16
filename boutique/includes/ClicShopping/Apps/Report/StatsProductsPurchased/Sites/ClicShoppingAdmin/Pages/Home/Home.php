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

  namespace ClicShopping\Apps\Report\StatsProductsPurchased\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsProductsPurchased\StatsProductsPurchased;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_StatsProductsPurchased = new StatsProductsPurchased();
      Registry::set('StatsProductsPurchased', $CLICSHOPPING_StatsProductsPurchased);

      $this->app = Registry::get('StatsProductsPurchased');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

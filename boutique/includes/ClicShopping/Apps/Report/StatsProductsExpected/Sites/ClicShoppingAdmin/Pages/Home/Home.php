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

  namespace ClicShopping\Apps\Report\StatsProductsExpected\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsProductsExpected\StatsProductsExpected;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_StatsProductsExpected = new StatsProductsExpected();
      Registry::set('StatsProductsExpected', $CLICSHOPPING_StatsProductsExpected);

      $this->app = Registry::get('StatsProductsExpected');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

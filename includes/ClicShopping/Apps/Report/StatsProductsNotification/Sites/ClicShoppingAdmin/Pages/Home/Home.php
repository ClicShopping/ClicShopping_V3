<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Report\StatsProductsNotification\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsProductsNotification\StatsProductsNotification;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_StatsProductsNotification = new StatsProductsNotification();
      Registry::set('StatsProductsNotification', $CLICSHOPPING_StatsProductsNotification);

      $this->app = Registry::get('StatsProductsNotification');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

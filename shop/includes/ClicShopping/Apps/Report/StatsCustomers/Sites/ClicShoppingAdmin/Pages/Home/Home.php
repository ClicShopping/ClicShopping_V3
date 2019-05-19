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

  namespace ClicShopping\Apps\Report\StatsCustomers\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsCustomers\StatsCustomers;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_StatsCustomers = new StatsCustomers();
      Registry::set('StatsCustomers', $CLICSHOPPING_StatsCustomers);

      $this->app = Registry::get('StatsCustomers');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

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

  namespace ClicShopping\Apps\OrderTotal\TotalShipping\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\OrderTotal\TotalShipping\TotalShipping;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_TotalShipping = new TotalShipping();
      Registry::set('TotalShipping', $CLICSHOPPING_TotalShipping);

      $this->app = $CLICSHOPPING_TotalShipping;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

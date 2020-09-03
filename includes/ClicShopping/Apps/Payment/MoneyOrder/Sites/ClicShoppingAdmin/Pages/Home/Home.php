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

  namespace ClicShopping\Apps\Payment\MoneyOrder\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\MoneyOrder\MoneyOrder;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_MoneyOrder = new MoneyOrder();
      Registry::set('MoneyOrder', $CLICSHOPPING_MoneyOrder);

      $this->app = $CLICSHOPPING_MoneyOrder;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

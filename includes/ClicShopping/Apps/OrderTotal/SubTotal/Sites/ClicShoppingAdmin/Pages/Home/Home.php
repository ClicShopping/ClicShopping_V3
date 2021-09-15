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

  namespace ClicShopping\Apps\OrderTotal\SubTotal\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\OrderTotal\SubTotal\SubTotal;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_SubTotal = new SubTotal();
      Registry::set('SubTotal', $CLICSHOPPING_SubTotal);

      $this->app = $CLICSHOPPING_SubTotal;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
